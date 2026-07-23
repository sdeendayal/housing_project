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
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'ews_developer') {
            abort(403, 'Unauthorized access to Developer dashboard.');
        }

        $userDist = !empty($user->district_name) ? strtoupper(trim($user->district_name)) : null;

        // District Flats Query
        $districtFlatsQuery = EwsBuilderFlat::query();
        if ($userDist) {
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
            'total_projects' => !empty($user->district_id) 
                ? EwsProject::where('district_id', $user->district_id)->count()
                : EwsProject::count(),
            'total_towns' => !empty($user->district_id) 
                ? EwsTown::where('district_id', $user->district_id)->count()
                : EwsTown::count(),
            'total_logs' => EwsDeveloperLog::where('user_id', $user->id)->count(),
        ];

        $currentView = $request->query('view', 'dashboard');

        $projectsList = !empty($user->district_id) 
            ? EwsProject::where('district_id', $user->district_id)->orderBy('name')->get()
            : EwsProject::orderBy('name')->get();
        $townsList = !empty($user->district_id) 
            ? EwsTown::where('district_id', $user->district_id)->orderBy('name')->get()
            : EwsTown::orderBy('name')->get();

        return view('ews.developer.dashboard', compact('user', 'stats', 'projectBreakdown', 'recentLogs', 'currentView', 'projectsList', 'townsList'));
    }

    /**
     * Helper to get registry query with applied search & district filters.
     */
    private function getFilteredQuery(Request $request)
    {
        $query = EwsBuilderFlat::query();
        $user = Auth::user();

        // 1. Ownership Scope Filter (My Flats vs All District Flats)
        if ($request->input('ownership_scope') === 'my_flats' || $request->input('my_flats') == '1') {
            $query->where('created_by', $user->id);
        } else {
            // Lock flats data strictly to developer's assigned district
            if ($user && !empty($user->district_name)) {
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
        if (!$user || $user->role !== 'ews_developer') {
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
        if (!$user || $user->role !== 'ews_developer') {
            abort(403);
        }

        // District wise locking: If developer is assigned a district, pre-select and restrict ONLY to their district!
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

        $towns = collect();
        if ($districts->count() === 1) {
            $towns = EwsTown::where('district_id', $districts->first()->id)->orderBy('name', 'asc')->get();
        }

        return view('ews.developer.create', compact('user', 'districts', 'towns'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'ews_developer') {
            abort(403);
        }

        // Validate basic parameters
        $request->validate([
            'district_id' => 'required|exists:ews_districts,id',
            'town_id' => 'required',
            'new_town_name' => 'required_if:town_id,new|nullable|string|max:255',
            'project_id' => 'required',
            'new_project_name' => 'required_if:project_id,new|nullable|string|max:255',
            'block_id' => 'required',
            'new_block_name' => 'required_if:block_id,new|nullable|string|max:255',
        ]);

        $district = DB::table('ews_districts')->where('id', $request->district_id)->first();
        if (!empty($user->district_name)) {
            $userDist = strtoupper(trim($user->district_name));
            $selectedDist = strtoupper(trim($district->name));
            if ($userDist !== $selectedDist && $user->district_id != $district->id) {
                return back()->withInput()->with('error', "Unauthorized: You can only register flats for {$user->district_name}.");
            }
        }

        // Resolve Town ID and Name
        if ($request->town_id === 'new') {
            $townExists = EwsTown::where('district_id', $district->id)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->new_town_name))])
                ->exists();
            if ($townExists) {
                return back()->withInput()->with('error', "Validation Error: A town named '{$request->new_town_name}' already exists in this district. Please select it from the list.");
            }

            $town = EwsTown::firstOrCreate([
                'district_id' => $district->id,
                'name' => trim($request->new_town_name),
            ]);
            $townId = $town->id;
            $townName = $town->name;
        } else {
            $town = EwsTown::where('district_id', $district->id)->where('id', $request->town_id)->firstOrFail();
            $townId = $town->id;
            $townName = $town->name;
        }

        // Resolve Project ID and Name
        if ($request->project_id === 'new') {
            $projectExists = EwsProject::where('district_id', $district->id)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->new_project_name))])
                ->exists();
            if ($projectExists) {
                return back()->withInput()->with('error', "Validation Error: A project named '{$request->new_project_name}' already exists in this district. Please select it from the list instead of adding it as a new project.");
            }

            $project = EwsProject::firstOrCreate([
                'district_id' => $district->id,
                'name' => trim($request->new_project_name),
            ]);
            $projectId = $project->id;
            $projectName = $project->name;
        } else {
            $project = EwsProject::where('district_id', $district->id)->where('id', $request->project_id)->firstOrFail();
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

        // Bulk Mode Generation
        if ($request->input('bulk_mode') == '1') {
            $request->validate([
                'from_floor' => 'required|integer|min:0|max:100',
                'to_floor' => 'required|integer|min:0|max:100|gte:from_floor',
                'flat_number_type' => 'required|in:range,custom',
                'from_flat' => 'required_if:flat_number_type,range|nullable|integer|min:1',
                'to_flat' => 'required_if:flat_number_type,range|nullable|integer|min:1|gte:from_flat',
                'custom_flat_numbers' => 'required_if:flat_number_type,custom|nullable|string',
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

            $fromFloor = (int)$request->from_floor;
            $toFloor = (int)$request->to_floor;
            
            $flatNumbers = [];
            if ($request->flat_number_type === 'custom') {
                $raw = $request->custom_flat_numbers;
                $parts = explode(',', $raw);
                foreach ($parts as $part) {
                    $num = trim($part);
                    if ($num !== '') {
                        $flatNumbers[] = $num;
                    }
                }
            } else {
                $fromFlat = (int)$request->from_flat;
                $toFlat = (int)$request->to_flat;
                for ($i = $fromFlat; $i <= $toFlat; $i++) {
                    $flatNumbers[] = $i;
                }
            }

            if (empty($flatNumbers)) {
                return back()->withInput()->with('error', "Invalid flats configuration. Please provide a valid flat range or custom list.");
            }

            // Query existing flats for duplicate check in bulk
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
                for ($floorNum = $fromFloor; $floorNum <= $toFloor; $floorNum++) {
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
                            'district_id' => $district->id,
                            'district_name' => $district->name,
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
                }

                EwsDeveloperLog::create([
                    'user_id' => $user->id,
                    'action' => 'CREATED_BULK',
                    'details' => "Bulk Registered {$createdCount} EWS Flats under Tower: {$blockName}, Project: '{$projectName}' in {$townName} (Floors: {$fromFloor} to {$toFloor})",
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
            'floor' => 'required|string|max:255',
            'flat_number' => 'required|string|max:255',
        ]);

        // Check if flat is already registered (Single Mode)
        $existsSingle = EwsBuilderFlat::where('district_id', $district->id)
            ->where('town_name', $townName)
            ->where('project_name', $projectName)
            ->where('block_tower_number', $blockName)
            ->where('floor', $request->floor)
            ->where('flat_number', $request->flat_number)
            ->exists();

        if ($existsSingle) {
            return back()->withInput()->with('error', "Validation Error: EWS Flat '{$request->flat_number}' on Floor '{$request->floor}' in Block '{$blockName}' of Project '{$projectName}' is already registered.");
        }

        $flat = EwsBuilderFlat::create([
            'district_id' => $district->id,
            'district_name' => $district->name,
            'town_name' => $townName,
            'town_id' => $townId,
            'project_name' => $projectName,
            'project_id' => $projectId,
            'block_tower_number' => $blockName,
            'block_id' => $blockId,
            'floor' => $request->floor,
            'flat_number' => $request->flat_number,
            'created_by' => $user->id,
            'secure_id' => md5(uniqid("flat_" . microtime() . rand(), true)),
            'flat_code' => EwsHelper::generateFlatCode(
                $townName,
                $user->name,
                $request->floor,
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
        if (!$user || $user->role !== 'ews_developer') {
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

        // Fetch projects for the flat's district
        $projects = EwsProject::where('district_id', $flat->district_id)
            ->orderBy('name', 'asc')
            ->get();

        // Fetch blocks for the flat's project
        $blocks = collect();
        if ($flat->project_id) {
            $blocks = EwsBlock::where('project_id', $flat->project_id)
                ->orderBy('name', 'asc')
                ->get();
        }

        return view('ews.developer.edit', compact('user', 'flat', 'districts', 'secureId', 'towns', 'projects', 'blocks'));
    }

    public function update(Request $request, $secureId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'ews_developer') {
            abort(403);
        }

        $flat = EwsBuilderFlat::where('secure_id', $secureId)->firstOrFail();

        // District-wise update check
        if (!empty($user->district_name)) {
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
            'project_id' => 'required',
            'new_project_name' => 'required_if:project_id,new|nullable|string|max:255',
            'block_id' => 'required',
            'new_block_name' => 'required_if:block_id,new|nullable|string|max:255',
            'floor' => 'required|string|max:255',
            'flat_number' => 'required|string|max:255',
        ]);

        $district = DB::table('ews_districts')->where('id', $request->district_id)->first();

        if (!empty($user->district_name)) {
            $userDist = strtoupper(trim($user->district_name));
            $selectedDist = strtoupper(trim($district->name));
            if ($userDist !== $selectedDist && $user->district_id != $district->id) {
                return back()->withInput()->with('error', "Unauthorized: You can only update flats for {$user->district_name}.");
            }
        }

        // Resolve Town ID and Name
        if ($request->town_id === 'new') {
            $townExists = EwsTown::where('district_id', $district->id)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->new_town_name))])
                ->exists();
            if ($townExists) {
                return back()->withInput()->with('error', "Validation Error: A town named '{$request->new_town_name}' already exists in this district. Please select it from the list.");
            }

            $town = EwsTown::firstOrCreate([
                'district_id' => $district->id,
                'name' => trim($request->new_town_name),
            ]);
            $townId = $town->id;
            $townName = $town->name;
        } else {
            $town = EwsTown::where('district_id', $district->id)->where('id', $request->town_id)->firstOrFail();
            $townId = $town->id;
            $townName = $town->name;
        }

        // Resolve Project ID and Name
        if ($request->project_id === 'new') {
            $projectExists = EwsProject::where('district_id', $district->id)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->new_project_name))])
                ->exists();
            if ($projectExists) {
                return back()->withInput()->with('error', "Validation Error: A project named '{$request->new_project_name}' already exists in this district. Please select it from the list instead of adding it as a new project.");
            }

            $project = EwsProject::firstOrCreate([
                'district_id' => $district->id,
                'name' => trim($request->new_project_name),
            ]);
            $projectId = $project->id;
            $projectName = $project->name;
        } else {
            $project = EwsProject::where('district_id', $district->id)->where('id', $request->project_id)->firstOrFail();
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

        // Check if flat is already registered (excluding this record)
        $existsUpdate = EwsBuilderFlat::where('id', '!=', $flat->id)
            ->where('district_id', $district->id)
            ->where('town_name', $townName)
            ->where('project_name', $projectName)
            ->where('block_tower_number', $blockName)
            ->where('floor', $request->floor)
            ->where('flat_number', $request->flat_number)
            ->exists();

        if ($existsUpdate) {
            return back()->withInput()->with('error', "Validation Error: Another EWS Flat with the same details ('{$request->flat_number}', Floor '{$request->floor}', Block '{$blockName}') is already registered.");
        }

        $validatedData = [
            'district_id' => $district->id,
            'district_name' => $district->name,
            'town_name' => $townName,
            'town_id' => $townId,
            'project_name' => $projectName,
            'project_id' => $projectId,
            'block_tower_number' => $blockName,
            'block_id' => $blockId,
            'floor' => $request->floor,
            'flat_number' => $request->flat_number,
            'flat_code' => EwsHelper::generateFlatCode(
                $townName,
                $user->name,
                $request->floor,
                $blockName,
                $request->flat_number
            )
        ];

        $oldDetails = "Flat: {$flat->flat_number}, Floor: {$flat->floor}, Tower: {$flat->block_tower_number} under Project '{$flat->project_name}' in {$flat->town_name} ({$flat->district_name})";

        $flat->update($validatedData);

        $newDetails = "Flat: {$flat->flat_number}, Floor: {$flat->floor}, Tower: {$flat->block_tower_number} under Project '{$flat->project_name}' in {$flat->town_name} ({$flat->district_name})";

        // Create log entry
        EwsDeveloperLog::create([
            'user_id' => $user->id,
            'action' => 'UPDATED',
            'details' => "Updated EWS Flat ID #{$flat->id} from [{$oldDetails}] to [{$newDetails}]",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('ews.developer.dashboard')->with('success', 'EWS Builder Flat record updated successfully.');
    }

    public function destroy(Request $request, $secureId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'ews_developer') {
            abort(403);
        }

        $flat = EwsBuilderFlat::where('secure_id', $secureId)->firstOrFail();
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
        if (!$user || $user->role !== 'ews_developer') {
            abort(403);
        }

        // Fetch logs with pagination
        $logs = EwsDeveloperLog::with('developer')
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('ews.developer.logs', compact('user', 'logs'));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'ews_developer') {
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
        if (!$user || $user->role !== 'ews_developer') {
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
        if (!$user || $user->role !== 'ews_developer') {
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
        $projects = EwsProject::where('district_id', $districtId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
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
            return response()->json([]);
        }
        $towns = EwsTown::where('district_id', $districtId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
        return response()->json($towns);
    }
}
