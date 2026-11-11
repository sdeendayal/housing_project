<?php

namespace App\Http\Controllers;

use App\Models\EwsBuilderFlat;
use App\Models\EwsDeveloperLog;
use App\Models\EwsFlatAuditLog;
use App\Models\EwsProject;
use App\Models\EwsBlock;
use App\Models\EwsTown;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        // My Flats Query (developer's own registered flats)
        $myFlatsQuery = EwsBuilderFlat::where('created_by', $user->id);
        $myFlatsCount = (clone $myFlatsQuery)->count();

        // Zone Allotted Flats from ews_allotted_8 (out of 4,211 master pool)
        $zoneAllottedQuery = DB::table('ews_allotted_8');
        if (!empty($zoneDistrictIds)) {
            $zoneAllottedQuery->whereIn('dist_id', $zoneDistrictIds);
        } elseif ($userDist) {
            $zoneAllottedQuery->where('dist_name', 'like', "%{$userDist}%");
        }
        $zoneAllottedCount = (clone $zoneAllottedQuery)->count();
        $stateAllottedTotal = DB::table('ews_allotted_8')->count();

        // Fetch only the flats created by THIS developer (strictly what appears in the table)
        $myFlats = EwsBuilderFlat::where('created_by', $user->id)
            ->whereNotNull('district_id')
            ->get(['district_id', 'district_name', 'town_id', 'town_name', 'project_id', 'project_name']);

        // 1. Districts present in developer's flats (frontend table)
        $myDistrictIds = $myFlats->pluck('district_id')->filter()->unique()->values()->toArray();
        $districtsDb = !empty($myDistrictIds)
            ? DB::table('ews_districts')->whereIn('id', $myDistrictIds)->get()->keyBy('id')
            : collect();

        $districts = collect($myDistrictIds)->map(function($distId) use ($districtsDb, $myFlats) {
            $dbDist = $districtsDb->get($distId);
            $flatDist = $myFlats->firstWhere('district_id', $distId);
            $name = $dbDist->name ?? $flatDist->district_name ?? ('District #' . $distId);
            return (object)[
                'id' => (string)$distId,
                'name' => strtoupper(trim($name))
            ];
        })->unique('id')->sortBy('name')->values();

        // 2. Towns present in developer's flats (frontend table)
        $myTownIds = $myFlats->pluck('town_id')->filter()->unique()->values()->toArray();
        $townsDb = !empty($myTownIds)
            ? EwsTown::whereIn('id', $myTownIds)->get()->keyBy('id')
            : collect();

        $townsList = $myFlats->filter(fn($f) => !empty($f->town_id))->map(function($f) use ($townsDb) {
            $dbTown = $townsDb->get($f->town_id);
            $name = $dbTown->name ?? $f->town_name ?? ('Town #' . $f->town_id);
            return (object)[
                'id' => (string)$f->town_id,
                'name' => strtoupper(trim($name)),
                'district_id' => (string)$f->district_id
            ];
        })->unique('id')->sortBy('name')->values();

        // 3. Projects present in developer's flats (frontend table)
        $myProjectIds = $myFlats->pluck('project_id')->filter()->unique()->values()->toArray();
        $projectsDb = !empty($myProjectIds)
            ? EwsProject::whereIn('id', $myProjectIds)->get()->keyBy('id')
            : collect();

        $projectsList = $myFlats->filter(fn($f) => !empty($f->project_id))->map(function($f) use ($projectsDb) {
            $dbProj = $projectsDb->get($f->project_id);
            $name = $dbProj->name ?? $f->project_name ?? ('Project #' . $f->project_id);
            return (object)[
                'id' => (string)$f->project_id,
                'name' => strtoupper(trim($name)),
                'district_id' => (string)$f->district_id,
                'town_id' => (string)$f->town_id
            ];
        })->unique('id')->sortBy('name')->values();

        $stats = [
            'total_allotted' => $zoneAllottedCount,
            'state_allotted_total' => $stateAllottedTotal,
            'total_flats' => $myFlatsCount,
            'my_flats' => $myFlatsCount,
            'total_projects' => $projectsList->count(),
            'total_towns' => $townsList->count(),
            'total_logs' => EwsDeveloperLog::where('user_id', $user->id)->count(),
        ];

        $currentView = $request->query('view', 'dashboard');

        // Build hierarchical tree for frontend cascading
        $developerFilterTree = [
            'districts' => $districts->map(fn($d) => ['id' => (string)$d->id, 'name' => strtoupper($d->name)])->values(),
            'towns' => $townsList->map(fn($t) => ['id' => (string)$t->id, 'name' => strtoupper($t->name), 'district_id' => (string)$t->district_id])->values(),
            'projects' => $projectsList->map(fn($p) => ['id' => (string)$p->id, 'name' => strtoupper($p->name), 'district_id' => (string)$p->district_id, 'town_id' => (string)$p->town_id])->values(),
        ];

        return view('ews.developer.dashboard', compact('user', 'stats', 'currentView', 'districts', 'projectsList', 'townsList', 'displayZoneName', 'developerFilterTree'));
    }

    /**
     * Helper to get registry query with applied search & district filters.
     */
    private function getFilteredQuery(Request $request)
    {
        $user = Auth::user();
        // Strictly show only flats registered by this developer
        $query = EwsBuilderFlat::where('created_by', $user->id);

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

        // Check if viewing Zone Allotted Flats (from 4,211 master pool)
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

            // Search filter
            $searchValue = '';
            if ($request->has('search')) {
                $searchParam = $request->search;
                $searchValue = is_array($searchParam) ? ($searchParam['value'] ?? '') : $searchParam;
            }
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('application_number', 'like', "%{$searchValue}%")
                      ->orWhere('full_name', 'like', "%{$searchValue}%")
                      ->orWhere('mobile_number', 'like', "%{$searchValue}%")
                      ->orWhere('flat_no', 'like', "%{$searchValue}%")
                      ->orWhere('dist_name', 'like', "%{$searchValue}%");
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('district_name', fn($r) => '<span class="font-bold text-slate-900 uppercase">' . $r->dist_name . '</span>')
                ->addColumn('town_name', fn($r) => '<span class="text-slate-400 font-mono text-[10px]">Zone Allotted</span>')
                ->addColumn('project_name', function($r) {
                    $parts = explode('-', $r->flat_no);
                    return count($parts) > 1 ? '<span class="text-slate-700 font-bold">' . $parts[1] . '</span>' : '<span class="text-slate-600 font-bold">EWS Flat</span>';
                })
                ->addColumn('block_tower_number', fn($r) => '<span class="text-indigo-600 font-mono font-bold">App #' . $r->application_number . '</span>')
                ->addColumn('floor', fn($r) => '<span class="font-bold text-slate-800">' . $r->full_name . '</span>')
                ->addColumn('flat_number', fn($r) => '<span class="text-violet-700 font-black font-mono">' . $r->flat_no . '</span>')
                ->addColumn('flat_code', fn($r) => '<span class="text-emerald-700 font-bold font-mono">' . ($r->mobile_number ? substr($r->mobile_number, 0, 2) . '******' . substr($r->mobile_number, -2) : 'N/A') . '</span>')
                ->addColumn('added_by', function($r) {
                    $isGiven = ($r->is_possession_given == 1 || $r->possession_status === 'Possession Given');
                    $status = $isGiven ? 'Possession Given' : ($r->possession_status ?: 'Allotted');
                    $color = $isGiven ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200';
                    return '<span class="px-2 py-0.5 ' . $color . ' border rounded text-[9px] font-black uppercase inline-flex items-center gap-1"><i class="bi bi-houses"></i> ' . $status . '</span>';
                })
                ->addColumn('actions', function($r) {
                    return '<div class="inline-flex justify-end w-full"><span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-bold uppercase border border-slate-200"><i class="bi bi-info-circle me-1"></i> Record</span></div>';
                })
                ->rawColumns(['district_name', 'town_name', 'project_name', 'block_tower_number', 'floor', 'flat_number', 'flat_code', 'added_by', 'actions'])
                ->make(true);
        }

        $query = $this->getFilteredQuery($request);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('district_name', fn($r) => strtoupper($r->district_name ?? ''))
            ->editColumn('town_name', fn($r) => ucwords(strtolower($r->town_name ?? '')))
            ->editColumn('project_name', fn($r) => ucwords(strtolower($r->project_name ?? '')))
            ->editColumn('block_tower_number', fn($r) => strtoupper($r->block_tower_number ?? ''))
            ->editColumn('floor', fn($r) => ucfirst($r->floor ?? ''))
            ->addColumn('actions', function ($row) {
                $secureId = !empty($row->secure_id) ? $row->secure_id : EwsHelper::encodeSecureId($row->id);
                $viewUrl = route('ews.developer.flats.show', $secureId);

                return '
                    <div class="inline-flex justify-end w-full">
                        <a href="'.$viewUrl.'" title="View Flat Details"
                            class="w-8 h-8 rounded-lg bg-sky-50 hover:bg-sky-600 text-sky-600 hover:text-white border border-sky-200/80 flex items-center justify-center transition-all shadow-2xs hover:shadow-sm group">
                            <i class="bi bi-eye-fill text-sm transition-transform group-hover:scale-115"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Show flat comprehensive dossier details on a dedicated page
     */
    public function show($secureId)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
        }

        $flat = EwsBuilderFlat::where('secure_id', $secureId)->first();
        if (!$flat && is_numeric($secureId)) {
            $flat = EwsBuilderFlat::find($secureId);
        }

        if (!$flat) {
            abort(404, 'Flat record not found.');
        }

        // Must belong to this developer
        if ($flat->created_by != $user->id) {
            abort(403, 'Unauthorized access to this flat record.');
        }

        $project = !empty($flat->project_id) ? EwsProject::find($flat->project_id) : null;
        $displayZoneName = !empty($flat->zone_name) ? $flat->zone_name : ($user->district_name ?? 'Zone');

        return view('ews.developer.show', compact('user', 'flat', 'project', 'displayZoneName'));
    }

    /**
     * Return comprehensive flat record details in JSON for modal view
     */
    public function showDetails($secureId)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $flat = EwsBuilderFlat::where('secure_id', $secureId)->first();
        if (!$flat && is_numeric($secureId)) {
            $flat = EwsBuilderFlat::find($secureId);
        }

        if (!$flat) {
            return response()->json(['success' => false, 'message' => 'Flat record not found.'], 404);
        }

        // Project fallback if any field missing
        $project = !empty($flat->project_id) ? EwsProject::find($flat->project_id) : null;

        $data = [
            'id' => $flat->id,
            'secure_id' => $flat->secure_id,
            'flat_code' => $flat->flat_code ?: 'N/A',
            'flat_number' => $flat->flat_number ?: 'N/A',
            'floor' => $flat->floor ?: 'N/A',
            'block_tower_number' => $flat->block_tower_number ?: 'N/A',
            'register_flat' => $flat->register_flat ? ucfirst(str_replace('_', ' ', $flat->register_flat)) : 'Standard EWS Flat',
            'zone_name' => $flat->zone_name ?: ($project->zone_name ?? 'N/A'),
            'district_name' => $flat->district_name ?: ($project->district_name ?? 'N/A'),
            'town_name' => $flat->town_name ?: ($project->town_name ?? 'N/A'),
            'project_name' => $flat->project_name ?: ($project->name ?? 'N/A'),
            'sector_no' => $flat->sector_no ?: ($project->sector_no ?? 'N/A'),
            'address_of_project' => $flat->address_of_project ?: ($project->address ?? 'N/A'),
            'area_of_project' => $flat->area_of_project ?: ($project->area_of_project ?? 'N/A'),
            'developer_name' => $flat->developer_name ?: ($project->developer_name ?? $user->name),
            'license_no' => $flat->license_no ?: ($project->license_no ?? 'N/A'),
            
            // Planning & Approvals
            'approved_building_plan' => strtolower($flat->approved_building_plan ?: ($project->approved_building_plan ?? 'no')),
            'building_plan_approval_date' => $flat->building_plan_approval_date ? \Carbon\Carbon::parse($flat->building_plan_approval_date)->format('d M, Y') : ($project && $project->building_plan_approval_date ? \Carbon\Carbon::parse($project->building_plan_approval_date)->format('d M, Y') : 'N/A'),
            'developer_plan_url' => ($flat->developer_plan ?: ($project->developer_plan ?? null)) ? asset('storage/' . ($flat->developer_plan ?: $project->developer_plan)) : null,
            'building_plan_copy_url' => ($flat->building_plan_copy ?: ($project->building_plan_copy ?? null)) ? asset('storage/' . ($flat->building_plan_copy ?: $project->building_plan_copy)) : null,

            // Occupation Certificate (OC) Details
            'oc_status_yn' => strtolower($flat->oc_status_yn ?: ($project->oc_status_yn ?? 'no')),
            'oc_status' => $flat->oc_status ? ucfirst($flat->oc_status) : 'N/A',
            'oc_date' => $flat->oc_date ? \Carbon\Carbon::parse($flat->oc_date)->format('d M, Y') : 'N/A',
            'oc_copy_url' => $flat->oc_copy_file ? asset('storage/' . $flat->oc_copy_file) : null,

            // Project Flat Inventory Metrics
            'total_flats_in_project' => $flat->total_flats_in_project !== null ? number_format($flat->total_flats_in_project) : ($project && $project->total_flats_in_project ? number_format($project->total_flats_in_project) : 'N/A'),
            'total_ews_flats_in_project' => $flat->total_ews_flats_in_project !== null ? number_format($flat->total_ews_flats_in_project) : ($project && $project->total_ews_flats_in_project ? number_format($project->total_ews_flats_in_project) : 'N/A'),
            'flats_offered_for_draw' => $flat->flats_offered_for_draw !== null ? number_format($flat->flats_offered_for_draw) : 'N/A',
            'remaining_flats_for_oc' => $flat->remaining_flats_for_oc !== null ? number_format($flat->remaining_flats_for_oc) : 'N/A',

            // Timestamp
            'created_at' => $flat->created_at ? $flat->created_at->format('d M, Y h:i A') : 'N/A',
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
        }

        // Fetch all active zones from ews_stp_districts
        $zones = DB::table('ews_stp_districts')
            ->where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();
        if ($zones->isEmpty()) {
            $zones = DB::table('ews_stp_districts')->orderBy('name', 'asc')->get();
        }

        $zone = null;
        if (!empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }

        $districts = $zone 
            ? DB::table('ews_districts')->where('zone_id', $zone->id)->orderBy('name', 'asc')->get()
            : DB::table('ews_districts')->orderBy('name', 'asc')->get();

        $selectedZoneId = null;
        $selectedDistrictId = null;
        $towns = collect();
        $projects = collect();
        $townTypes = EwsTown::whereNotNull('type')->where('type', '!=', '')->distinct()->pluck('type')->sort()->values();
        if ($townTypes->isEmpty()) {
            $townTypes = collect(['Municipal Corporation', 'Municipal Council', 'Municipal Committee']);
        }
        $displayZoneName = $this->resolveDisplayZoneName($user);

        return view('ews.developer.create', compact('user', 'zone', 'zones', 'selectedZoneId', 'districts', 'towns', 'projects', 'townTypes', 'selectedDistrictId', 'displayZoneName'));
    }

    public function getDistricts(Request $request)
    {
        $zoneId = $request->query('zone_id');
        $query = DB::table('ews_districts');
        if ($zoneId) {
            $query->where('zone_id', $zoneId);
        }
        $districts = $query->orderBy('name', 'asc')->get(['id', 'name', 'zone_id']);
        return response()->json($districts);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
        }

        if ($request->input('approved_building_plan') === 'no') {
            return back()->withInput()->with('error', "Validation Error: Approved Building Plan is mandatory for registering flats. Without approval, flats cannot be registered.");
        }
        if ($request->input('oc_status_yn') === 'no') {
            return back()->withInput()->with('error', "Validation Error: Occupation Certificate (OC) is mandatory to offer flats for allotment.");
        }

        // Validate basic parameters
        $request->validate([
            'register_flat' => 'nullable|string|max:255',
            'district_id' => 'required|exists:ews_districts,id',
            'zone_id' => 'nullable',
            'developer_name' => 'required|string|max:255',
            'license_no' => 'required|string|max:150',
            'town_id' => 'required',
            'new_town_name' => 'required_if:town_id,new|nullable|string|max:255',
            'new_town_type' => 'required_if:town_id,new|nullable|string|max:255',
            'custom_town_type' => 'required_if:new_town_type,other|nullable|string|max:255',
            'project_id' => 'required',
            'new_project_name' => 'required_if:project_id,new|nullable|string|max:255',
            'new_project_address' => 'nullable|string|max:500',
            'new_project_sector' => 'nullable|string|max:100',
            'address_of_project' => 'nullable|string|max:500',
            'sector_no' => 'nullable|string|max:100',
            'area_of_project' => 'required|string|max:150',
            'developer_plan' => 'nullable|file|mimes:pdf|max:512',
            'approved_building_plan' => 'required|in:yes,no',
            'building_plan_approval_date' => 'required_if:approved_building_plan,yes|nullable|date',
            'building_plan_copy' => 'nullable|file|mimes:pdf|max:10240',
            'oc_status_yn' => 'required|in:yes,no',
            'oc_status' => 'required_if:oc_status_yn,yes|nullable|in:part,complete',
            'block_id' => 'required',
            'new_block_name' => 'required_if:block_id,new|nullable|string|max:255',
            'oc_date' => 'nullable|date',
            'oc_copy' => 'nullable|file|mimes:pdf|max:10240',
            'total_flats_in_project' => 'nullable|numeric',
            'total_ews_flats_in_project' => 'nullable|numeric',
            'flats_offered_for_draw' => 'nullable|numeric',
            'remaining_flats_for_oc' => 'nullable|numeric',
        ]);

        $district = DB::table('ews_districts')->where('id', $request->district_id)->first();
        if (!$district) {
            return back()->withInput()->with('error', "Invalid district selected.");
        }

        if ($request->input('approved_building_plan') === 'no') {
            return back()->withInput()->with('error', "Validation Error: Approved Building Plan is mandatory for EWS flats. Without approval, flats cannot be registered.");
        }
        if ($request->input('oc_status_yn') === 'no') {
            return back()->withInput()->with('error', "Validation Error: Occupation Certificate (OC) is mandatory to offer flats for allotment.");
        }

        // Handle Developer Plan PDF upload (max 500KB)
        $developerPlanPath = null;
        if ($request->hasFile('developer_plan') && $request->file('developer_plan')->isValid()) {
            $file = $request->file('developer_plan');
            $filename = 'dev_plan_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('developer_plans', $filename, 'public');
            $developerPlanPath = $path;
        }

        // Handle Building Plan Copy PDF upload
        $buildingPlanCopyPath = null;
        if ($request->hasFile('building_plan_copy') && $request->file('building_plan_copy')->isValid()) {
            $file = $request->file('building_plan_copy');
            $filename = 'bldg_plan_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('building_plans', $filename, 'public');
            $buildingPlanCopyPath = $path;
        }

        // Handle OC PDF Document Upload
        $ocFilePath = null;
        if ($request->hasFile('oc_copy') && $request->file('oc_copy')->isValid()) {
            $file = $request->file('oc_copy');
            $filename = 'oc_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('oc_documents', $filename, 'public');
            $ocFilePath = $path;
        }

        // Resolve Zone
        $zone = null;
        if ($request->filled('zone_id')) {
            $zone = DB::table('ews_stp_districts')->where('id', $request->zone_id)->first();
        }
        if (!$zone && $district->zone_id) {
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
                'address' => $request->address_of_project ?? $request->new_project_address,
                'sector_no' => $request->sector_no ?? $request->new_project_sector,
                'developer_name' => $request->developer_name,
                'license_no' => $request->license_no,
                'area_of_project' => $request->area_of_project,
                'developer_plan' => $developerPlanPath,
                'approved_building_plan' => $request->approved_building_plan,
                'building_plan_approval_date' => $request->building_plan_approval_date,
                'building_plan_copy' => $buildingPlanCopyPath,
                'oc_status_yn' => $request->oc_status_yn,
            ]);
            $projectId = $project->id;
            $projectName = $project->name;
        } else {
            $project = EwsProject::where('district_id', $district->id)->where('id', $request->project_id)->firstOrFail();
            $project->update([
                'zone_id' => $project->zone_id ?? $zoneId,
                'zone_name' => $project->zone_name ?? $zoneName,
                'district_name' => $project->district_name ?? $district->name,
                'town_id' => $townId ?: $project->town_id,
                'town_name' => $townName ?: $project->town_name,
                'address' => $project->address ?? ($request->address_of_project ?? $request->new_project_address),
                'sector_no' => $project->sector_no ?? ($request->sector_no ?? $request->new_project_sector),
                'developer_name' => $project->developer_name ?? $request->developer_name,
                'license_no' => $project->license_no ?? $request->license_no,
                'area_of_project' => $project->area_of_project ?? $request->area_of_project,
                'developer_plan' => $developerPlanPath ?: $project->developer_plan,
                'approved_building_plan' => $project->approved_building_plan ?? $request->approved_building_plan,
                'building_plan_approval_date' => $project->building_plan_approval_date ?? $request->building_plan_approval_date,
                'building_plan_copy' => $buildingPlanCopyPath ?: $project->building_plan_copy,
                'oc_status_yn' => $project->oc_status_yn ?? $request->oc_status_yn,
            ]);
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
        $zoneName = $zone ? $zone->name . ' ZONE' : ($user->zone_name ?? ($district ? $district->name . ' ZONE' : 'ZONE'));

        $bulkMode = (string)$request->input('bulk_mode') === '1';

        // Sanitize floor_number in case multiple inputs were submitted
        $floorInput = $request->input('floor_number');
        if (is_array($floorInput)) {
            $floorInput = $bulkMode ? end($floorInput) : reset($floorInput);
            $request->merge(['floor_number' => $floorInput]);
        }

        // Step 10: Approved Building Plan Handling (Strict Nullification on 'no')
        $isPlanApproved = strtolower($request->approved_building_plan ?? 'no') === 'yes';
        $bldgPlanDate = $isPlanApproved ? $request->building_plan_approval_date : null;
        $bldgPlanCopy = $isPlanApproved ? ($buildingPlanCopyPath ?: ($project->building_plan_copy ?? null)) : null;

        // Step 11: Status of OC Handling (Strict Nullification on 'no' or 'complete')
        $isOcReceived = strtolower($request->oc_status_yn ?? 'no') === 'yes';
        $ocStatusVal = $isOcReceived ? strtolower($request->input('oc_status', 'part')) : null;
        $ocDate = $isOcReceived ? $request->oc_date : null;
        $ocCopyFile = $isOcReceived ? $ocFilePath : null;
        $totalFlats = $isOcReceived && $request->filled('total_flats_in_project') ? (int)$request->total_flats_in_project : null;
        $totalEwsFlats = $isOcReceived && $request->filled('total_ews_flats_in_project') ? (int)$request->total_ews_flats_in_project : null;
        $flatsOffered = ($isOcReceived && $ocStatusVal === 'part' && $request->filled('flats_offered_for_draw')) ? (int)$request->flats_offered_for_draw : null;
        $remainingFlats = ($isOcReceived && $ocStatusVal === 'part' && $request->filled('remaining_flats_for_oc')) ? (int)$request->remaining_flats_for_oc : null;

        // Bulk Mode Generation
        if ($bulkMode) {
            // Nullify single mode field to prevent conflicting data
            $request->merge(['flat_number' => null]);

            if ($request->flat_number_type === 'custom') {
                $request->merge(['from_flat' => null, 'to_flat' => null]);
            } else {
                $request->merge(['custom_flat_numbers' => null]);
            }

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
                        'register_flat' => $request->register_flat,
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
                        'sector_no' => $request->sector_no ?? ($project->sector_no ?? null),
                        'address_of_project' => $request->address_of_project ?? ($project->address ?? null),
                        'block_tower_number' => $blockName,
                        'block_id' => $blockId,
                        'floor' => $floorLabel,
                        'flat_number' => $flatNumberStr,
                        'created_by' => $user->id,
                        'developer_name' => $request->developer_name,
                        'license_no' => $request->license_no,
                        'area_of_project' => $request->area_of_project ?? ($project->area_of_project ?? null),
                        'developer_plan' => $developerPlanPath ?: ($project->developer_plan ?? null),
                        'approved_building_plan' => $isPlanApproved ? 'yes' : 'no',
                        'building_plan_approval_date' => $bldgPlanDate,
                        'building_plan_copy' => $bldgPlanCopy,
                        'oc_status_yn' => $isOcReceived ? 'yes' : 'no',
                        'oc_status' => $ocStatusVal,
                        'oc_date' => $ocDate,
                        'oc_copy_file' => $ocCopyFile,
                        'total_flats_in_project' => $totalFlats,
                        'total_ews_flats_in_project' => $totalEwsFlats,
                        'flats_offered_for_draw' => $flatsOffered,
                        'remaining_flats_for_oc' => $remainingFlats,
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
        $request->merge([
            'from_flat' => null,
            'to_flat' => null,
            'custom_flat_numbers' => null,
            'flat_number_type' => null,
            'floor_prefix_enabled' => null,
        ]);

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
            'register_flat' => $request->register_flat,
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
            'sector_no' => $request->sector_no ?? ($project->sector_no ?? null),
            'address_of_project' => $request->address_of_project ?? ($project->address ?? null),
            'block_tower_number' => $blockName,
            'block_id' => $blockId,
            'floor' => $floorLabel,
            'flat_number' => $request->flat_number,
            'created_by' => $user->id,
            'developer_name' => $request->developer_name,
            'license_no' => $request->license_no,
            'area_of_project' => $request->area_of_project ?? ($project->area_of_project ?? null),
            'developer_plan' => $developerPlanPath ?: ($project->developer_plan ?? null),
            'approved_building_plan' => $isPlanApproved ? 'yes' : 'no',
            'building_plan_approval_date' => $bldgPlanDate,
            'building_plan_copy' => $bldgPlanCopy,
            'oc_status_yn' => $isOcReceived ? 'yes' : 'no',
            'oc_status' => $ocStatusVal,
            'oc_date' => $ocDate,
            'oc_copy_file' => $ocCopyFile,
            'total_flats_in_project' => $totalFlats,
            'total_ews_flats_in_project' => $totalEwsFlats,
            'flats_offered_for_draw' => $flatsOffered,
            'remaining_flats_for_oc' => $remainingFlats,
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

        // Resolve user's zone if set as zone_id or zone_name/district_name
        $userZoneId = $user->zone_id;
        if (!$userZoneId && !empty($user->district_name)) {
            $cleanUserDist = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
            $stpZone = DB::table('ews_stp_districts')->where('name', $cleanUserDist)->first();
            if ($stpZone) {
                $userZoneId = $stpZone->id;
            }
        }

        // If creator, always allow
        if ($flat->created_by == $user->id) {
            $allowed = true;
        } else {
            // Zone-wise and district-wise edit authorization check
            $allowed = false;
            if (!empty($userZoneId)) {
                $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $userZoneId)->pluck('id')->toArray();
                $allowed = ($flat->zone_id == $userZoneId) || in_array($flat->district_id, $zoneDistrictIds);
                if (!$allowed && $flat->district_id) {
                    $flatDistZone = DB::table('ews_districts')->where('id', $flat->district_id)->value('zone_id');
                    if ($flatDistZone && $flatDistZone == $userZoneId) {
                        $allowed = true;
                    }
                }
            } elseif (!empty($user->district_name)) {
                $userDist = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
                $flatDist = strtoupper(trim(str_ireplace(' ZONE', '', $flat->district_name)));
                $allowed = ($userDist === $flatDist || $user->district_id == $flat->district_id);
            }
        }

        if (!$allowed) {
            abort(403, 'Unauthorized action for this flat record.');
        }

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

        $project = !empty($flat->project_id) ? EwsProject::find($flat->project_id) : null;
        if (!$project && !empty($flat->project_name)) {
            $project = EwsProject::where('name', $flat->project_name)->first();
        }
        $zones = DB::table('ews_stp_districts')->orderBy('name')->get();

        return view('ews.developer.edit', compact('user', 'flat', 'project', 'zone', 'zones', 'districts', 'secureId', 'towns', 'townTypes', 'projects', 'blocks', 'displayZoneName'));
    }

    public function update(Request $request, $secureId)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403);
        }

        $flat = EwsBuilderFlat::where('secure_id', $secureId)->first();
        if (!$flat && is_numeric($secureId)) {
            $flat = EwsBuilderFlat::find($secureId);
        }
        if (!$flat) {
            abort(404, 'Flat record not found.');
        }

        // Resolve user's zone if set as zone_id or zone_name/district_name
        $userZoneId = $user->zone_id;
        if (!$userZoneId && !empty($user->district_name)) {
            $cleanUserDist = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
            $stpZone = DB::table('ews_stp_districts')->where('name', $cleanUserDist)->first();
            if ($stpZone) {
                $userZoneId = $stpZone->id;
            }
        }

        // If creator, always allow
        if ($flat->created_by == $user->id) {
            $allowed = true;
        } else {
            // Zone-wise update authorization check
            $allowed = false;
            if (!empty($userZoneId)) {
                $zoneDistrictIds = DB::table('ews_districts')->where('zone_id', $userZoneId)->pluck('id')->toArray();
                $allowed = ($flat->zone_id == $userZoneId) || in_array($flat->district_id, $zoneDistrictIds);
                if (!$allowed && $flat->district_id) {
                    $flatDistZone = DB::table('ews_districts')->where('id', $flat->district_id)->value('zone_id');
                    if ($flatDistZone && $flatDistZone == $userZoneId) {
                        $allowed = true;
                    }
                }
            } elseif (!empty($user->district_name)) {
                $userDist = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
                $flatDist = strtoupper(trim(str_ireplace(' ZONE', '', $flat->district_name)));
                $allowed = ($userDist === $flatDist || $user->district_id == $flat->district_id);
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
            if ($district->zone_id != $userZoneId) {
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

            $town = EwsTown::create([
                'district_id' => $district->id,
                'zone_id'     => $zoneId,
                'zone_name'   => $zoneName,
                'name'        => $cleanTownName,
                'type'        => !empty($townType) ? $townType : null,
            ]);
        } else {
            $town = EwsTown::where('district_id', $district->id)->where('id', $request->town_id)->first() ?? EwsTown::find($request->town_id);
        }
        $townId = $town ? $town->id : $flat->town_id;
        $townName = $town ? $town->name : $flat->town_name;

        // Resolve Project ID and Name
        if ($request->project_id === 'new') {
            $cleanProjName = trim($request->new_project_name);
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
            $project = EwsProject::find($request->project_id);
            $projectId = $project ? $project->id : $flat->project_id;
            $projectName = $project ? $project->name : $flat->project_name;
        }

        // Resolve Block ID and Name
        if ($request->block_id === 'new') {
            $cleanBlockName = trim($request->new_block_name);
            $block = EwsBlock::create([
                'project_id' => $projectId,
                'name' => $cleanBlockName,
            ]);
            $blockId = $block->id;
            $blockName = $block->name;
        } else {
            $block = EwsBlock::find($request->block_id);
            $blockId = $block ? $block->id : $flat->block_id;
            $blockName = $block ? $block->name : $flat->block_tower_number;
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

        // Check duplicate
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
            'register_flat' => 'Flat Title',
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
        $flat->register_flat = $request->register_flat;
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
            $request->developer_name ?: $user->name,
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
            ? "Record updated by {$user->name} (" . strtoupper($user->role ?? 'USER') . "): " . implode('; ', $summaryItems)
            : "Record verified and re-submitted by {$user->name} (" . strtoupper($user->role ?? 'USER') . ") without field modifications";

        // Store into EwsFlatAuditLog
        try {
            if (Schema::hasTable('ews_flat_audit_logs')) {
                EwsFlatAuditLog::create([
                    'flat_id' => $flat->id,
                    'flat_code' => $flat->flat_code,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role ?? 'developer',
                    'action' => !empty($changedDiff) ? 'UPDATED' : 'RESUBMITTED',
                    'summary' => $summaryText,
                    'changed_fields' => $changedDiff,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        } catch (\Throwable $e) {}

        // Store into EwsDeveloperLog
        try {
            EwsDeveloperLog::create([
                'user_id' => $user->id,
                'action' => 'UPDATED_FLAT',
                'details' => "Updated Flat {$flat->flat_number} ({$flat->flat_code}): " . $summaryText,
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('ews.developer.flats.show', $flat->secure_id)
            ->with('success', 'EWS Builder Flat record updated and audit log successfully registered.');
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
            ->get(['id', 'name', 'project_abbr', 'town_id', 'town_name', 'district_id', 'zone_id', 'address', 'sector_no', 'developer_name', 'license_no', 'area_of_project', 'developer_plan', 'approved_building_plan', 'building_plan_approval_date', 'building_plan_copy', 'oc_status_yn']);

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
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'district_id' => 'required|exists:ews_districts,id',
            'town_id' => 'nullable|exists:ews_towns,id',
            'project_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'sector_no' => 'nullable|string|max:100',
            'developer_name' => 'nullable|string|max:255',
            'license_no' => 'nullable|string|max:150',
            'zone_id' => 'nullable',
        ]);

        $districtId = (int)$request->district_id;
        $townId = $request->filled('town_id') ? (int)$request->town_id : null;
        $cleanName = trim($request->project_name);

        $district = DB::table('ews_districts')->where('id', $districtId)->first();
        $town = $townId ? EwsTown::find($townId) : null;

        // Resolve Zone
        $zone = null;
        if ($request->filled('zone_id')) {
            $zone = DB::table('ews_stp_districts')->where('id', $request->zone_id)->first();
        }
        if (!$zone && $district && $district->zone_id) {
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
            'address' => $request->address,
            'sector_no' => $request->sector_no,
            'developer_name' => $request->developer_name ?: null,
            'license_no' => $request->license_no,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Project '{$cleanName}' added successfully to database.",
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'address' => $project->address,
                'sector_no' => $project->sector_no,
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
     * Direct DTP Approval API (Single Click Action with Audit Logging)
     */
    public function approveDtp(Request $request, $secureId)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_stp', 'stp', 'mmgay-dtp', 'ews_tcp', 'tcp', 'ews_developer'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $flat = EwsBuilderFlat::where('secure_id', $secureId)
            ->orWhere('id', $secureId)
            ->first();

        if (!$flat && is_numeric(EwsHelper::decodeSecureId($secureId))) {
            $flat = EwsBuilderFlat::find(EwsHelper::decodeSecureId($secureId));
        }

        if (!$flat) {
            return response()->json(['success' => false, 'message' => 'Flat record not found.'], 404);
        }

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
            if (Schema::hasTable('ews_flat_audit_logs')) {
                EwsFlatAuditLog::create([
                    'flat_id' => $flat->id,
                    'flat_code' => $flat->flat_code,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role ?? 'developer',
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
            }
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
