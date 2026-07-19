<?php

namespace App\Http\Controllers;

use App\Models\EwsBuilderFlat;
use App\Models\EwsDeveloperLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\EwsHelper;

class EwsDeveloperDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'ews_developer') {
            abort(403, 'Unauthorized access to Developer dashboard.');
        }

        // Load districts alphabetically for the filter dropdown
        $districts = \App\Models\EwsDeveloperDistrict::orderBy('name', 'asc')->get();

        $stats = [
            'total_flats' => EwsBuilderFlat::count(),
            'active_districts' => EwsBuilderFlat::distinct('district_id')->count(),
            'total_logs' => EwsDeveloperLog::count(),
        ];

        return view('ews.developer.dashboard', compact('user', 'districts', 'stats'));
    }

    /**
     * Helper to get registry query with applied search & district filters.
     */
    /**
     * Helper to get registry query with applied search & district filters.
     */
    private function getFilteredQuery(Request $request)
    {
        $query = EwsBuilderFlat::query();
        $user = Auth::user();

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
            ->rawColumns(['actions'])
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

        return view('ews.developer.create', compact('user', 'districts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'ews_developer') {
            abort(403);
        }

        $validated = $request->validate([
            'district_id' => 'required|exists:ews_districts,id',
            'town_name' => 'required|string|max:255',
            'project_name' => 'required|string|max:255',
            'block_tower_number' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'flat_number' => 'required|string|max:255',
        ]);

        $district = DB::table('ews_districts')->where('id', $request->district_id)->first();

        // Enforce District-wise lock: Developers can ONLY register flats for their assigned district!
        if (!empty($user->district_name)) {
            $userDist = strtoupper(trim($user->district_name));
            $selectedDist = strtoupper(trim($district->name));
            if ($userDist !== $selectedDist && $user->district_id != $district->id) {
                return back()->withInput()->with('error', "Unauthorized: You are assigned to {$user->district_name} district. You can only register flats for {$user->district_name}.");
            }
        }

        $validated['district_id'] = $district->id;
        $validated['district_name'] = $district->name;
        $validated['created_by'] = $user->id;
        $validated['secure_id'] = md5(uniqid("flat_" . microtime() . rand(), true));

        $flat = EwsBuilderFlat::create($validated);

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

        return view('ews.developer.edit', compact('user', 'flat', 'districts', 'secureId'));
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

        $validated = $request->validate([
            'district_id' => 'required|exists:ews_districts,id',
            'town_name' => 'required|string|max:255',
            'project_name' => 'required|string|max:255',
            'block_tower_number' => 'required|string|max:255',
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

        $validated['district_id'] = $district->id;
        $validated['district_name'] = $district->name;

        $oldDetails = "Flat: {$flat->flat_number}, Floor: {$flat->floor}, Tower: {$flat->block_tower_number} under Project '{$flat->project_name}' in {$flat->town_name} ({$flat->district_name})";

        $flat->update($validated);

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
            ->paginate(15);

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

    public function districtStats()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'ews_developer') {
            abort(403);
        }

        // Calculate counts for all 23 districts in ews_districts (alphabetically)
        $districts = DB::table('ews_districts')->orderBy('name', 'asc')->get()->map(function ($district) {
            $district->flats_count = EwsBuilderFlat::where('district_id', $district->id)->count();
            return $district;
        });

        return view('ews.developer.district_stats', compact('user', 'districts'));
    }
}
