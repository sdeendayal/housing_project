<?php

namespace App\Http\Controllers;

use App\Models\EwsBuilderFlat;
use App\Models\EwsDeveloperLog;
use App\Models\EwsFlatAuditLog;
use App\Models\EwsProject;
use App\Models\EwsBlock;
use App\Models\EwsTown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\EwsHelper;

class EwsStpDashboardController extends Controller
{
    use \App\Traits\EwsBuilderFlatsMatrixTrait;
    private function ensureAuditTableExists(): void
    {
        try {
            if (!Schema::hasTable('ews_flat_audit_logs')) {
                Schema::create('ews_flat_audit_logs', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('flat_id')->index();
                    $table->string('flat_code', 100)->nullable()->index();
                    $table->unsignedBigInteger('user_id')->nullable()->index();
                    $table->string('user_name', 255)->nullable();
                    $table->string('user_role', 50)->nullable();
                    $table->string('action', 50)->default('UPDATED');
                    $table->text('summary')->nullable();
                    $table->json('changed_fields')->nullable();
                    $table->string('ip_address', 50)->nullable();
                    $table->text('user_agent')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            // Ignore if schema already exists
        }
    }

    private function ensureDtpApprovalColumnsExist(): void
    {
        try {
            if (Schema::hasTable('ews_builder_flats')) {
                Schema::table('ews_builder_flats', function (Blueprint $table) {
                    if (!Schema::hasColumn('ews_builder_flats', 'is_aproved_by_dtp')) {
                        $table->tinyInteger('is_aproved_by_dtp')->default(0)->index()->after('remaining_flats_for_oc');
                    }
                    if (!Schema::hasColumn('ews_builder_flats', 'is_approved_by_dtp')) {
                        $table->tinyInteger('is_approved_by_dtp')->default(0)->index()->after('is_aproved_by_dtp');
                    }
                    if (!Schema::hasColumn('ews_builder_flats', 'dtp_approved_at')) {
                        $table->timestamp('dtp_approved_at')->nullable()->after('is_approved_by_dtp');
                    }
                    if (!Schema::hasColumn('ews_builder_flats', 'dtp_approved_by')) {
                        $table->unsignedBigInteger('dtp_approved_by')->nullable()->after('dtp_approved_at');
                    }
                    if (!Schema::hasColumn('ews_builder_flats', 'dtp_user_name')) {
                        $table->string('dtp_user_name', 255)->nullable()->after('dtp_approved_by');
                    }
                    if (!Schema::hasColumn('ews_builder_flats', 'dtp_approval_remarks')) {
                        $table->text('dtp_approval_remarks')->nullable()->after('dtp_user_name');
                    }
                });
            }
        } catch (\Throwable $e) {
            // Ignore if schema already exists
        }
    }

    private function resolveDisplayZoneName($user)
    {
        $zone = null;
        if (!empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        if (!$zone && !empty($user->zone_name)) {
            $cleanZone = strtoupper(trim(str_ireplace(' ZONE', '', $user->zone_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanZone)->first();
        }
        if (!$zone && !empty($user->district_name)) {
            $cleanName = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanName)->first();
            if (!$zone) {
                $dist = DB::table('ews_districts')->where('name', $cleanName)->first();
                if ($dist && $dist->zone_id) {
                    $zone = DB::table('ews_stp_districts')->where('id', $dist->zone_id)->first();
                }
            }
        }
        if ($zone) {
            return str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE';
        }
        return !empty($user->zone_name) ? strtoupper($user->zone_name) : (!empty($user->district_name) ? strtoupper($user->district_name) : 'ZONE');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403, 'Unauthorized access to STP dashboard.');
        }

        if (empty($user->zone_id)) {
            $cleanName = strtoupper(trim(str_ireplace(' ZONE', '', $user->zone_name ?: $user->district_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanName)->first();
            if ($zone) {
                $user->zone_id = $zone->id;
            }
        }

        // Auto-heal any builder flats missing zone_id or zone_name from ews_districts
        try {
            DB::statement("
                UPDATE ews_builder_flats f
                JOIN ews_districts d ON (f.district_id = d.id OR f.dist_id = d.id OR UPPER(TRIM(f.district_name)) = UPPER(TRIM(d.name)))
                SET f.zone_id = d.zone_id,
                    f.district_id = COALESCE(f.district_id, d.id),
                    f.dist_id = COALESCE(f.dist_id, d.id),
                    f.district_name = COALESCE(f.district_name, d.name),
                    f.dist_name = COALESCE(f.dist_name, d.name)
                WHERE (f.zone_id IS NULL OR f.zone_id = 0) AND d.zone_id IS NOT NULL
            ");
            DB::statement("
                UPDATE ews_builder_flats f
                JOIN ews_stp_districts z ON f.zone_id = z.id
                SET f.zone_name = CONCAT(z.name, ' ZONE')
                WHERE f.zone_id IS NOT NULL AND (f.zone_name IS NULL OR f.zone_name = '')
            ");
        } catch (\Throwable $e) {}

        $displayZoneName = $this->resolveDisplayZoneName($user);

        $zoneDistrictIds = [];
        if (!empty($user->zone_id)) {
            $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $user->zone_id)->pluck('id')->toArray();
        }

        // Summary Statistics (Calculated in 1 fast indexed query strictly for this STP zone)
        $stats = $this->getMatrixSummaryStats($user->zone_id ?? null);

        // Calculate Physical Possession Total Allotted Flats strictly for this STP Zone
        $stats['total_allotted'] = 0;
        if (!empty($zoneDistrictIds)) {
            $projectsForStats = EwsProject::whereIn('district_id', $zoneDistrictIds)->get(['id', 'name', 'project_abbr']);
            $abbrs = [];
            foreach ($projectsForStats as $p) {
                $abbr = $p->project_abbr ?: DB::table('ews_flat_abbreviations')->where('project_name', $p->name)->value('project_abbr');
                if ($abbr) {
                    $abbrs[] = $abbr;
                }
            }
            $abbrs = array_values(array_unique($abbrs));
            if (!empty($abbrs)) {
                $stats['total_allotted'] = DB::table('ews_allotted_8')->where(function ($q) use ($abbrs) {
                    foreach ($abbrs as $a) {
                        $q->orWhere('flat_no', 'LIKE', "%-{$a}-%");
                    }
                })->count();
            }
        }

        // Cascading Filter Dropdown Masters (STRICTLY SCOPED TO THIS STP'S ZONE)
        $zonesList = DB::table('ews_stp_districts')->where('id', $user->zone_id)->get();
        $districts = DB::table('ews_districts')->where('zone_id', $user->zone_id)->orderBy('name')->get();

        $townsQuery = EwsTown::orderBy('name');
        if (!empty($zoneDistrictIds)) {
            $townsQuery->whereIn('district_id', $zoneDistrictIds);
        }
        $townsList = $townsQuery->get();

        $projectsQuery = EwsProject::orderBy('name');
        if (!empty($zoneDistrictIds)) {
            $projectsQuery->whereIn('district_id', $zoneDistrictIds);
        }
        $projectsList = $projectsQuery->get(['id', 'name', 'license_no']);

        $licensesList = $this->getMatrixLicensesList($user->zone_id ?? null);

        $currentView = $request->query('view', 'dashboard');

        return view('ews.stp.dashboard', compact(
            'user',
            'stats',
            'zonesList',
            'districts',
            'projectsList',
            'townsList',
            'licensesList',
            'displayZoneName',
            'currentView'
        ));
    }

    /**
     * Helper to get registry query with applied search & district filters.
     */
    private function getFilteredQuery(Request $request)
    {
        $query = EwsBuilderFlat::query();
        $user = Auth::user();

        // 1. Ownership Scope Filter (My Flats vs All Zone Flats)
        if ($request->input('ownership_scope') === 'my_flats' || $request->input('my_flats') == '1') {
            $query->where('created_by', $user->id);
        } else {
            // Lock flats data strictly to developer's assigned zone
            if (!empty($user->zone_id)) {
                $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $user->zone_id)->pluck('id')->toArray();
                $query->where(function ($q) use ($user, $zoneDistrictIds) {
                    $q->where('zone_id', $user->zone_id);
                    if (!empty($zoneDistrictIds)) {
                        $q->orWhereIn('district_id', $zoneDistrictIds);
                    }
                });
            } elseif ($user && !empty($user->district_name)) {
                $userDist = strtoupper(trim($user->district_name));
                $query->where(function ($q) use ($user, $userDist) {
                    $q->where('district_name', $userDist)
                      ->orWhere('district_name', $user->district_name);
                    if (!empty($user->district_id)) {
                        $q->orWhere('district_id', $user->district_id);
                    }
                });
            }
        }

        // 2. Specific District Filter
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        // 3. Specific Town Filter
        if ($request->filled('town_id')) {
            $query->where('town_id', $request->town_id);
        }

        // 4. Specific Project Filter
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // 5. Specific License Number Filter
        if ($request->filled('license_no')) {
            $query->where('license_no', trim($request->license_no));
        }

        // 6. DTP Approval Filter (Approved / Pending)
        if ($request->input('ownership_scope') === 'dtp_approved' || $request->input('dtp_filter') === 'approved') {
            $query->where('is_aproved_by_dtp', 1);
        } elseif ($request->input('ownership_scope') === 'dtp_pending' || $request->input('dtp_filter') === 'pending') {
            $query->where(function($q) {
                $q->whereNull('is_aproved_by_dtp')->orWhere('is_aproved_by_dtp', 0);
            });
        }

        // Search Filter (Standard string parameter or Yajra request array)
        $searchValue = '';
        if ($request->has('search')) {
            $searchParam = $request->search;
            if (is_array($searchParam)) {
                $searchValue = $searchParam['value'] ?? '';
            } else {
                $searchValue = $searchParam;
            }
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('district_name', 'like', "%{$searchValue}%")
                  ->orWhere('town_name', 'like', "%{$searchValue}%")
                  ->orWhere('project_name', 'like', "%{$searchValue}%")
                  ->orWhere('license_no', 'like', "%{$searchValue}%")
                  ->orWhere('block_tower_number', 'like', "%{$searchValue}%")
                  ->orWhere('floor', 'like', "%{$searchValue}%")
                  ->orWhere('flat_number', 'like', "%{$searchValue}%")
                  ->orWhere('flat_code', 'like', "%{$searchValue}%");
            });
        }

        return $query->orderBy('id', 'desc');
    }

    public function getFlatsData(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403);
        }

        // Always lock to STP assigned zone
        if (!empty($user->zone_id)) {
            $request->merge(['zone_id' => $user->zone_id]);
        }

        return $this->buildMatrixDataTable($request);
    }

    public function getDistricts(Request $request)
    {
        $user = Auth::user();
        $zoneId = !empty($user->zone_id) ? $user->zone_id : $request->input('zone_id');
        $query = DB::table('ews_districts')->orderBy('name', 'asc');
        if (!empty($zoneId)) {
            $query->where('zone_id', $zoneId);
        }
        return response()->json($query->get(['id', 'name', 'zone_id']));
    }

    public function getLicenses(Request $request)
    {
        $user = Auth::user();
        $zoneId = !empty($user->zone_id) ? $user->zone_id : $request->input('zone_id');
        $licenses = $this->getMatrixLicensesList(
            $zoneId,
            $request->input('district_id'),
            $request->input('town_id'),
            $request->input('project_id')
        );
        return response()->json($licenses);
    }

    public function exportFlats($format, Request $request)
    {
        $user = Auth::user();
        if (!$request->filled('zone_id') && !empty($user->zone_id)) {
            $request->merge(['zone_id' => $user->zone_id]);
        }
        return $this->exportMatrixFlats($format, $request);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403);
        }

        // 1. Resolve Zone directly from ews_stp_districts master table
        $zone = null;
        if (!empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        if (!$zone && !empty($user->zone_name)) {
            $cleanZoneName = strtoupper(trim(str_replace(' ZONE', '', $user->zone_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanZoneName)->first();
        }
        if (!$zone && !empty($user->district_name)) {
            $cleanName = strtoupper(trim(str_replace(' ZONE', '', $user->district_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanName)->first();
            if (!$zone) {
                $dist = DB::table('ews_districts')->where('name', $cleanName)->first();
                if ($dist && $dist->zone_id) {
                    $zone = DB::table('ews_stp_districts')->where('id', $dist->zone_id)->first();
                }
            }
        }

        // 2. Fetch ONLY districts that belong to this STP's Zone!
        if ($zone) {
            $districts = DB::table('ews_districts')
                ->where('zone_id', $zone->id)
                ->orderBy('name', 'asc')
                ->get();
        } else {
            $districts = DB::table('ews_districts')->orderBy('name', 'asc')->get();
        }

        // Do not auto-preselect district; user must select everything manually while zone remains frozen
        $selectedDistrictId = null;
        $towns = collect();
        $projects = collect();
        $townTypes = EwsTown::whereNotNull('type')->where('type', '!=', '')->distinct()->pluck('type')->sort()->values();
        if ($townTypes->isEmpty()) {
            $townTypes = collect(['Municipal Corporation', 'Municipal Council', 'Municipal Committee']);
        }
        $displayZoneName = $this->resolveDisplayZoneName($user);

        return view('ews.stp.create', compact('user', 'zone', 'districts', 'towns', 'projects', 'townTypes', 'selectedDistrictId', 'displayZoneName'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403);
        }

        // Validate basic parameters
        $request->validate([
            'register_flat' => 'nullable|string|max:255',
            'district_id' => 'required|exists:ews_districts,id',
            'town_id' => 'required',
            'new_town_name' => 'required_if:town_id,new|nullable|string|max:255',
            'new_town_type' => 'required_if:town_id,new|nullable|string|max:255',
            'custom_town_type' => 'required_if:new_town_type,other|nullable|string|max:255',
            'project_id' => 'required',
            'new_project_name' => 'required_if:project_id,new|nullable|string|max:255',
            'block_id' => 'required',
            'new_block_name' => 'required_if:block_id,new|nullable|string|max:255',
        ]);

        $district = DB::table('ews_districts')->where('id', $request->district_id)->first();
        if (!$district) {
            return back()->withInput()->with('error', "Invalid district selected.");
        }

        // Resolve Zone
        $zone = null;
        if ($district->zone_id) {
            $zone = DB::table('ews_stp_districts')->where('id', $district->zone_id)->first();
        }
        if (!$zone && !empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        $zoneId = $zone ? $zone->id : ($district->zone_id ?? $user->zone_id);
        $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : (!empty($user->zone_name) ? strtoupper($user->zone_name) : ($district ? strtoupper($district->name) . ' ZONE' : 'ZONE'));

        // Resolve Town ID and Name from master ews_towns table
        if ($request->town_id === 'new') {
            $cleanTownName = trim($request->new_town_name);
            $townType = $request->new_town_type === 'other' ? trim($request->custom_town_type ?? '') : trim($request->new_town_type ?? '');

            $existingTowns = EwsTown::where('district_id', $district->id)->get();
            $similarTown = EwsHelper::findSimilarName($cleanTownName, $existingTowns);
            if ($similarTown) {
                return back()->withInput()->with('error', "Validation Error: Town '{$cleanTownName}' already exists or is too similar to existing town '{$similarTown['existing']}' ({$similarTown['reason']}). Please select it from the list instead of adding a new one.");
            }

            $town = EwsTown::create([
                'district_id' => $district->id,
                'zone_id'     => $zoneId,
                'zone_name'   => $zoneName,
                'name'        => $cleanTownName,
                'type'        => !empty($townType) ? $townType : null,
            ]);
        } else {
            $town = EwsTown::where('district_id', $district->id)->where('id', $request->town_id)->first();
            if (!$town) {
                $town = EwsTown::find($request->town_id);
            }
            if (!$town) {
                return back()->withInput()->with('error', "Validation Error: The selected town is not registered under {$district->name}.");
            }
        }
        $townId = $town->id;
        $townName = $town->name;

        // Resolve Project ID and Name
        if ($request->project_id === 'new') {
            $cleanProjName = trim($request->new_project_name);
            $projectQuery = EwsProject::where('district_id', $district->id);
            if ($zoneId) {
                $projectQuery->where('zone_id', $zoneId);
            }
            if ($townId) {
                $projectQuery->where(function($q) use ($townId) {
                    $q->where('town_id', $townId)->orWhereNull('town_id');
                });
            }
            $existingProjects = $projectQuery->get();
            $similarProject = EwsHelper::findSimilarName($cleanProjName, $existingProjects);
            if ($similarProject) {
                return back()->withInput()->with('error', "Validation Error: Project '{$cleanProjName}' already exists or is too similar to existing project '{$similarProject['existing']}' ({$similarProject['reason']}). Please select it from the list instead of adding it as a new project.");
            }

            $project = EwsProject::create([
                'zone_id' => $zoneId,
                'zone_name' => $zoneName,
                'district_id' => $district->id,
                'district_name' => $district->name,
                'town_id' => $townId,
                'town_name' => $townName,
                'name' => $cleanProjName,
            ]);
            $projectId = $project->id;
            $projectName = $project->name;
        } else {
            $project = EwsProject::where('district_id', $district->id)->where('id', $request->project_id)->firstOrFail();
            if (empty($project->town_id) && $townId) {
                $project->update([
                    'zone_id' => $project->zone_id ?? $zoneId,
                    'zone_name' => $project->zone_name ?? $zoneName,
                    'district_name' => $project->district_name ?? $district->name,
                    'town_id' => $townId,
                    'town_name' => $townName,
                ]);
            }
            $projectId = $project->id;
            $projectName = $project->name;
        }

        // Resolve Block ID and Name
        if ($request->block_id === 'new') {
            $cleanBlockName = trim($request->new_block_name);
            $existingBlocks = EwsBlock::where('project_id', $projectId)
                ->where(function($q) use ($townId) {
                    $q->where('town_id', $townId)->orWhereNull('town_id');
                })->get();
            $similarBlock = EwsHelper::findSimilarName($cleanBlockName, $existingBlocks);
            if ($similarBlock) {
                return back()->withInput()->with('error', "Validation Error: Block/Tower '{$cleanBlockName}' already exists or is too similar to existing '{$similarBlock['existing']}' ({$similarBlock['reason']}) under the selected project. Please select it from the list.");
            }

            $block = EwsBlock::create([
                'zone_id'       => $zoneId,
                'zone_name'     => $zoneName,
                'district_id'   => $district->id,
                'district_name' => $district->name,
                'town_id'       => $townId,
                'town_name'     => $townName,
                'project_id'    => $projectId,
                'project_name'  => $projectName,
                'name'          => $cleanBlockName,
            ]);
            $blockId = $block->id;
            $blockName = $block->name;
        } else {
            $block = EwsBlock::where('project_id', $projectId)->where('id', $request->block_id)->firstOrFail();
            if ((empty($block->town_id) && $townId) || empty($block->zone_id)) {
                $block->update([
                    'zone_id'       => $block->zone_id ?? $zoneId,
                    'zone_name'     => $block->zone_name ?? $zoneName,
                    'district_id'   => $block->district_id ?? $district->id,
                    'district_name' => $block->district_name ?? $district->name,
                    'town_id'       => $block->town_id ?? $townId,
                    'town_name'     => $block->town_name ?? $townName,
                    'project_name'  => $block->project_name ?? $projectName,
                ]);
            }
            $blockId = $block->id;
            $blockName = $block->name;
        }

        // Resolve Zone directly from ews_stp_districts master table
        $zone = null;
        if (!empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        if (!$zone && !empty($user->district_name)) {
            $cleanZoneName = strtoupper(trim(str_replace(' ZONE', '', $user->district_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanZoneName)->first();
        }
        if (!$zone && $district) {
            $cleanDist = strtoupper(trim(str_replace(' ZONE', '', $district->name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanDist)->first();
        }
        $zoneId = $zone ? $zone->id : ($user->zone_id ?? null);
        $zoneName = $zone ? $zone->name . ' ZONE' : ($user->zone_name ?? ($district ? $district->name . ' ZONE' : 'N/A'));

        // Bulk Mode Generation
        if ($request->input('bulk_mode') == '1') {
            $request->validate([
                'floor_number' => 'required|integer|min:0|max:100',
                'flat_number_type' => 'required|in:range,custom',
                'from_flat' => 'required_if:flat_number_type,range|nullable|integer|min:1',
                'to_flat' => 'required_if:flat_number_type,range|nullable|integer|min:1|gte:from_flat',
                'custom_flat_numbers' => [
                    'required_if:flat_number_type,custom',
                    'nullable',
                    'string',
                    'regex:/^[0-9,\s]*$/'
                ],
            ], [
                'custom_flat_numbers.regex' => 'The flat numbers list must only contain numbers, commas, and spaces.',
            ]);

            if ($request->flat_number_type === 'custom') {
                if ($request->filled('from_flat') || $request->filled('to_flat')) {
                    return back()->withInput()->with('error', "Validation Error: Range fields must be empty when selecting Custom List.");
                }
            } else {
                if ($request->filled('custom_flat_numbers')) {
                    return back()->withInput()->with('error', "Validation Error: Custom list field must be empty when selecting Numerical Range.");
                }
            }

            $floorNum = (int)$request->floor_number;
            if ($request->flat_number_type === 'range') {
                $flatNumbers = range((int)$request->from_flat, (int)$request->to_flat);
            } else {
                $flatNumbers = array_filter(array_map('trim', explode(',', $request->custom_flat_numbers)));
            }

            if (empty($flatNumbers)) {
                return back()->withInput()->with('error', "Validation Error: No valid flat numbers provided.");
            }

            // Existing Flats in Project and Block to prevent duplicates
            $existingFlats = EwsBuilderFlat::where('district_id', $district->id)
                ->where('town_name', $townName)
                ->where('project_name', $projectName)
                ->where('block_tower_number', $blockName)
                ->get(['floor', 'flat_number'])
                ->groupBy('floor')
                ->map(function ($items) {
                    return $items->pluck('flat_number')->toArray();
                })
                ->toArray();

            $createdCount = 0;
            DB::beginTransaction();
            try {
                if ($floorNum === 0) {
                    $floorLabel = "Ground Floor";
                } elseif ($floorNum === 1) {
                    $floorLabel = "First Floor";
                } elseif ($floorNum === 2) {
                    $floorLabel = "Second Floor";
                } elseif ($floorNum === 3) {
                    $floorLabel = "Third Floor";
                } else {
                    $floorLabel = "{$floorNum}th Floor";
                }

                foreach ($flatNumbers as $flatSeq) {
                    if ($request->input('floor_prefix_enabled') == '1') {
                        if ($floorNum === 0) {
                            $flatNumberStr = str_pad($flatSeq, 2, '0', STR_PAD_LEFT);
                        } else {
                            $flatNumberStr = $floorNum . str_pad($flatSeq, 2, '0', STR_PAD_LEFT);
                        }
                    } else {
                        $flatNumberStr = (string)$flatSeq;
                    }

                    // Check if already registered
                    if (isset($existingFlats[$floorLabel]) && in_array($flatNumberStr, $existingFlats[$floorLabel])) {
                        throw new \Exception("Flat '{$flatNumberStr}' on '{$floorLabel}' is already registered under Project '{$projectName}' Block '{$blockName}'.");
                    }

                    $flatData = [
                        'zone_id' => $zoneId,
                        'zone_name' => $zoneName,
                        'district_id' => $district->id,
                        'dist_id' => $district->id,
                        'district_name' => $district->name,
                        'dist_name' => $district->name,
                        'town_name' => $townName,
                        'town_id' => $townId,
                        'project_name' => $projectName,
                        'project_id' => $projectId,
                        'block_tower_number' => $blockName,
                        'block_id' => $blockId,
                        'floor' => $floorLabel,
                        'flat_number' => $flatNumberStr,
                        'created_by' => $user->id,
                        'secure_id' => md5(uniqid("flat_" . microtime() . rand(), true)),
                        'flat_code' => EwsHelper::generateFlatCode(
                            $townName,
                            $user->name,
                            $floorLabel,
                            $blockName,
                            $flatNumberStr
                        )
                    ];

                    EwsBuilderFlat::create($flatData);
                    $createdCount++;
                }

                EwsDeveloperLog::create([
                    'user_id' => $user->id,
                    'action' => 'CREATED_BULK',
                    'details' => "Bulk Registered {$createdCount} EWS Flats under Tower: {$blockName}, Project: '{$projectName}' in {$townName} (Floor: {$floorLabel})",
                    'ip_address' => $request->ip(),
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('error', "Validation Error: " . $e->getMessage());
            }

            return redirect()->route('ews.stp.dashboard')->with('success', "Bulk Registry successfully generated and added {$createdCount} EWS flats.");
        }

        // Single Mode Generation
        $request->validate([
            'floor_number' => 'required|integer|min:0|max:100',
            'flat_number' => 'required|string|max:255',
        ]);

        $floorNum = (int)$request->floor_number;
        if ($floorNum === 0) {
            $floorLabel = "Ground Floor";
        } elseif ($floorNum === 1) {
            $floorLabel = "First Floor";
        } elseif ($floorNum === 2) {
            $floorLabel = "Second Floor";
        } elseif ($floorNum === 3) {
            $floorLabel = "Third Floor";
        } else {
            $floorLabel = "{$floorNum}th Floor";
        }

        // Check if flat is already registered (Single Mode)
        $existsSingle = EwsBuilderFlat::where('district_id', $district->id)
            ->where('town_name', $townName)
            ->where('project_name', $projectName)
            ->where('block_tower_number', $blockName)
            ->where('floor', $floorLabel)
            ->where('flat_number', $request->flat_number)
            ->exists();

        if ($existsSingle) {
            return back()->withInput()->with('error', "Validation Error: EWS Flat '{$request->flat_number}' on Floor '{$floorLabel}' in Block '{$blockName}' of Project '{$projectName}' is already registered.");
        }

        $flat = EwsBuilderFlat::create([
            'zone_id' => $zoneId,
            'zone_name' => $zoneName,
            'district_id' => $district->id,
            'dist_id' => $district->id,
            'district_name' => $district->name,
            'dist_name' => $district->name,
            'town_name' => $townName,
            'town_id' => $townId,
            'project_name' => $projectName,
            'project_id' => $projectId,
            'block_tower_number' => $blockName,
            'block_id' => $blockId,
            'floor' => $floorLabel,
            'flat_number' => $request->flat_number,
            'created_by' => $user->id,
            'secure_id' => md5(uniqid("flat_" . microtime() . rand(), true)),
            'flat_code' => EwsHelper::generateFlatCode(
                $townName,
                $user->name,
                $floorLabel,
                $blockName,
                $request->flat_number
            )
        ]);

        // Create log entry
        EwsDeveloperLog::create([
            'user_id' => $user->id,
            'action' => 'CREATED',
            'details' => "Added EWS Flat: {$flat->flat_number}, Floor: {$flat->floor}, Tower: {$flat->block_tower_number} under Project '{$flat->project_name}' in {$flat->town_name} ({$flat->district_name})",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('ews.stp.dashboard')->with('success', 'EWS Builder Flat record created successfully.');
    }

    /**
     * Show Comprehensive Dossier for Single EWS Builder Flat
     */
    public function show($secureId = null)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403, 'Unauthorized access to STP dossier.');
        }

        $id = $secureId ?: request('id');
        if (empty($id)) {
            abort(404, 'Flat identifier is required.');
        }

        $this->ensureAuditTableExists();

        // Support lookup by integer ID, secure_id, or decoded secure ID
        $flat = EwsBuilderFlat::where('id', $id)
            ->orWhere('secure_id', $id)
            ->first();

        if (!$flat && is_numeric(EwsHelper::decodeSecureId($id))) {
            $flat = EwsBuilderFlat::find(EwsHelper::decodeSecureId($id));
        }

        if (!$flat) {
            abort(404, 'Flat dossier record not found.');
        }

        // Zone-wise access check
        if (!empty($user->zone_id)) {
            $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $user->zone_id)->pluck('id')->toArray();
            $allowed = ($flat->zone_id == $user->zone_id) || in_array($flat->district_id, $zoneDistrictIds) || ($flat->created_by == $user->id);
            if (!$allowed) {
                $userZone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->value('name');
                if (!empty($userZone) && !empty($flat->zone_name) && strcasecmp(trim(str_replace(' ZONE', '', $userZone)), trim(str_replace(' ZONE', '', $flat->zone_name))) !== 0) {
                    abort(403, 'Unauthorized action for this zone.');
                }
            }
        }

        $project = null;
        if (!empty($flat->project_id)) {
            $project = EwsProject::find($flat->project_id);
        }
        if (!$project && !empty($flat->project_name)) {
            $project = EwsProject::where('name', $flat->project_name)->first();
        }

        $displayZoneName = $this->resolveDisplayZoneName($user);

        // Fetch Audit Logs for this flat
        $auditLogs = collect();
        if (Schema::hasTable('ews_flat_audit_logs')) {
            $auditLogs = EwsFlatAuditLog::where('flat_id', $flat->id)
                ->orWhere('flat_code', $flat->flat_code)
                ->orderBy('id', 'desc')
                ->get();
        }

        $creator = null;
        if (!empty($flat->created_by)) {
            $creator = \App\Models\User::find($flat->created_by);
        }

        $backUrl = route('ews.stp.dashboard');
        $stats = $this->getMatrixSummaryStats();

        return view('ews.tcp.flat_view', compact('user', 'flat', 'project', 'creator', 'backUrl', 'stats', 'auditLogs', 'displayZoneName'));
    }

    public function edit($secureId = null)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403);
        }

        $id = $secureId ?: request('id');
        if (empty($id)) {
            abort(404, 'Flat identifier is required.');
        }

        // Support lookup by integer ID, secure_id, or decoded secure ID
        $flat = EwsBuilderFlat::where('id', $id)
            ->orWhere('secure_id', $id)
            ->first();

        if (!$flat && is_numeric(EwsHelper::decodeSecureId($id))) {
            $flat = EwsBuilderFlat::find(EwsHelper::decodeSecureId($id));
        }

        if (!$flat) {
            abort(404, 'Flat record not found.');
        }

        // Resolve user's zone if set as zone_id or zone_name/district_name
        $userZoneId = $user->zone_id;
        if (!$userZoneId && !empty($user->zone_name)) {
            $cleanUserZone = strtoupper(trim(str_ireplace(' ZONE', '', $user->zone_name)));
            $stpZone = DB::table('ews_stp_districts')->where('name', $cleanUserZone)->first();
            if ($stpZone) {
                $userZoneId = $stpZone->id;
            }
        }
        if (!$userZoneId && !empty($user->district_name)) {
            $cleanUserDist = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
            $stpZone = DB::table('ews_stp_districts')->where('name', $cleanUserDist)->first();
            if ($stpZone) {
                $userZoneId = $stpZone->id;
            } else {
                $dist = DB::table('ews_districts')->where('name', $cleanUserDist)->first();
                if ($dist && $dist->zone_id) {
                    $userZoneId = $dist->zone_id;
                }
            }
        }
        if (!$userZoneId) {
            $displayZone = $this->resolveDisplayZoneName($user);
            $cleanDisplayZone = strtoupper(trim(str_ireplace(' ZONE', '', $displayZone)));
            $stpZone = DB::table('ews_stp_districts')->where('name', $cleanDisplayZone)->first();
            if ($stpZone) {
                $userZoneId = $stpZone->id;
            }
        }

        // Resolve flat's district and zone if missing
        if ((empty($flat->district_id) || empty($flat->zone_id)) && !empty($flat->district_name)) {
            $matchedDist = DB::table('ews_districts')->where('name', strtoupper(trim($flat->district_name)))->first();
            if ($matchedDist) {
                $flat->district_id = $flat->district_id ?: $matchedDist->id;
                $flat->zone_id = $flat->zone_id ?: $matchedDist->zone_id;
            }
        }

        // Zone-wise edit authorization check
        $allowed = false;
        if (!empty($userZoneId)) {
            $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $userZoneId)->pluck('id')->toArray();
            $allowed = ($flat->zone_id == $userZoneId) || in_array($flat->district_id, $zoneDistrictIds) || ($flat->created_by == $user->id);
            if (!$allowed && $flat->district_id) {
                $flatDistZone = DB::table('ews_districts')->where('id', $flat->district_id)->value('zone_id');
                if ($flatDistZone && $flatDistZone == $userZoneId) {
                    $allowed = true;
                }
            }
            if (!$allowed && !empty($flat->district_name)) {
                $flatDistZone = DB::table('ews_districts')->where('name', strtoupper(trim($flat->district_name)))->value('zone_id');
                if ($flatDistZone && $flatDistZone == $userZoneId) {
                    $allowed = true;
                }
            }
            if (!$allowed && !empty($flat->zone_name)) {
                $userZoneName = DB::table('ews_stp_districts')->where('id', $userZoneId)->value('name');
                if ($userZoneName && strcasecmp(trim(str_ireplace(' ZONE', '', $userZoneName)), trim(str_ireplace(' ZONE', '', $flat->zone_name))) === 0) {
                    $allowed = true;
                }
            }
        } else {
            $allowed = ($flat->created_by == $user->id) || ($user->district_id == $flat->district_id);
            if (!$allowed && !empty($user->district_name) && !empty($flat->district_name)) {
                $allowed = (strcasecmp(trim($user->district_name), trim($flat->district_name)) === 0);
            }
        }

        if (!$allowed) {
            abort(403, 'Unauthorized action for this flat record.');
        }

        // Fetch districts accessible to this user
        if (!empty($userZoneId)) {
            $districts = DB::table('ews_districts')
                ->where('zone_id', $userZoneId)
                ->orderBy('name', 'asc')
                ->get();
        } elseif (!empty($user->district_name)) {
            $userDist = strtoupper(trim($user->district_name));
            $districts = DB::table('ews_districts')
                ->where('name', $userDist)
                ->orWhere('id', $user->district_id)
                ->orderBy('name', 'asc')
                ->get();
        } else {
            $districts = DB::table('ews_districts')->orderBy('name', 'asc')->get();
        }

        if ($districts->isEmpty()) {
            $districts = DB::table('ews_districts')->orderBy('name', 'asc')->get();
        }

        // Fetch towns for the flat's district
        $towns = EwsTown::where('district_id', $flat->district_id)
            ->orderBy('name', 'asc')
            ->get();
        if (!$flat->town_id && !empty($flat->town_name)) {
            $matchingTown = $towns->first(function($t) use ($flat) {
                return strcasecmp(trim($t->name), trim($flat->town_name)) === 0;
            });
            if ($matchingTown) {
                $flat->town_id = $matchingTown->id;
            }
        }

        // Fetch projects for the flat's district and town
        $projectsQuery = EwsProject::where('district_id', $flat->district_id);
        if ($flat->town_id) {
            $projectsQuery->where(function ($q) use ($flat) {
                $q->where('town_id', $flat->town_id);
                if ($flat->project_id) {
                    $q->orWhere('id', $flat->project_id);
                }
            });
        }
        $projects = $projectsQuery->orderBy('name', 'asc')->get();
        if (!$flat->project_id && !empty($flat->project_name)) {
            $matchingProj = $projects->first(function($p) use ($flat) {
                return strcasecmp(trim($p->name), trim($flat->project_name)) === 0;
            });
            if ($matchingProj) {
                $flat->project_id = $matchingProj->id;
            }
        }

        $project = null;
        if (!empty($flat->project_id)) {
            $project = EwsProject::find($flat->project_id);
        }
        if (!$project && !empty($flat->project_name)) {
            $project = EwsProject::where('name', $flat->project_name)->first();
        }

        // Fetch blocks for the flat's project
        $blocks = collect();
        if ($flat->project_id) {
            $blocks = EwsBlock::where('project_id', $flat->project_id)
                ->orderBy('name', 'asc')
                ->get();
            if (!$flat->block_id && !empty($flat->block_tower_number)) {
                $matchingBlock = $blocks->first(function($b) use ($flat) {
                    return strcasecmp(trim($b->name), trim($flat->block_tower_number)) === 0;
                });
                if ($matchingBlock) {
                    $flat->block_id = $matchingBlock->id;
                }
            }
        }

        $zone = null;
        if (!empty($flat->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $flat->zone_id)->first();
        } elseif (!empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        if (!$zone && !empty($user->district_name)) {
            $cleanZoneName = strtoupper(trim(str_replace(' ZONE', '', $user->district_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanZoneName)->first();
        }
        if (!$zone && $flat->district_name) {
            $cleanDist = strtoupper(trim(str_replace(' ZONE', '', $flat->district_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanDist)->first();
        }

        $townTypes = EwsTown::whereNotNull('type')->where('type', '!=', '')->distinct()->pluck('type')->sort()->values();
        if ($townTypes->isEmpty()) {
            $townTypes = collect(['Municipal Corporation', 'Municipal Council', 'Municipal Committee']);
        }
        $displayZoneName = $this->resolveDisplayZoneName($user);

        return view('ews.stp.edit', compact('user', 'flat', 'project', 'zone', 'districts', 'secureId', 'towns', 'townTypes', 'projects', 'blocks', 'displayZoneName'));
    }

    public function update(Request $request, $secureId = null)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403);
        }

        $id = $secureId ?: $request->input('id');
        if (empty($id)) {
            abort(404, 'Flat identifier is required.');
        }

        $flat = EwsBuilderFlat::where('id', $id)
            ->orWhere('secure_id', $id)
            ->first();

        if (!$flat && is_numeric(EwsHelper::decodeSecureId($id))) {
            $flat = EwsBuilderFlat::find(EwsHelper::decodeSecureId($id));
        }

        if (!$flat) {
            abort(404, 'Flat not found');
        }

        // Resolve user's zone if set as zone_id or zone_name/district_name
        $userZoneId = $user->zone_id;
        if (!$userZoneId && !empty($user->zone_name)) {
            $cleanUserZone = strtoupper(trim(str_ireplace(' ZONE', '', $user->zone_name)));
            $stpZone = DB::table('ews_stp_districts')->where('name', $cleanUserZone)->first();
            if ($stpZone) {
                $userZoneId = $stpZone->id;
            }
        }
        if (!$userZoneId && !empty($user->district_name)) {
            $cleanUserDist = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
            $stpZone = DB::table('ews_stp_districts')->where('name', $cleanUserDist)->first();
            if ($stpZone) {
                $userZoneId = $stpZone->id;
            } else {
                $dist = DB::table('ews_districts')->where('name', $cleanUserDist)->first();
                if ($dist && $dist->zone_id) {
                    $userZoneId = $dist->zone_id;
                }
            }
        }
        if (!$userZoneId) {
            $displayZone = $this->resolveDisplayZoneName($user);
            $cleanDisplayZone = strtoupper(trim(str_ireplace(' ZONE', '', $displayZone)));
            $stpZone = DB::table('ews_stp_districts')->where('name', $cleanDisplayZone)->first();
            if ($stpZone) {
                $userZoneId = $stpZone->id;
            }
        }

        // Resolve flat's district and zone if missing
        if ((empty($flat->district_id) || empty($flat->zone_id)) && !empty($flat->district_name)) {
            $matchedDist = DB::table('ews_districts')->where('name', strtoupper(trim($flat->district_name)))->first();
            if ($matchedDist) {
                $flat->district_id = $flat->district_id ?: $matchedDist->id;
                $flat->zone_id = $flat->zone_id ?: $matchedDist->zone_id;
            }
        }

        // Zone-wise update authorization check
        $allowed = false;
        if (!empty($userZoneId)) {
            $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $userZoneId)->pluck('id')->toArray();
            $allowed = ($flat->zone_id == $userZoneId) || in_array($flat->district_id, $zoneDistrictIds) || ($flat->created_by == $user->id);
            if (!$allowed && $flat->district_id) {
                $flatDistZone = DB::table('ews_districts')->where('id', $flat->district_id)->value('zone_id');
                if ($flatDistZone && $flatDistZone == $userZoneId) {
                    $allowed = true;
                }
            }
            if (!$allowed && !empty($flat->district_name)) {
                $flatDistZone = DB::table('ews_districts')->where('name', strtoupper(trim($flat->district_name)))->value('zone_id');
                if ($flatDistZone && $flatDistZone == $userZoneId) {
                    $allowed = true;
                }
            }
            if (!$allowed && !empty($flat->zone_name)) {
                $userZoneName = DB::table('ews_stp_districts')->where('id', $userZoneId)->value('name');
                if ($userZoneName && strcasecmp(trim(str_ireplace(' ZONE', '', $userZoneName)), trim(str_ireplace(' ZONE', '', $flat->zone_name))) === 0) {
                    $allowed = true;
                }
            }
        } else {
            $allowed = ($flat->created_by == $user->id) || ($user->district_id == $flat->district_id);
            if (!$allowed && !empty($user->district_name) && !empty($flat->district_name)) {
                $allowed = (strcasecmp(trim($user->district_name), trim($flat->district_name)) === 0);
            }
        }

        if (!$allowed) {
            abort(403, 'Unauthorized action for this flat record.');
        }

        $request->validate([
            'register_flat' => 'nullable|string|max:255',
            'district_id' => 'required|exists:ews_districts,id',
            'town_id' => 'required',
            'new_town_name' => 'required_if:town_id,new|nullable|string|max:255',
            'new_town_type' => 'required_if:town_id,new|nullable|string|max:255',
            'custom_town_type' => 'required_if:new_town_type,other|nullable|string|max:255',
            'developer_name' => 'required|string|max:255',
            'project_id' => 'required',
            'new_project_name' => 'required_if:project_id,new|nullable|string|max:255',
            'block_id' => 'required',
            'new_block_name' => 'required_if:block_id,new|nullable|string|max:255',
            'floor_number' => 'required|integer|min:0|max:100',
            'flat_number' => 'required|string|max:255',
            'license_no' => 'required|string|max:255',
            'address_of_project' => 'nullable|string|max:1000',
            'sector_no' => 'nullable|string|max:100',
            'area_of_project' => 'nullable|string|max:255',
            'approved_building_plan' => 'required|in:yes,no',
            'building_plan_approval_date' => 'required_if:approved_building_plan,yes|nullable|date',
            'oc_status_yn' => 'required|in:yes,no',
            'oc_status' => 'required_if:oc_status_yn,yes|nullable|string|max:50',
            'oc_date' => 'required_if:oc_status_yn,yes|nullable|date',
            'total_flats_in_project' => 'nullable|integer|min:1',
            'total_ews_flats_in_project' => 'nullable|integer|min:1',
            'flats_offered_for_draw' => 'nullable|integer|min:0',
            'remaining_flats_for_oc' => 'nullable|integer|min:0',
            'developer_plan' => 'nullable|file|mimes:pdf|max:10240',
            'building_plan_copy' => 'nullable|file|mimes:pdf|max:10240',
            'oc_copy' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->input('approved_building_plan') === 'no') {
            return back()->withInput()->with('error', "Validation Error: Approved Building Plan is mandatory for EWS flats. Without approval, flats cannot be saved.");
        }
        if ($request->input('oc_status_yn') === 'no') {
            return back()->withInput()->with('error', "Validation Error: Occupation Certificate (OC) is mandatory to offer flats for allotment.");
        }

        $district = DB::table('ews_districts')->where('id', $request->district_id)->first();

        if (!empty($userZoneId)) {
            $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $userZoneId)->pluck('id')->toArray();
            if ($district->zone_id != $userZoneId && !in_array($district->id, $zoneDistrictIds)) {
                return back()->withInput()->with('error', "Unauthorized: You can only update flats for districts within your assigned zone.");
            }
        } elseif (!empty($user->district_name)) {
            $userDist = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
            $selectedDist = strtoupper(trim(str_ireplace(' ZONE', '', $district->name)));
            if ($userDist !== $selectedDist && $user->district_id != $district->id) {
                return back()->withInput()->with('error', "Unauthorized: You can only update flats for {$user->district_name}.");
            }
        }

        // Resolve Zone directly from ews_stp_districts master table
        $zone = null;
        if (!empty($district->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $district->zone_id)->first();
        }
        if (!$zone && !empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        if (!$zone && !empty($user->district_name)) {
            $cleanZoneName = strtoupper(trim(str_replace(' ZONE', '', $user->district_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanZoneName)->first();
        }
        if (!$zone && $district) {
            $cleanDist = strtoupper(trim(str_replace(' ZONE', '', $district->name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanDist)->first();
        }
        $zoneId = $zone ? $zone->id : ($district->zone_id ?? ($user->zone_id ?? $flat->zone_id));
        $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : ($flat->zone_name ?? (!empty($user->zone_name) ? strtoupper($user->zone_name) : ($district ? strtoupper($district->name) . ' ZONE' : 'ZONE')));

        // Resolve Town ID and Name
        if ($request->town_id === 'new') {
            $cleanTownName = trim($request->new_town_name);
            $townType = $request->new_town_type === 'other' ? trim($request->custom_town_type ?? '') : trim($request->new_town_type ?? '');

            $existingTowns = EwsTown::where('district_id', $district->id)->get();
            $similarTown = EwsHelper::findSimilarName($cleanTownName, $existingTowns);
            if ($similarTown) {
                return back()->withInput()->with('error', "Validation Error: Town '{$cleanTownName}' already exists or is too similar to existing town '{$similarTown['existing']}' ({$similarTown['reason']}). Please select it from the list instead of adding a new one.");
            }

            $town = EwsTown::create([
                'district_id' => $district->id,
                'zone_id'     => $zoneId,
                'zone_name'   => $zoneName,
                'name'        => $cleanTownName,
                'type'        => !empty($townType) ? $townType : null,
            ]);
        } else {
            $town = EwsTown::where('district_id', $district->id)->where('id', $request->town_id)->first();
            if (!$town) {
                $town = EwsTown::find($request->town_id);
            }
            if (!$town) {
                return back()->withInput()->with('error', "Validation Error: The selected town is not registered under {$district->name}.");
            }
        }
        $townId = $town->id;
        $townName = $town->name;

        // Resolve Project ID and Name
        if ($request->project_id === 'new') {
            $cleanProjName = trim($request->new_project_name);
            $existingProjects = EwsProject::where('district_id', $district->id)->get();
            $similarProject = EwsHelper::findSimilarName($cleanProjName, $existingProjects);
            if ($similarProject) {
                return back()->withInput()->with('error', "Validation Error: Project '{$cleanProjName}' already exists or is too similar to existing project '{$similarProject['existing']}' ({$similarProject['reason']}). Please select it from the list instead of adding it as a new project.");
            }

            $project = EwsProject::create([
                'zone_id' => $zoneId,
                'zone_name' => $zoneName,
                'district_id' => $district->id,
                'district_name' => $district->name,
                'town_id' => $townId,
                'town_name' => $townName,
                'name' => $cleanProjName,
                'developer_name' => $request->developer_name,
                'license_no' => $request->license_no,
                'sector_no' => $request->sector_no,
                'address' => $request->address_of_project,
                'area_of_project' => $request->area_of_project,
            ]);
            $projectId = $project->id;
            $projectName = $project->name;
        } else {
            $project = EwsProject::where('district_id', $district->id)->where('id', $request->project_id)->firstOrFail();
            $project->update([
                'zone_id' => $project->zone_id ?? $zoneId,
                'zone_name' => $project->zone_name ?? $zoneName,
                'district_name' => $project->district_name ?? $district->name,
                'town_id' => $townId,
                'town_name' => $townName,
                'developer_name' => $request->developer_name ?: $project->developer_name,
                'license_no' => $request->license_no ?: $project->license_no,
                'sector_no' => $request->sector_no ?: $project->sector_no,
                'address' => $request->address_of_project ?: $project->address,
                'area_of_project' => $request->area_of_project ?: $project->area_of_project,
            ]);
            $projectId = $project->id;
            $projectName = $project->name;
        }

        // Resolve Block ID and Name
        if ($request->block_id === 'new') {
            $cleanBlockName = trim($request->new_block_name);
            $existingBlocks = EwsBlock::where('project_id', $projectId)->get();
            $similarBlock = EwsHelper::findSimilarName($cleanBlockName, $existingBlocks);
            if ($similarBlock) {
                return back()->withInput()->with('error', "Validation Error: Block/Tower '{$cleanBlockName}' already exists or is too similar to existing '{$similarBlock['existing']}' ({$similarBlock['reason']}) under the selected project. Please select it from the list.");
            }

            $block = EwsBlock::create([
                'project_id' => $projectId,
                'name' => $cleanBlockName,
            ]);
            $blockId = $block->id;
            $blockName = $block->name;
        } else {
            $block = EwsBlock::where('project_id', $projectId)->where('id', $request->block_id)->firstOrFail();
            $blockId = $block->id;
            $blockName = $block->name;
        }

        $floorNum = (int)$request->floor_number;
        if ($floorNum === 0) {
            $floorLabel = "Ground Floor";
        } elseif ($floorNum === 1) {
            $floorLabel = "First Floor";
        } elseif ($floorNum === 2) {
            $floorLabel = "Second Floor";
        } elseif ($floorNum === 3) {
            $floorLabel = "Third Floor";
        } else {
            $floorLabel = "{$floorNum}th Floor";
        }

        // Check if flat is already registered (excluding this record)
        $existsUpdate = EwsBuilderFlat::where('id', '!=', $flat->id)
            ->where('district_id', $district->id)
            ->where('town_name', $townName)
            ->where('project_name', $projectName)
            ->where('block_tower_number', $blockName)
            ->where('floor', $floorLabel)
            ->where('flat_number', $request->flat_number)
            ->exists();

        if ($existsUpdate) {
            return back()->withInput()->with('error', "Validation Error: Another EWS Flat with the same details ('{$request->flat_number}', Floor '{$floorLabel}', Block '{$blockName}') is already registered.");
        }

        // Handle File Uploads (Optional in edit)
        $developerPlanPath = null;
        if ($request->hasFile('developer_plan') && $request->file('developer_plan')->isValid()) {
            $file = $request->file('developer_plan');
            $filename = 'dev_plan_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $developerPlanPath = $file->storeAs('developer_plans', $filename, 'public');
        }

        $buildingPlanCopyPath = null;
        if ($request->hasFile('building_plan_copy') && $request->file('building_plan_copy')->isValid()) {
            $file = $request->file('building_plan_copy');
            $filename = 'bldg_plan_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $buildingPlanCopyPath = $file->storeAs('building_plans', $filename, 'public');
        }

        $ocFilePath = null;
        if ($request->hasFile('oc_copy') && $request->file('oc_copy')->isValid()) {
            $file = $request->file('oc_copy');
            $filename = 'oc_copy_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $ocFilePath = $file->storeAs('oc_documents', $filename, 'public');
        }

        // Track Old Values for Audit Diff
        $trackedKeys = [
            'flat_number' => 'Flat Number',
            'floor' => 'Floor',
            'block_tower_number' => 'Block / Tower',
            'developer_name' => 'Developer / Colonizer',
            'project_name' => 'Project Name',
            'license_no' => 'License No.',
            'sector_no' => 'Sector No.',
            'address_of_project' => 'Project Address',
            'area_of_project' => 'Project Area',
            'approved_building_plan' => 'Approved Building Plan',
            'building_plan_approval_date' => 'Building Plan Approval Date',
            'oc_status_yn' => 'OC Status (Y/N)',
            'oc_status' => 'OC Category',
            'oc_date' => 'OC Date',
            'total_flats_in_project' => 'Total Flats in Project',
            'total_ews_flats_in_project' => 'Total EWS Flats',
            'flats_offered_for_draw' => 'Flats Offered for Draw',
            'remaining_flats_for_oc' => 'Remaining Flats for OC',
            'district_name' => 'District',
            'town_name' => 'Town',
            'is_aproved_by_dtp' => 'DTP Approval Status',
        ];

        $oldValues = [];
        foreach ($trackedKeys as $k => $label) {
            $oldValues[$k] = $flat->$k;
        }

        // Update Flat Attributes
        $flat->district_id = $district->id;
        $flat->dist_id = $district->id;
        $flat->district_name = $district->name;
        $flat->dist_name = $district->name;
        $flat->zone_id = $zoneId;
        $flat->zone_name = $zoneName;
        $flat->town_id = $townId;
        $flat->town_name = $townName;
        $flat->project_id = $projectId;
        $flat->project_name = $projectName;
        $flat->developer_name = $request->developer_name;
        $flat->license_no = $request->license_no;
        $flat->sector_no = $request->sector_no;
        $flat->address_of_project = $request->address_of_project;
        $flat->area_of_project = $request->area_of_project;
        // Step 10: Approved Building Plan Handling (Strict Nullification on 'no')
        $isPlanApproved = strtolower($request->approved_building_plan ?? 'no') === 'yes';
        $flat->approved_building_plan = $isPlanApproved ? 'yes' : 'no';
        if ($isPlanApproved) {
            $flat->building_plan_approval_date = $request->building_plan_approval_date;
            if ($buildingPlanCopyPath) {
                $flat->building_plan_copy = $buildingPlanCopyPath;
            }
        } else {
            // Nullify building plan approval date & copy when NOT approved
            $flat->building_plan_approval_date = null;
            $flat->building_plan_copy = null;
        }

        // Step 11: Status of OC Handling (Strict Nullification on 'no')
        $isOcReceived = strtolower($request->oc_status_yn ?? 'no') === 'yes';
        $flat->oc_status_yn = $isOcReceived ? 'yes' : 'no';
        if ($isOcReceived) {
            $ocCat = strtolower($request->oc_status ?? 'part');
            $flat->oc_status = $ocCat;
            $flat->oc_date = $request->oc_date;
            if ($ocFilePath) {
                $flat->oc_copy_file = $ocFilePath;
            }
            $flat->total_flats_in_project = $request->filled('total_flats_in_project') ? (int)$request->total_flats_in_project : null;
            $totalEws = $request->filled('total_ews_flats_in_project') ? (int)$request->total_ews_flats_in_project : null;
            $flat->total_ews_flats_in_project = $totalEws;

            if ($ocCat === 'complete') {
                $flat->flats_offered_for_draw = null;
                $flat->remaining_flats_for_oc = null;
            } else {
                $offered = $request->filled('flats_offered_for_draw') ? (int)$request->flats_offered_for_draw : null;
                $flat->flats_offered_for_draw = $offered;
                $flat->remaining_flats_for_oc = $request->filled('remaining_flats_for_oc') ? (int)$request->remaining_flats_for_oc : null;
            }
        } else {
            // Nullify ALL OC fields when OC is NOT received
            $flat->oc_status = null;
            $flat->oc_date = null;
            $flat->oc_copy_file = null;
            $flat->total_flats_in_project = null;
            $flat->total_ews_flats_in_project = null;
            $flat->flats_offered_for_draw = null;
            $flat->remaining_flats_for_oc = null;
        }

        $flat->block_id = $blockId;
        $flat->block_tower_number = $blockName;
        $flat->floor = $floorLabel;
        $flat->flat_number = $request->flat_number;
        $flat->flat_code = EwsHelper::generateFlatCode(
            $townName,
            $user->name,
            $floorLabel,
            $blockName,
            $request->flat_number
        );

        // Step DTP Approval Handling (Approved & Submit = 1)
        if ($request->filled('is_aproved_by_dtp') || $request->has('approve_and_submit')) {
            $approvedVal = (int)$request->input('is_aproved_by_dtp', 1);
            $flat->is_aproved_by_dtp = $approvedVal;
            $flat->is_approved_by_dtp = $approvedVal;
            if ($approvedVal === 1) {
                $flat->dtp_approved_at = $flat->dtp_approved_at ?: now();
                $flat->dtp_approved_by = $user->id;
                $flat->dtp_user_name = $user->name;
            } else {
                $flat->dtp_approved_at = null;
                $flat->dtp_approved_by = null;
                $flat->dtp_user_name = null;
            }
        }

        $flat->save();

        // Calculate Diff and Build Audit Trail
        $changedDiff = [];
        $summaryItems = [];
        foreach ($trackedKeys as $k => $label) {
            $oVal = trim((string)($oldValues[$k] ?? ''));
            $nVal = trim((string)($flat->$k ?? ''));
            if ($oVal !== $nVal) {
                $changedDiff[$k] = [
                    'label' => $label,
                    'old' => $oVal !== '' ? $oVal : '(None)',
                    'new' => $nVal !== '' ? $nVal : '(None)',
                ];
                $summaryItems[] = "{$label}: [{$oVal}] ➔ [{$nVal}]";
            }
        }
        if ($developerPlanPath) {
            $changedDiff['developer_plan'] = ['label' => 'Developer Plan PDF', 'old' => 'Previous File', 'new' => 'New PDF Uploaded'];
            $summaryItems[] = "New Developer Plan PDF uploaded";
        }
        if ($buildingPlanCopyPath) {
            $changedDiff['building_plan_copy'] = ['label' => 'Building Plan PDF', 'old' => 'Previous File', 'new' => 'New PDF Uploaded'];
            $summaryItems[] = "New Building Plan PDF uploaded";
        }
        if ($ocFilePath) {
            $changedDiff['oc_copy_file'] = ['label' => 'OC Copy PDF', 'old' => 'Previous File', 'new' => 'New OC Copy Uploaded'];
            $summaryItems[] = "New OC Copy PDF uploaded";
        }

        $summaryText = !empty($summaryItems)
            ? "Record updated by {$user->name} (" . strtoupper($user->role ?? 'STP') . "): " . implode('; ', $summaryItems)
            : "Record verified and re-submitted by {$user->name} (" . strtoupper($user->role ?? 'STP') . ") without field modifications";

        // Store into EwsFlatAuditLog
        $this->ensureAuditTableExists();
        try {
            EwsFlatAuditLog::create([
                'flat_id' => $flat->id,
                'flat_code' => $flat->flat_code,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role ?? 'ews_stp',
                'action' => !empty($changedDiff) ? 'UPDATED' : 'RESUBMITTED',
                'summary' => $summaryText,
                'changed_fields' => $changedDiff,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Ignore audit log error if table issue
        }

        // Store into EwsDeveloperLog
        try {
            EwsDeveloperLog::create([
                'user_id' => $user->id,
                'action' => 'UPDATED_FLAT',
                'details' => "Updated Flat {$flat->flat_number} ({$flat->flat_code}): " . $summaryText,
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('ews.stp.flats.show', $flat->secure_id)
            ->with('success', 'EWS Flat record updated and audit log successfully registered.');
    }

    public function destroy(Request $request, $secureId)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403);
        }

        $flat = EwsBuilderFlat::where('secure_id', $secureId)->firstOrFail();

        // Zone-wise delete authorization check
        if (!empty($user->zone_id)) {
            $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $user->zone_id)->pluck('id')->toArray();
            $allowed = ($flat->zone_id == $user->zone_id) || in_array($flat->district_id, $zoneDistrictIds) || ($flat->created_by == $user->id);
            if (!$allowed) {
                abort(403, 'Unauthorized action for this zone.');
            }
        }

        $oldDetails = "Flat: {$flat->flat_number}, Floor: {$flat->floor}, Tower: {$flat->block_tower_number} under Project '{$flat->project_name}' in {$flat->town_name} ({$flat->district_name})";

        $flat->delete();

        // Create log entry
        EwsDeveloperLog::create([
            'user_id' => $user->id,
            'action' => 'DELETED',
            'details' => "Deleted EWS Flat [{$oldDetails}]",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'EWS Builder Flat record deleted successfully.');
    }

    public function logs()
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403);
        }

        $displayZoneName = $this->resolveDisplayZoneName($user);

        // Fetch logs with pagination
        $logs = EwsDeveloperLog::with('developer')
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('ews.stp.logs', compact('user', 'logs', 'displayZoneName'));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403);
        }

        if ($request->input('ownership_scope') === 'allotted') {
            $query = DB::table('ews_allotted_8');
            if ($request->filled('district_id')) {
                $query->where('dist_id', $request->district_id);
            } else {
                if (!empty($user->zone_id)) {
                    $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $user->zone_id)->pluck('id')->toArray();
                    $query->whereIn('dist_id', $zoneDistrictIds);
                } elseif ($user && !empty($user->district_name)) {
                    $userDist = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
                    $query->where('dist_name', 'like', "%{$userDist}%");
                }
            }
            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function($q) use ($s) {
                    $q->where('application_number', 'like', "%{$s}%")
                      ->orWhere('full_name', 'like', "%{$s}%")
                      ->orWhere('mobile_number', 'like', "%{$s}%")
                      ->orWhere('flat_no', 'like', "%{$s}%")
                      ->orWhere('dist_name', 'like', "%{$s}%");
                });
            }
            $allotted = $query->orderBy('id', 'asc')->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="ews_allotted_flats_' . date('Ymd_His') . '.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($allotted) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['S.No.', 'District Name', 'Application Number', 'Allottee Name', 'Mobile Number', 'Flat No.', 'Possession Status']);

                foreach ($allotted as $index => $row) {
                    $isGiven = ($row->is_possession_given == 1 || $row->possession_status === 'Possession Given');
                    fputcsv($file, [
                        $index + 1,
                        $row->dist_name,
                        $row->application_number,
                        $row->full_name,
                        $row->mobile_number,
                        $row->flat_no,
                        $isGiven ? 'Possession Given' : ($row->possession_status ?: 'Allotted'),
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Respect search and custom district filters
        $flats = $this->getFilteredQuery($request)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ews_builder_flats_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($flats) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['S.No.', 'District Name', 'Town Name', 'Project Name', 'License No.', 'Block/Tower No.', 'Floor Details', 'Flat No.', 'Registered At']);

            foreach ($flats as $index => $flat) {
                fputcsv($file, [
                    $index + 1,
                    $flat->district_name,
                    $flat->town_name,
                    $flat->project_name,
                    $flat->license_no ?? '-',
                    $flat->block_tower_number,
                    $flat->floor,
                    $flat->flat_number,
                    $flat->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403);
        }

        // Respect search and custom district filters
        $flats = $this->getFilteredQuery($request)->get();

        $pdf = Pdf::loadView('ews.stp.pdf_report', compact('flats'));
        return $pdf->download('ews_builder_flats_' . date('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403);
        }

        if ($request->input('ownership_scope') === 'allotted') {
            $query = DB::table('ews_allotted_8');
            if ($request->filled('district_id')) {
                $query->where('dist_id', $request->district_id);
            } else {
                if (!empty($user->zone_id)) {
                    $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $user->zone_id)->pluck('id')->toArray();
                    $query->whereIn('dist_id', $zoneDistrictIds);
                } elseif ($user && !empty($user->district_name)) {
                    $userDist = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
                    $query->where('dist_name', 'like', "%{$userDist}%");
                }
            }
            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function($q) use ($s) {
                    $q->where('application_number', 'like', "%{$s}%")
                      ->orWhere('full_name', 'like', "%{$s}%")
                      ->orWhere('mobile_number', 'like', "%{$s}%")
                      ->orWhere('flat_no', 'like', "%{$s}%")
                      ->orWhere('dist_name', 'like', "%{$s}%");
                });
            }
            $allotted = $query->orderBy('id', 'asc')->get();

            $headers = [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="ews_allotted_flats_' . date('Ymd_His') . '.xls"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($allotted) {
                $file = fopen('php://output', 'w');
                fputs($file, "S.No.\tDistrict Name\tApplication Number\tAllottee Name\tMobile Number\tFlat No.\tPossession Status\n");

                foreach ($allotted as $index => $row) {
                    $isGiven = ($row->is_possession_given == 1 || $row->possession_status === 'Possession Given');
                    fputs($file, ($index + 1) . "\t" .
                        $row->dist_name . "\t" .
                        $row->application_number . "\t" .
                        $row->full_name . "\t" .
                        $row->mobile_number . "\t" .
                        $row->flat_no . "\t" .
                        ($isGiven ? 'Possession Given' : ($row->possession_status ?: 'Allotted')) . "\n"
                    );
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        $flats = $this->getFilteredQuery($request)->get();

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="ews_builder_flats_' . date('Ymd_His') . '.xls"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($flats) {
            $file = fopen('php://output', 'w');
            fputs($file, "S.No.\tDistrict Name\tTown Name\tProject Name\tLicense No.\tBlock/Tower No.\tFloor Details\tFlat No.\tRegistered At\n");

            foreach ($flats as $index => $flat) {
                fputs($file, ($index + 1) . "\t" .
                    $flat->district_name . "\t" .
                    $flat->town_name . "\t" .
                    $flat->project_name . "\t" .
                    ($flat->license_no ?? '-') . "\t" .
                    $flat->block_tower_number . "\t" .
                    $flat->floor . "\t" .
                    $flat->flat_number . "\t" .
                    $flat->created_at->format('Y-m-d H:i:s') . "\n"
                );
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function districtStats()
    {
        return redirect()->route('ews.stp.dashboard');
    }

    public function getProjects(Request $request)
    {
        $districtId = $request->query('district_id');
        $townId = $request->query('town_id');
        $zoneId = $request->query('zone_id');

        if (!$districtId && !$townId && !$zoneId) {
            return response()->json([]);
        }

        $query = EwsProject::query();

        if ($zoneId) {
            $query->where('zone_id', $zoneId);
        }

        if ($districtId) {
            $query->where('district_id', $districtId);
        }

        if ($townId) {
            $query->where(function ($q) use ($townId) {
                $q->where('town_id', $townId)
                  ->orWhereNull('town_id');
            });
        }

        $projects = $query->orderBy('name', 'asc')
            ->get(['id', 'name', 'project_abbr', 'town_id', 'town_name', 'district_id', 'zone_id']);

        return response()->json($projects);
    }

    public function getBlocks(Request $request)
    {
        $projectId = $request->query('project_id');
        $townId = $request->query('town_id');
        $districtId = $request->query('district_id');
        $zoneId = $request->query('zone_id');

        if (!$projectId && !$townId && !$districtId && !$zoneId) {
            return response()->json([]);
        }

        $query = EwsBlock::query();

        if ($projectId) {
            $query->where('project_id', $projectId);
        } else {
            if ($townId) {
                $query->where(function ($q) use ($townId) {
                    $q->where('town_id', $townId)
                      ->orWhereNull('town_id');
                });
            }

            if ($districtId) {
                $query->where(function ($q) use ($districtId) {
                    $q->where('district_id', $districtId)
                      ->orWhereNull('district_id');
                });
            }

            if ($zoneId) {
                $query->where(function ($q) use ($zoneId) {
                    $q->where('zone_id', $zoneId)
                      ->orWhereNull('zone_id');
                });
            }
        }

        $blocks = $query->orderBy('name', 'asc')
            ->get(['id', 'name', 'project_id', 'project_name', 'town_id', 'town_name', 'district_id', 'zone_id']);

        return response()->json($blocks);
    }

    public function getTowns(Request $request)
    {
        $districtId = $request->query('district_id');
        if (!$districtId) {
            $user = Auth::user();
            if ($user && !empty($user->zone_id)) {
                $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $user->zone_id)->pluck('id')->toArray();
                $towns = EwsTown::whereIn('district_id', $zoneDistrictIds)->orderBy('name', 'asc')->get(['id', 'name', 'type']);
                return response()->json($towns);
            }
            return response()->json([]);
        }
        $towns = EwsTown::where('district_id', $districtId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'type']);
        return response()->json($towns);
    }

    public function storeTownAjax(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'register_flat' => 'nullable|string|max:255',
            'district_id' => 'required|exists:ews_districts,id',
            'town_name' => 'required|string|max:255',
            'town_type' => 'required|string|max:255',
        ]);

        $districtId = (int)$request->district_id;
        $cleanName = trim($request->town_name);
        $cleanType = trim($request->town_type);

        // Resolve Zone for this district
        $district = DB::table('ews_districts')->where('id', $districtId)->first();
        $zone = null;
        if ($district && !empty($district->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $district->zone_id)->first();
        }
        if (!$zone && $user && !empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        $zoneId = $zone ? $zone->id : ($district->zone_id ?? null);
        $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : null;

        $existingTowns = EwsTown::where('district_id', $districtId)->get();
        $similarTown = EwsHelper::findSimilarName($cleanName, $existingTowns);

        if ($similarTown) {
            return response()->json([
                'success' => false,
                'duplicate' => true,
                'existing_name' => $similarTown['existing'],
                'message' => "Town '{$cleanName}' already exists or is too similar to existing town '{$similarTown['existing']}' ({$similarTown['reason']}). Please select it from the list instead of adding a new one."
            ], 422);
        }

        $town = EwsTown::create([
            'district_id' => $districtId,
            'zone_id'     => $zoneId,
            'zone_name'   => $zoneName,
            'name'        => $cleanName,
            'type'        => $cleanType,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Town '{$cleanName}' added successfully to database.",
            'town' => [
                'id' => $town->id,
                'name' => $town->name,
                'type' => $town->type,
                'zone_id' => $town->zone_id,
                'zone_name' => $town->zone_name,
            ]
        ]);
    }

    public function storeProjectAjax(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'register_flat' => 'nullable|string|max:255',
            'district_id' => 'required|exists:ews_districts,id',
            'town_id' => 'nullable|exists:ews_towns,id',
            'project_name' => 'required|string|max:255',
        ]);

        $districtId = (int)$request->district_id;
        $townId = $request->filled('town_id') ? (int)$request->town_id : null;
        $cleanName = trim($request->project_name);

        $district = DB::table('ews_districts')->where('id', $districtId)->first();
        $town = $townId ? EwsTown::find($townId) : null;

        // Resolve Zone
        $zone = null;
        if ($district && $district->zone_id) {
            $zone = DB::table('ews_stp_districts')->where('id', $district->zone_id)->first();
        }
        if (!$zone && !empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        $zoneId = $zone ? $zone->id : ($district->zone_id ?? $user->zone_id);
        $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : (!empty($user->zone_name) ? strtoupper($user->zone_name) : null);

        $projectQuery = EwsProject::where('district_id', $districtId);
        if ($zoneId) {
            $projectQuery->where('zone_id', $zoneId);
        }
        if ($townId) {
            $projectQuery->where(function($q) use ($townId) {
                $q->where('town_id', $townId)->orWhereNull('town_id');
            });
        }
        $existingProjects = $projectQuery->get();
        $similarProject = EwsHelper::findSimilarName($cleanName, $existingProjects);

        if ($similarProject) {
            return response()->json([
                'success' => false,
                'duplicate' => true,
                'existing_name' => $similarProject['existing'],
                'message' => "Project '{$cleanName}' already exists or is too similar to existing project '{$similarProject['existing']}' ({$similarProject['reason']}). Please select it from the dropdown."
            ], 422);
        }

        $project = EwsProject::create([
            'zone_id' => $zoneId,
            'zone_name' => $zoneName,
            'district_id' => $districtId,
            'district_name' => $district ? $district->name : null,
            'town_id' => $townId,
            'town_name' => $town ? $town->name : null,
            'name' => $cleanName,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Project '{$cleanName}' added successfully to database.",
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ]
        ]);
    }

    public function storeBlockAjax(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'project_id' => 'required|exists:ews_projects,id',
            'block_name' => 'required|string|max:255',
            'town_id'    => 'nullable|exists:ews_towns,id',
            'district_id'=> 'nullable|exists:ews_districts,id',
            'zone_id'    => 'nullable',
        ]);

        $projectId = (int)$request->project_id;
        $cleanName = trim($request->block_name);

        $project = EwsProject::find($projectId);
        $districtId = $request->filled('district_id') ? (int)$request->district_id : ($project->district_id ?? null);
        $townId = $request->filled('town_id') ? (int)$request->town_id : ($project->town_id ?? null);
        $zoneId = $request->filled('zone_id') ? (int)$request->zone_id : ($project->zone_id ?? $user->zone_id);

        $district = $districtId ? DB::table('ews_districts')->where('id', $districtId)->first() : null;
        $town = $townId ? EwsTown::find($townId) : null;
        $zone = $zoneId ? DB::table('ews_stp_districts')->where('id', $zoneId)->first() : null;

        $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : ($project->zone_name ?? null);
        $districtName = $district ? $district->name : ($project->district_name ?? null);
        $townName = $town ? $town->name : ($project->town_name ?? null);
        $projectName = $project ? $project->name : null;

        $existingBlocksQuery = EwsBlock::where('project_id', $projectId);
        if ($townId) {
            $existingBlocksQuery->where(function($q) use ($townId) {
                $q->where('town_id', $townId)->orWhereNull('town_id');
            });
        }
        $existingBlocks = $existingBlocksQuery->get();
        $similarBlock = EwsHelper::findSimilarName($cleanName, $existingBlocks);

        if ($similarBlock) {
            return response()->json([
                'success' => false,
                'duplicate' => true,
                'existing_name' => $similarBlock['existing'],
                'message' => "Block/Tower '{$cleanName}' already exists or is too similar to existing '{$similarBlock['existing']}' ({$similarBlock['reason']}) under this project. Please select it from the dropdown."
            ], 422);
        }

        $block = EwsBlock::create([
            'zone_id'       => $zoneId,
            'zone_name'     => $zoneName,
            'district_id'   => $districtId,
            'district_name' => $districtName,
            'town_id'       => $townId,
            'town_name'     => $townName,
            'project_id'    => $projectId,
            'project_name'  => $projectName,
            'name'          => $cleanName,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Block/Tower '{$cleanName}' added successfully to database.",
            'block' => [
                'id' => $block->id,
                'name' => $block->name,
            ]
        ]);
    }

    /**
     * Render the STP License & Flat Verification Console view.
     */
    public function verifyView(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403, 'Unauthorized access to STP verification console.');
        }

        $displayZoneName = $this->resolveDisplayZoneName($user);

        // Resolve Zone model
        $zone = null;
        if (!empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        if (!$zone && !empty($user->zone_name)) {
            $cleanZone = strtoupper(trim(str_ireplace(' ZONE', '', $user->zone_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanZone)->first();
        }
        if (!$zone && !empty($user->district_name)) {
            $cleanName = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanName)->first();
            if (!$zone) {
                $dist = DB::table('ews_districts')->where('name', $cleanName)->first();
                if ($dist && $dist->zone_id) {
                    $zone = DB::table('ews_stp_districts')->where('id', $dist->zone_id)->first();
                }
            }
        }
        $zoneId = $zone ? $zone->id : ($user->zone_id ?? 2);

        // Districts strictly under this STP's Zone
        $districts = DB::table('ews_districts')
            ->where('zone_id', $zoneId)
            ->orderBy('name')
            ->get();
        $districtIds = $districts->pluck('id')->toArray();

        // Distinct license numbers in this zone
        $licensesFromFlats = DB::table('ews_builder_flats')
            ->where(function($q) use ($zoneId, $displayZoneName, $districtIds) {
                $q->where('zone_id', $zoneId)
                  ->orWhere('zone_name', 'like', "%{$displayZoneName}%")
                  ->orWhereIn('district_id', $districtIds);
            })
            ->whereNotNull('license_no')
            ->where('license_no', '!=', '')
            ->distinct()
            ->pluck('license_no')
            ->toArray();

        $licensesFromProjects = DB::table('ews_projects')
            ->where(function($q) use ($zoneId, $displayZoneName, $districtIds) {
                $q->where('zone_id', $zoneId)
                  ->orWhere('zone_name', 'like', "%{$displayZoneName}%")
                  ->orWhereIn('district_id', $districtIds);
            })
            ->whereNotNull('license_no')
            ->where('license_no', '!=', '')
            ->distinct()
            ->pluck('license_no')
            ->toArray();

        $licenses = array_values(array_unique(array_filter(array_merge($licensesFromFlats, $licensesFromProjects))));
        sort($licenses);

        return view('ews.stp.verify', compact(
            'user',
            'zone',
            'zoneId',
            'displayZoneName',
            'districts',
            'licenses'
        ));
    }

    /**
     * Render the dedicated full-page Matrix View of verified license records.
     */
    public function verifyMatrixView(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            abort(403, 'Unauthorized access to verification matrix.');
        }

        $licenseNo = trim($request->query('license_no', ''));
        $districtId = $request->query('district_id');
        $townId = $request->query('town_id');
        $displayZoneName = $this->resolveDisplayZoneName($user);

        $flats = collect();
        if (!empty($licenseNo)) {
            $flats = DB::table('ews_builder_flats')
                ->where('license_no', $licenseNo)
                ->when(!empty($districtId), fn($q) => $q->where('district_id', $districtId))
                ->when(!empty($townId), fn($q) => $q->where('town_id', $townId))
                ->orderBy('id')
                ->get()
                ->map(function($f) {
                    $f->secure_id = $f->secure_id ?: EwsHelper::encodeSecureId($f->id);
                    return $f;
                });
        }

        return view('ews.stp.verify_matrix', compact(
            'user',
            'displayZoneName',
            'licenseNo',
            'districtId',
            'townId',
            'flats'
        ));
    }

    /**
     * Get filtered license numbers for AJAX cascading updates.
     */
    public function getVerifyLicenses(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            return response()->json([], 403);
        }

        $districtId = $request->query('district_id');
        $townId = $request->query('town_id');
        $projectId = $request->query('project_id');
        $displayZoneName = $this->resolveDisplayZoneName($user);
        $zoneId = $user->zone_id ?? 2;

        $flatQuery = DB::table('ews_builder_flats')
            ->whereNotNull('license_no')
            ->where('license_no', '!=', '');

        $projQuery = DB::table('ews_projects')
            ->whereNotNull('license_no')
            ->where('license_no', '!=', '');

        if (!empty($projectId)) {
            $flatQuery->where('project_id', $projectId);
            $projQuery->where('id', $projectId);
        }

        if (!empty($districtId)) {
            $flatQuery->where('district_id', $districtId);
            $projQuery->where('district_id', $districtId);
        } else {
            $districtIds = DB::table('ews_districts')->where('zone_id', $zoneId)->pluck('id')->toArray();
            $flatQuery->where(function($q) use ($zoneId, $displayZoneName, $districtIds) {
                $q->where('zone_id', $zoneId)
                  ->orWhere('zone_name', 'like', "%{$displayZoneName}%")
                  ->orWhereIn('district_id', $districtIds);
            });
            $projQuery->where(function($q) use ($zoneId, $displayZoneName, $districtIds) {
                $q->where('zone_id', $zoneId)
                  ->orWhere('zone_name', 'like', "%{$displayZoneName}%")
                  ->orWhereIn('district_id', $districtIds);
            });
        }

        if (!empty($townId)) {
            $flatQuery->where('town_id', $townId);
            $projQuery->where('town_id', $townId);
        }

        $flatLicenses = $flatQuery->distinct()->pluck('license_no')->toArray();
        $projLicenses = $projQuery->distinct()->pluck('license_no')->toArray();

        $licenses = array_values(array_unique(array_filter(array_merge($flatLicenses, $projLicenses))));
        sort($licenses);

        return response()->json($licenses);
    }

    /**
     * Hit local database for all records under the selected License Number.
     * Supports multiple project records / schemes / phases under the same license.
     */
    public function getVerifyRecords(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $licenseNo = trim($request->query('license_no', ''));
        if (empty($licenseNo)) {
            return response()->json(['success' => false, 'message' => 'License number is required'], 400);
        }

        $districtId = $request->query('district_id');
        $townId = $request->query('town_id');

        // 1. Query all matching projects
        $projects = DB::table('ews_projects')
            ->where('license_no', $licenseNo)
            ->when(!empty($districtId), fn($q) => $q->where('district_id', $districtId))
            ->when(!empty($townId), fn($q) => $q->where('town_id', $townId))
            ->orderBy('id')
            ->get();

        // 2. Query all matching flats
        $flats = DB::table('ews_builder_flats')
            ->where('license_no', $licenseNo)
            ->when(!empty($districtId), fn($q) => $q->where('district_id', $districtId))
            ->when(!empty($townId), fn($q) => $q->where('town_id', $townId))
            ->orderBy('id')
            ->get();

        if ($projects->isEmpty() && $flats->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No records found in local database for License: ' . $licenseNo,
                'total_records' => 0,
                'total_flats' => 0,
                'records' => [],
                'all_flats' => [],
                'flats' => [],
                'meta' => null
            ]);
        }

        $records = [];
        $matchedFlatIds = [];
        $recordIdx = 1;

        // Group 1: From ews_projects
        foreach ($projects as $proj) {
            $projFlats = $flats->filter(function($f) use ($proj) {
                if (!empty($f->project_id) && $f->project_id == $proj->id) return true;
                if (empty($f->project_id) && !empty($f->project_name) && strcasecmp(trim($f->project_name), trim($proj->name)) === 0) return true;
                return false;
            })->values();

            foreach ($projFlats as $pf) {
                $matchedFlatIds[] = $pf->id;
            }

            $sampleFlat = $projFlats->first();

            // Format flats with record context
            $formattedFlats = $projFlats->map(function($f) use ($recordIdx, $proj) {
                $fObj = (array) $f;
                $fObj['record_id'] = 'proj_' . $proj->id;
                $fObj['record_index'] = $recordIdx;
                $fObj['record_project_name'] = $proj->name ?? ($f->project_name ?? 'N/A');
                $fObj['secure_id'] = $f->secure_id ?: EwsHelper::encodeSecureId($f->id);
                $fObj['is_aproved_by_dtp'] = (int)($f->is_aproved_by_dtp ?? $f->is_approved_by_dtp ?? 0);
                $fObj['dtp_approved_at'] = !empty($f->dtp_approved_at) ? \Carbon\Carbon::parse($f->dtp_approved_at)->format('d M, Y H:i') : null;
                $fObj['dtp_user_name'] = $f->dtp_user_name ?? null;
                return (object) $fObj;
            });

            $records[] = [
                'record_id' => 'proj_' . $proj->id,
                'record_index' => $recordIdx,
                'project_id' => $proj->id,
                'project_name' => $proj->name ?? ($sampleFlat->project_name ?? 'N/A'),
                'developer_name' => $proj->developer_name ?? ($sampleFlat->developer_name ?? 'N/A'),
                'district_name' => $proj->district_name ?? ($sampleFlat->district_name ?? 'N/A'),
                'town_name' => $proj->town_name ?? ($sampleFlat->town_name ?? 'N/A'),
                'sector_no' => $proj->sector_no ?? ($sampleFlat->sector_no ?? 'N/A'),
                'address' => $proj->address ?? ($sampleFlat->address_of_project ?? 'N/A'),
                'area_of_project' => $proj->area_of_project ?? ($sampleFlat->area_of_project ?? 'N/A'),
                'total_flats_in_project' => $sampleFlat->total_flats_in_project ?? null,
                'total_ews_flats_in_project' => $sampleFlat->total_ews_flats_in_project ?? null,
                'flats_offered_for_draw' => $sampleFlat->flats_offered_for_draw ?? $projFlats->count(),
                'remaining_flats_for_oc' => $sampleFlat->remaining_flats_for_oc ?? null,
                'oc_status' => !empty($sampleFlat->oc_status) ? ucfirst($sampleFlat->oc_status) : (!empty($proj->oc_status_yn) ? strtoupper($proj->oc_status_yn) : 'Pending'),
                'oc_date' => $sampleFlat->oc_date ?? null,
                'approved_building_plan' => !empty($sampleFlat->approved_building_plan) ? strtoupper($sampleFlat->approved_building_plan) : (!empty($proj->approved_building_plan) ? strtoupper($proj->approved_building_plan) : 'N/A'),
                'building_plan_approval_date' => $sampleFlat->building_plan_approval_date ?? ($proj->building_plan_approval_date ?? null),
                'developer_plan_url' => !empty($sampleFlat->developer_plan) ? asset('storage/' . $sampleFlat->developer_plan) : (!empty($proj->developer_plan) ? asset('storage/' . $proj->developer_plan) : null),
                'building_plan_url' => !empty($sampleFlat->building_plan_copy) ? asset('storage/' . $sampleFlat->building_plan_copy) : (!empty($proj->building_plan_copy) ? asset('storage/' . $proj->building_plan_copy) : null),
                'oc_copy_url' => !empty($sampleFlat->oc_copy_file) ? asset('storage/' . $sampleFlat->oc_copy_file) : null,
                'flats_count' => $projFlats->count(),
                'flats' => $formattedFlats->toArray()
            ];

            $recordIdx++;
        }

        // Group 2: Unmatched flats in ews_builder_flats
        $unmatchedFlats = $flats->filter(fn($f) => !in_array($f->id, $matchedFlatIds));
        if ($unmatchedFlats->isNotEmpty()) {
            $groupedUnmatched = $unmatchedFlats->groupBy(fn($f) => ($f->project_name ?: 'Scheme') . '___' . ($f->sector_no ?: ''));
            foreach ($groupedUnmatched as $groupKey => $gFlats) {
                $sampleFlat = $gFlats->first();

                $formattedFlats = $gFlats->map(function($f) use ($recordIdx, $sampleFlat) {
                    $fObj = (array) $f;
                    $fObj['record_id'] = 'unmatched_' . md5($sampleFlat->project_name . $sampleFlat->sector_no);
                    $fObj['record_index'] = $recordIdx;
                    $fObj['record_project_name'] = $sampleFlat->project_name ?? 'N/A';
                    $fObj['secure_id'] = $f->secure_id ?: EwsHelper::encodeSecureId($f->id);
                    $fObj['is_aproved_by_dtp'] = (int)($f->is_aproved_by_dtp ?? $f->is_approved_by_dtp ?? 0);
                    $fObj['dtp_approved_at'] = !empty($f->dtp_approved_at) ? \Carbon\Carbon::parse($f->dtp_approved_at)->format('d M, Y H:i') : null;
                    $fObj['dtp_user_name'] = $f->dtp_user_name ?? null;
                    return (object) $fObj;
                });

                $records[] = [
                    'record_id' => 'unmatched_' . md5($groupKey),
                    'record_index' => $recordIdx,
                    'project_id' => $sampleFlat->project_id ?? null,
                    'project_name' => $sampleFlat->project_name ?? 'N/A',
                    'developer_name' => $sampleFlat->developer_name ?? 'N/A',
                    'district_name' => $sampleFlat->district_name ?? 'N/A',
                    'town_name' => $sampleFlat->town_name ?? 'N/A',
                    'sector_no' => $sampleFlat->sector_no ?? 'N/A',
                    'address' => $sampleFlat->address_of_project ?? 'N/A',
                    'area_of_project' => $sampleFlat->area_of_project ?? 'N/A',
                    'total_flats_in_project' => $sampleFlat->total_flats_in_project ?? null,
                    'total_ews_flats_in_project' => $sampleFlat->total_ews_flats_in_project ?? null,
                    'flats_offered_for_draw' => $sampleFlat->flats_offered_for_draw ?? $gFlats->count(),
                    'remaining_flats_for_oc' => $sampleFlat->remaining_flats_for_oc ?? null,
                    'oc_status' => !empty($sampleFlat->oc_status) ? ucfirst($sampleFlat->oc_status) : 'Pending',
                    'oc_date' => $sampleFlat->oc_date ?? null,
                    'approved_building_plan' => !empty($sampleFlat->approved_building_plan) ? strtoupper($sampleFlat->approved_building_plan) : 'N/A',
                    'building_plan_approval_date' => $sampleFlat->building_plan_approval_date ?? null,
                    'developer_plan_url' => !empty($sampleFlat->developer_plan) ? asset('storage/' . $sampleFlat->developer_plan) : null,
                    'building_plan_url' => !empty($sampleFlat->building_plan_copy) ? asset('storage/' . $sampleFlat->building_plan_copy) : null,
                    'oc_copy_url' => !empty($sampleFlat->oc_copy_file) ? asset('storage/' . $sampleFlat->oc_copy_file) : null,
                    'flats_count' => $gFlats->count(),
                    'flats' => $formattedFlats->toArray()
                ];

                $recordIdx++;
            }
        }

        // Combine all flats for master list
        $allFlats = [];
        foreach ($records as $r) {
            foreach ($r['flats'] as $rf) {
                $allFlats[] = $rf;
            }
        }

        // Summary metrics
        $colonizers = array_values(array_unique(array_filter(array_column($records, 'developer_name'))));
        $sectors = array_values(array_unique(array_filter(array_column($records, 'sector_no'))));
        $districts = array_values(array_unique(array_filter(array_column($records, 'district_name'))));
        $towns = array_values(array_unique(array_filter(array_column($records, 'town_name'))));

        $approvedCount = collect($allFlats)->where('is_aproved_by_dtp', 1)->count();
        $pendingCount = count($allFlats) - $approvedCount;
        $approvalRate = count($allFlats) > 0 ? round(($approvedCount / count($allFlats)) * 100, 1) : 0;

        $summary = [
            'license_no' => $licenseNo,
            'colonizers' => $colonizers,
            'sectors' => $sectors,
            'districts' => $districts,
            'towns' => $towns,
            'total_records' => count($records),
            'total_flats' => count($allFlats),
            'approved_count' => $approvedCount,
            'pending_count' => $pendingCount,
            'approval_rate' => $approvalRate,
        ];

        return response()->json([
            'success' => true,
            'license_no' => $licenseNo,
            'total_records' => count($records),
            'total_flats' => count($allFlats),
            'approved_count' => $approvedCount,
            'pending_count' => $pendingCount,
            'approval_rate' => $approvalRate,
            'count' => count($allFlats),
            'meta' => $records[0] ?? null,
            'summary' => $summary,
            'records' => $records,
            'flats' => $allFlats,
            'all_flats' => $allFlats,
        ]);
    }

    /**
     * Direct DTP Approval API (Single Click Action with Audit Logging)
     */
    public function approveDtp(Request $request, $secureId = null)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp', 'mmgay-dtp', 'ews_tcp', 'tcp'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action for DTP authority.'], 403);
        }

        $id = $secureId ?: $request->input('id') ?: $request->input('secure_id');
        $flat = EwsBuilderFlat::where('id', $id)
            ->orWhere('secure_id', $id)
            ->first();

        if (!$flat && is_numeric(EwsHelper::decodeSecureId($id))) {
            $flat = EwsBuilderFlat::find(EwsHelper::decodeSecureId($id));
        }

        if (!$flat) {
            return response()->json(['success' => false, 'message' => 'Flat record not found.'], 404);
        }

        $this->ensureDtpApprovalColumnsExist();
        $this->ensureAuditTableExists();

        $oldStatus = (int)($flat->is_aproved_by_dtp ?? $flat->is_approved_by_dtp ?? 0);
        $newStatus = (int)$request->input('status', 1);

        $flat->is_aproved_by_dtp = $newStatus;
        $flat->is_approved_by_dtp = $newStatus;
        $flat->dtp_approved_at = $newStatus === 1 ? now() : null;
        $flat->dtp_approved_by = $newStatus === 1 ? $user->id : null;
        $flat->dtp_user_name = $newStatus === 1 ? $user->name : null;
        $flat->dtp_approval_remarks = $request->input('remarks', $newStatus === 1 ? 'Approved and Certified by DTP Competent Authority' : 'DTP Approval Revoked');
        $flat->save();

        // Audit Logging
        $action = $newStatus === 1 ? 'DTP_APPROVED' : 'DTP_APPROVAL_REVOKED';
        $summary = $newStatus === 1
            ? "Flat certified and marked APPROVED by DTP Officer: {$user->name} (" . strtoupper($user->role ?? 'DTP') . ")"
            : "Flat DTP approval revoked by {$user->name}";

        try {
            EwsFlatAuditLog::create([
                'flat_id' => $flat->id,
                'flat_code' => $flat->flat_code,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role ?? 'ews_stp',
                'action' => $action,
                'summary' => $summary,
                'changed_fields' => [
                    'is_aproved_by_dtp' => [
                        'label' => 'DTP Approval Status',
                        'old' => $oldStatus === 1 ? 'Approved (1)' : 'Pending (0)',
                        'new' => $newStatus === 1 ? 'Approved (1)' : 'Pending (0)',
                    ]
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        try {
            EwsDeveloperLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'details' => "Flat {$flat->flat_number} ({$flat->flat_code}): {$summary}",
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'is_aproved_by_dtp' => $newStatus,
            'dtp_approved_at' => $flat->dtp_approved_at ? $flat->dtp_approved_at->format('d M, Y H:i') : null,
            'dtp_user_name' => $flat->dtp_user_name,
            'message' => $newStatus === 1
                ? "Flat Unit {$flat->flat_number} ({$flat->flat_code}) officially APPROVED by DTP."
                : "Flat Unit {$flat->flat_number} approval revoked to Pending status."
        ]);
    }
}
