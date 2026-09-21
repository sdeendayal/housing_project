<?php

namespace App\Http\Controllers;

use App\Models\EwsBuilderFlat;
use App\Models\EwsDeveloperLog;
use App\Models\EwsProject;
use App\Models\EwsBlock;
use App\Models\EwsTown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\EwsHelper;

class EwsDeveloperDashboardController extends Controller
{
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
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403, 'Unauthorized access to Developer dashboard.');
        }

        $displayZoneName = $this->resolveDisplayZoneName($user);

        $zoneDistrictIds = [];
        if (!empty($user->zone_id)) {
            $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $user->zone_id)->pluck('id')->toArray();
        }

        $userDist = !empty($user->district_name) ? strtoupper(trim($user->district_name)) : null;

        // Zone / District Flats Query
        $districtFlatsQuery = EwsBuilderFlat::query();
        if (!empty($user->zone_id)) {
            $districtFlatsQuery->where(function ($q) use ($user, $zoneDistrictIds) {
                $q->where('zone_id', $user->zone_id);
                if (!empty($zoneDistrictIds)) {
                    $q->orWhereIn('district_id', $zoneDistrictIds);
                }
            });
        } elseif ($userDist) {
            $districtFlatsQuery->where(function ($q) use ($user, $userDist) {
                $q->where('district_name', $userDist)
                  ->orWhere('district_name', $user->district_name);
                if (!empty($user->district_id)) {
                    $q->orWhere('district_id', $user->district_id);
                }
            });
        }

        // My Flats Query
        $myFlatsQuery = EwsBuilderFlat::where('created_by', $user->id);

        // Project Breakdown in District
        $projectBreakdown = (clone $districtFlatsQuery)
            ->select('town_name', 'project_name', DB::raw('count(*) as total_flats'), DB::raw('count(distinct block_tower_number) as towers_count'))
            ->groupBy('town_name', 'project_name')
            ->orderBy('total_flats', 'desc')
            ->get();

        // Recent Activity Logs
        $recentLogs = EwsDeveloperLog::where('user_id', $user->id)->latest()->take(5)->get();

        $stats = [
            'total_flats' => (clone $districtFlatsQuery)->count(),
            'my_flats' => (clone $myFlatsQuery)->count(),
            'total_projects' => !empty($zoneDistrictIds) 
                ? EwsProject::whereIn('district_id', $zoneDistrictIds)->count()
                : (!empty($user->district_id) ? EwsProject::where('district_id', $user->district_id)->count() : EwsProject::count()),
            'total_towns' => !empty($zoneDistrictIds) 
                ? EwsTown::whereIn('district_id', $zoneDistrictIds)->count()
                : (!empty($user->district_id) ? EwsTown::where('district_id', $user->district_id)->count() : EwsTown::count()),
            'total_logs' => EwsDeveloperLog::where('user_id', $user->id)->count(),
        ];

        $currentView = $request->query('view', 'dashboard');

        $projectsList = !empty($zoneDistrictIds) 
            ? EwsProject::whereIn('district_id', $zoneDistrictIds)->orderBy('name')->get()
            : (!empty($user->district_id) ? EwsProject::where('district_id', $user->district_id)->orderBy('name')->get() : EwsProject::orderBy('name')->get());
        $townsList = !empty($zoneDistrictIds) 
            ? EwsTown::whereIn('district_id', $zoneDistrictIds)->orderBy('name')->get()
            : (!empty($user->district_id) ? EwsTown::where('district_id', $user->district_id)->orderBy('name')->get() : EwsTown::orderBy('name')->get());

        return view('ews.developer.dashboard', compact('user', 'stats', 'projectBreakdown', 'recentLogs', 'currentView', 'projectsList', 'townsList', 'displayZoneName'));
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
            } elseif ($request->filled('district_id')) {
                $query->where('district_id', $request->district_id);
            }
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
                  ->orWhere('block_tower_number', 'like', "%{$searchValue}%")
                  ->orWhere('floor', 'like', "%{$searchValue}%")
                  ->orWhere('flat_number', 'like', "%{$searchValue}%");
            });
        }

        return $query->orderBy('id', 'desc');
    }

    public function getFlatsData(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
        }

        $query = $this->getFilteredQuery($request);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('added_by', function ($row) use ($user) {
                if ($row->created_by == $user->id) {
                    return '<span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[9px] font-black uppercase inline-flex items-center gap-1 shadow-sm"><i class="bi bi-person-check-fill"></i> Added By Me</span>';
                }
                return '<span class="px-2 py-0.5 bg-slate-100 text-slate-600 border border-slate-200 rounded text-[9px] font-black uppercase inline-flex items-center gap-1"><i class="bi bi-building"></i> District Record</span>';
            })
            ->addColumn('actions', function ($row) {
                $secureId = !empty($row->secure_id) ? $row->secure_id : EwsHelper::encodeSecureId($row->id);
                $editUrl = route('ews.developer.flats.edit', $secureId);
                $destroyRoute = route('ews.developer.flats.destroy', $secureId);
                $csrf = csrf_field();
                $method = method_field('DELETE');

                return '
                    <div class="inline-flex gap-1.5 justify-end w-full">
                        <a href="'.$editUrl.'"
                            class="px-2.5 py-1.5 bg-sky-50 hover:bg-sky-500 hover:text-white text-sky-600 rounded-lg text-[9px] font-black uppercase transition-all flex items-center gap-0.5 border border-sky-100 shadow-sm">
                            <i class="bi bi-pencil-square"></i>
                            <span>Edit</span>
                        </a>
                        <form action="'.$destroyRoute.'" method="POST" class="inline m-0" id="delete-form-'.$secureId.'">
                            '.$csrf.'
                            '.$method.'
                            <button type="button" onclick="confirmDelete(\''.$secureId.'\')"
                                class="px-2.5 py-1.5 bg-red-50 hover:bg-red-500 hover:text-white text-red-500 rounded-lg text-[9px] font-black uppercase transition-all flex items-center gap-0.5 border border-red-100 shadow-sm">
                                <i class="bi bi-trash3"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['added_by', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
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

        // Pre-select user's preferred district if inside zone, else first district
        $selectedDistrictId = null;
        if (!empty($user->district_name)) {
            $cleanUserDist = strtoupper(trim(str_replace(' ZONE', '', $user->district_name)));
            $matched = $districts->firstWhere('name', $cleanUserDist);
            if ($matched) {
                $selectedDistrictId = $matched->id;
            }
        }
        if (!$selectedDistrictId && $districts->isNotEmpty()) {
            $selectedDistrictId = $districts->first()->id;
        }

        $towns = collect();
        if ($selectedDistrictId) {
            $towns = EwsTown::where('district_id', $selectedDistrictId)->orderBy('name', 'asc')->get();
        }
        $townTypes = EwsTown::whereNotNull('type')->where('type', '!=', '')->distinct()->pluck('type')->sort()->values();
        if ($townTypes->isEmpty()) {
            $townTypes = collect(['Municipal Corporation', 'Municipal Council', 'Municipal Committee']);
        }
        $displayZoneName = $this->resolveDisplayZoneName($user);

        return view('ews.developer.create', compact('user', 'zone', 'districts', 'towns', 'townTypes', 'selectedDistrictId', 'displayZoneName'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
        }

        // Validate basic parameters
        $request->validate([
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

            $town = EwsTown::where('district_id', $district->id)
                ->whereRaw('LOWER(name) = ?', [strtolower($cleanTownName)])
                ->first();
            if (!$town) {
                $town = EwsTown::create([
                    'district_id' => $district->id,
                    'zone_id'     => $zoneId,
                    'zone_name'   => $zoneName,
                    'name'        => $cleanTownName,
                    'type'        => !empty($townType) ? $townType : null,
                ]);
            } else {
                $updateData = [];
                if (!empty($townType) && empty($town->type)) {
                    $updateData['type'] = $townType;
                }
                if (!empty($zoneId) && empty($town->zone_id)) {
                    $updateData['zone_id'] = $zoneId;
                    $updateData['zone_name'] = $zoneName;
                }
                if (!empty($updateData)) {
                    $town->update($updateData);
                }
            }
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
            $projectExists = EwsProject::where('district_id', $district->id)
                ->where('town_id', $townId)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->new_project_name))])
                ->exists();
            if ($projectExists) {
                return back()->withInput()->with('error', "Validation Error: A project named '{$request->new_project_name}' already exists in this town. Please select it from the list instead of adding it as a new project.");
            }

            $project = EwsProject::create([
                'zone_id' => $zoneId,
                'zone_name' => $zoneName,
                'district_id' => $district->id,
                'district_name' => $district->name,
                'town_id' => $townId,
                'town_name' => $townName,
                'name' => trim($request->new_project_name),
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
            $blockExists = EwsBlock::where('project_id', $projectId)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->new_block_name))])
                ->exists();
            if ($blockExists) {
                return back()->withInput()->with('error', "Validation Error: A block/tower named '{$request->new_block_name}' already exists under the selected project. Please select it from the list.");
            }

            $block = EwsBlock::firstOrCreate([
                'project_id' => $projectId,
                'name' => trim($request->new_block_name),
            ]);
            $blockId = $block->id;
            $blockName = $block->name;
        } else {
            $block = EwsBlock::where('project_id', $projectId)->where('id', $request->block_id)->firstOrFail();
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

            return redirect()->route('ews.developer.dashboard')->with('success', "Bulk Registry successfully generated and added {$createdCount} EWS flats.");
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

        return redirect()->route('ews.developer.dashboard')->with('success', 'EWS Builder Flat record created successfully.');
    }

    public function edit($secureId)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
        }

        $flat = EwsBuilderFlat::where('secure_id', $secureId)->firstOrFail();

        // District-wise edit check
        if (!empty($user->district_name)) {
            $userDist = strtoupper(trim($user->district_name));
            $flatDist = strtoupper(trim($flat->district_name));
            if ($userDist !== $flatDist && $user->district_id != $flat->district_id && $flat->created_by != $user->id) {
                abort(403, 'Unauthorized action for this district.');
            }
        }

        if (!empty($user->district_name)) {
            $userDist = strtoupper(trim($user->district_name));
            $districts = DB::table('ews_districts')
                ->where('name', $userDist)
                ->orWhere('id', $user->district_id)
                ->orderBy('name', 'asc')
                ->get();
            if ($districts->isEmpty()) {
                $districts = DB::table('ews_districts')->orderBy('name', 'asc')->get();
            }
        } else {
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

        return view('ews.developer.edit', compact('user', 'flat', 'zone', 'districts', 'secureId', 'towns', 'townTypes', 'projects', 'blocks', 'displayZoneName'));
    }

    public function update(Request $request, $secureId)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
        }

        $flat = EwsBuilderFlat::where('secure_id', $secureId)->firstOrFail();

        // Zone-wise update authorization check
        if (!empty($user->zone_id)) {
            $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $user->zone_id)->pluck('id')->toArray();
            $allowed = ($flat->zone_id == $user->zone_id) || in_array($flat->district_id, $zoneDistrictIds) || ($flat->created_by == $user->id);
            if (!$allowed) {
                abort(403, 'Unauthorized action for this zone.');
            }
        } elseif (!empty($user->district_name)) {
            $userDist = strtoupper(trim($user->district_name));
            $flatDist = strtoupper(trim($flat->district_name));
            if ($userDist !== $flatDist && $user->district_id != $flat->district_id && $flat->created_by != $user->id) {
                abort(403, 'Unauthorized action for this district.');
            }
        }

        $request->validate([
            'district_id' => 'required|exists:ews_districts,id',
            'town_id' => 'required',
            'new_town_name' => 'required_if:town_id,new|nullable|string|max:255',
            'new_town_type' => 'required_if:town_id,new|nullable|string|max:255',
            'custom_town_type' => 'required_if:new_town_type,other|nullable|string|max:255',
            'project_id' => 'required',
            'new_project_name' => 'required_if:project_id,new|nullable|string|max:255',
            'block_id' => 'required',
            'new_block_name' => 'required_if:block_id,new|nullable|string|max:255',
            'floor_number' => 'required|integer|min:0|max:100',
            'flat_number' => 'required|string|max:255',
        ]);

        $district = DB::table('ews_districts')->where('id', $request->district_id)->first();

        if (!empty($user->zone_id)) {
            if ($district->zone_id != $user->zone_id) {
                return back()->withInput()->with('error', "Unauthorized: You can only update flats for districts within your assigned zone.");
            }
        } elseif (!empty($user->district_name)) {
            $userDist = strtoupper(trim($user->district_name));
            $selectedDist = strtoupper(trim($district->name));
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

        // Resolve Town ID and Name from master ews_towns table
        if ($request->town_id === 'new') {
            $cleanTownName = trim($request->new_town_name);
            $townType = $request->new_town_type === 'other' ? trim($request->custom_town_type ?? '') : trim($request->new_town_type ?? '');

            $town = EwsTown::where('district_id', $district->id)
                ->whereRaw('LOWER(name) = ?', [strtolower($cleanTownName)])
                ->first();
            if (!$town) {
                $town = EwsTown::create([
                    'district_id' => $district->id,
                    'zone_id'     => $zoneId,
                    'zone_name'   => $zoneName,
                    'name'        => $cleanTownName,
                    'type'        => !empty($townType) ? $townType : null,
                ]);
            } else {
                $updateData = [];
                if (!empty($townType) && empty($town->type)) {
                    $updateData['type'] = $townType;
                }
                if (!empty($zoneId) && empty($town->zone_id)) {
                    $updateData['zone_id'] = $zoneId;
                    $updateData['zone_name'] = $zoneName;
                }
                if (!empty($updateData)) {
                    $town->update($updateData);
                }
            }
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
            $projectExists = EwsProject::where('district_id', $district->id)
                ->where('town_id', $townId)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->new_project_name))])
                ->exists();
            if ($projectExists) {
                return back()->withInput()->with('error', "Validation Error: A project named '{$request->new_project_name}' already exists in this town. Please select it from the list instead of adding it as a new project.");
            }

            $project = EwsProject::create([
                'zone_id' => $zoneId,
                'zone_name' => $zoneName,
                'district_id' => $district->id,
                'district_name' => $district->name,
                'town_id' => $townId,
                'town_name' => $townName,
                'name' => trim($request->new_project_name),
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
            $blockExists = EwsBlock::where('project_id', $projectId)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->new_block_name))])
                ->exists();
            if ($blockExists) {
                return back()->withInput()->with('error', "Validation Error: A block/tower named '{$request->new_block_name}' already exists under the selected project. Please select it from the list.");
            }

            $block = EwsBlock::firstOrCreate([
                'project_id' => $projectId,
                'name' => trim($request->new_block_name),
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
        $zoneId = $zone ? $zone->id : ($flat->zone_id ?? null);
        $zoneName = $zone ? $zone->name . ' ZONE' : ($flat->zone_name ?? ($district ? $district->name . ' ZONE' : 'N/A'));

        $validatedData = [
            'zone_id' => $zoneId,
            'zone_name' => $zoneName,
            'district_id' => $district->id,
            'district_name' => $district->name,
            'town_name' => $townName,
            'town_id' => $townId,
            'project_name' => $projectName,
            'project_id' => $projectId,
            'block_tower_number' => $blockName,
            'block_id' => $blockId,
            'floor' => $floorLabel,
            'flat_number' => $request->flat_number,
            'flat_code' => EwsHelper::generateFlatCode(
                $townName,
                $user->name,
                $floorLabel,
                $blockName,
                $request->flat_number
            )
        ];

        $oldDetails = "Flat: {$flat->flat_number}, Floor: {$flat->floor}, Tower: {$flat->block_tower_number} under Project '{$flat->project_name}' in {$flat->town_name} ({$flat->district_name})";
        if (!empty($district->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $district->zone_id)->first();
        }
        if (!$zone && !empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        if (!$zone && !empty($user->zone_name)) {
            $cleanZoneName = strtoupper(trim(str_replace(' ZONE', '', $user->zone_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanZoneName)->first();
        }
        if (!$zone && !empty($user->district_name)) {
            $cleanDist = strtoupper(trim(str_replace(' ZONE', '', $user->district_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanDist)->first();
        }
        $zoneId = $zone->id ?? ($user->zone_id ?? null);
        $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : (!empty($user->zone_name) ? strtoupper($user->zone_name) : null);

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

        $flat->save();

        // Update activity log
        EwsDeveloperLog::create([
            'user_id' => $user->id,
            'action' => 'UPDATED',
            'details' => "Updated EWS Flat [Flat: {$flat->flat_number}, Tower: {$flat->block_tower_number}] under Project '{$flat->project_name}' in {$flat->town_name} ({$flat->district_name})",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('ews.developer.dashboard')->with('success', 'EWS Builder Flat record updated successfully.');
    }

    public function destroy(Request $request, $secureId)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
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
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
        }

        $displayZoneName = $this->resolveDisplayZoneName($user);

        // Fetch logs with pagination
        $logs = EwsDeveloperLog::with('developer')
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('ews.developer.logs', compact('user', 'logs', 'displayZoneName'));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
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
            fputcsv($file, ['S.No.', 'District Name', 'Town Name', 'Project Name', 'Block/Tower No.', 'Floor Details', 'Flat No.', 'Registered At']);

            foreach ($flats as $index => $flat) {
                fputcsv($file, [
                    $index + 1,
                    $flat->district_name,
                    $flat->town_name,
                    $flat->project_name,
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
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
        }

        // Respect search and custom district filters
        $flats = $this->getFilteredQuery($request)->get();

        $pdf = Pdf::loadView('ews.developer.pdf_report', compact('flats'));
        return $pdf->download('ews_builder_flats_' . date('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
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
            fputs($file, "S.No.\tDistrict Name\tTown Name\tProject Name\tBlock/Tower No.\tFloor Details\tFlat No.\tRegistered At\n");

            foreach ($flats as $index => $flat) {
                fputs($file, ($index + 1) . "\t" .
                    $flat->district_name . "\t" .
                    $flat->town_name . "\t" .
                    $flat->project_name . "\t" .
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
        return redirect()->route('ews.developer.dashboard');
    }

    public function getProjects(Request $request)
    {
        $districtId = $request->query('district_id');
        if (!$districtId) {
            return response()->json([]);
        }

        // Return all projects for the selected district (district-wise project master)
        $projects = EwsProject::where('district_id', $districtId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'project_abbr', 'town_id', 'town_name']);

        return response()->json($projects);
    }

    public function getBlocks(Request $request)
    {
        $projectId = $request->query('project_id');
        if (!$projectId) {
            return response()->json([]);
        }
        $blocks = EwsBlock::where('project_id', $projectId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
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
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
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

        $exists = EwsTown::where('district_id', $districtId)
            ->whereRaw('LOWER(name) = ?', [strtolower($cleanName)])
            ->first();

        if ($exists) {
            $updateData = [];
            if (!empty($cleanType) && empty($exists->type)) {
                $updateData['type'] = $cleanType;
            }
            if (!empty($zoneId) && empty($exists->zone_id)) {
                $updateData['zone_id'] = $zoneId;
                $updateData['zone_name'] = $zoneName;
            }
            if (!empty($updateData)) {
                $exists->update($updateData);
            }
            return response()->json([
                'success' => true,
                'message' => "Town '{$exists->name}' already exists.",
                'town' => [
                    'id' => $exists->id,
                    'name' => $exists->name,
                    'type' => $exists->type,
                    'zone_id' => $exists->zone_id,
                    'zone_name' => $exists->zone_name,
                ]
            ]);
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
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
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

        $existsQuery = EwsProject::where('district_id', $districtId)
            ->whereRaw('LOWER(name) = ?', [strtolower($cleanName)]);
        if ($townId) {
            $existsQuery->where('town_id', $townId);
        }
        $exists = $existsQuery->first();

        if ($exists) {
            return response()->json([
                'success' => true,
                'message' => "Project '{$exists->name}' already exists.",
                'project' => [
                    'id' => $exists->id,
                    'name' => $exists->name,
                ]
            ]);
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
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'project_id' => 'required|exists:ews_projects,id',
            'block_name' => 'required|string|max:255',
        ]);

        $projectId = (int)$request->project_id;
        $cleanName = trim($request->block_name);

        $exists = EwsBlock::where('project_id', $projectId)
            ->whereRaw('LOWER(name) = ?', [strtolower($cleanName)])
            ->first();

        if ($exists) {
            return response()->json([
                'success' => true,
                'message' => "Block/Tower '{$exists->name}' already exists.",
                'block' => [
                    'id' => $exists->id,
                    'name' => $exists->name,
                ]
            ]);
        }

        $block = EwsBlock::create([
            'project_id' => $projectId,
            'name' => $cleanName,
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
}
