<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\EwsTown;
use App\Models\EwsProject;
use App\Traits\EwsBuilderFlatsMatrixTrait;

class EwsTcpDashboardController extends Controller
{
    use EwsBuilderFlatsMatrixTrait;

    /**
     * Display the dynamic TCP (Town & Country Planning) Portal Dashboard.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('ews.tcp.login');
        }

        if (!in_array($user->role, ['ews_tcp', 'tcp', 'ews_department', 'super_admin', 'admin'])) {
            abort(403, 'Unauthorized access to TCP portal.');
        }

        // Summary Statistics (Haryana Statewide) - Includes Builder Flats & 4,211 Master Allotted Pool
        $stats = $this->getMatrixSummaryStats();

        // Cascading Filter Dropdown Masters
        $zonesList = DB::table('ews_stp_districts')->where('is_active', 1)->orderBy('name')->get();
        $districts = DB::table('ews_districts')->orderBy('name')->get();
        $townsList = DB::table('ews_towns')->orderBy('name')->get();
        $projectsList = DB::table('ews_projects')->orderBy('name')->get(['id', 'name', 'license_no']);
        $licensesList = $this->getMatrixLicensesList();

        $currentView = $request->query('view', 'dashboard');

        return view('ews.tcp.dashboard', compact(
            'user',
            'stats',
            'zonesList',
            'districts',
            'townsList',
            'projectsList',
            'licensesList',
            'currentView'
        ));
    }

    /**
     * Server-side DataTables AJAX source for TCP Portal.
     */
    public function getFlatsData(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_tcp', 'tcp', 'ews_department', 'super_admin', 'admin'])) {
            abort(403, 'Unauthorized.');
        }

        return $this->buildMatrixDataTable($request);
    }

    /**
     * Dynamic District list cascading endpoint by Zone.
     */
    public function getDistricts(Request $request)
    {
        $query = DB::table('ews_districts')->orderBy('name', 'asc');
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        return response()->json($query->get(['id', 'name', 'zone_id']));
    }

    /**
     * Dynamic Town list cascading endpoint.
     */
    public function getTowns(Request $request)
    {
        $query = EwsTown::orderBy('name', 'asc');
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        } elseif ($request->filled('zone_id')) {
            $zoneDistIds = DB::table('ews_districts')->where('zone_id', $request->zone_id)->pluck('id')->toArray();
            $query->where(function($q) use ($request, $zoneDistIds) {
                $q->where('zone_id', $request->zone_id);
                if (!empty($zoneDistIds)) $q->orWhereIn('district_id', $zoneDistIds);
            });
        }
        return response()->json($query->get(['id', 'name']));
    }

    /**
     * Dynamic Project list cascading endpoint.
     */
    public function getProjects(Request $request)
    {
        $query = EwsProject::orderBy('name', 'asc');
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        } elseif ($request->filled('zone_id')) {
            $zoneDistIds = DB::table('ews_districts')->where('zone_id', $request->zone_id)->pluck('id')->toArray();
            $query->where(function($q) use ($request, $zoneDistIds) {
                $q->where('zone_id', $request->zone_id);
                if (!empty($zoneDistIds)) $q->orWhereIn('district_id', $zoneDistIds);
            });
        }
        if ($request->filled('town_id')) {
            $query->where('town_id', $request->town_id);
        }
        return response()->json($query->get(['id', 'name', 'license_no']));
    }

    /**
     * Dynamic License list cascading endpoint.
     */
    public function getLicenses(Request $request)
    {
        $licenses = $this->getMatrixLicensesList(
            $request->input('zone_id'),
            $request->input('district_id'),
            $request->input('town_id'),
            $request->input('project_id')
        );
        return response()->json($licenses);
    }

    /**
     * Dedicated Single Flat Comprehensive Bio-Data Page (Read-Only).
     */
    public function showFlat(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_tcp', 'tcp', 'ews_department', 'super_admin', 'admin'])) {
            abort(403, 'Unauthorized access to TCP portal.');
        }

        // Strict security: Primary ID access blocked - strictly secure_id only
        if (is_numeric($id)) {
            abort(404, 'Error: Flat cannot be opened with Primary ID. Access is strictly permitted via secure_id only.');
        }

        $flat = \App\Models\EwsBuilderFlat::where('secure_id', $id)->first();

        if (!$flat) {
            abort(404, 'Flat dossier record not found.');
        }

        $project = null;
        if (!empty($flat->project_id)) {
            $project = EwsProject::find($flat->project_id);
        }
        if (!$project && !empty($flat->project_name)) {
            $project = EwsProject::where('name', $flat->project_name)->first();
        }

        $creator = null;
        if (!empty($flat->created_by)) {
            $creator = \App\Models\User::find($flat->created_by);
        }

        $backUrl = route('ews.tcp.dashboard');
        $stats = $this->getMatrixSummaryStats();

        return view('ews.tcp.flat_view', compact('user', 'flat', 'project', 'creator', 'backUrl', 'stats'));
    }

    /**
     * Single Flat Comprehensive Bio-Data (Read-Only JSON).
     */
    public function getFlatDetails(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_tcp', 'tcp', 'ews_department', 'super_admin', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        return $this->getFlatBioDataPayload($id);
    }

    /**
     * Export Offered Flats Matrix (CSV, Excel, PDF).
     */
    public function exportFlats(Request $request, $format)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_tcp', 'tcp', 'ews_department', 'super_admin', 'admin'])) {
            abort(403, 'Unauthorized.');
        }

        if ($format === 'pdf') {
            return $this->exportMatrixPdfFile($request, 'tcp_ews');
        } elseif ($format === 'excel') {
            return $this->exportMatrixExcelFile($request, 'tcp_ews');
        }

        return $this->exportMatrixCsvFile($request, 'tcp_ews');
    }

    /**
     * Dedicated Physical Possession Web Module (4,211 Master Allotted Beneficiaries).
     */
    public function possession(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('ews.tcp.login');
        }

        if (!in_array($user->role, ['ews_tcp', 'tcp', 'ews_department', 'super_admin', 'admin'])) {
            abort(403, 'Unauthorized access to TCP portal.');
        }

        $totalAllotted = DB::table('ews_allotted_8')->count();
        $possessionGiven = DB::table('ews_allotted_8')
            ->where(function ($q) {
                $q->where('is_possession_given', 1)
                  ->orWhere('possession_status', 'Possession Given')
                  ->orWhere('possession_status', 'GIVEN');
            })->count();
        $possessionPending = max(0, $totalAllotted - $possessionGiven);

        $stats = [
            'total_allotted' => $totalAllotted,
            'possession_given' => $possessionGiven,
            'possession_pending' => $possessionPending,
            'given_percentage' => $totalAllotted > 0 ? round(($possessionGiven / $totalAllotted) * 100, 2) : 0,
            'pending_percentage' => $totalAllotted > 0 ? round(($possessionPending / $totalAllotted) * 100, 2) : 0,
        ];

        // Cascading District and Category Masters
        $districts = DB::table('ews_districts')->orderBy('name')->get(['id', 'name']);
        $allottedDistricts = DB::table('ews_allotted_8')
            ->whereNotNull('dist_name')
            ->where('dist_name', '!=', '')
            ->distinct()
            ->orderBy('dist_name')
            ->pluck('dist_name');

        $initialScope = $request->query('scope', 'all');

        return view('ews.tcp.possession', compact(
            'user',
            'stats',
            'districts',
            'allottedDistricts',
            'initialScope'
        ));
    }

    /**
     * Server-side DataTables AJAX source for Physical Possession (4,211 Pool).
     */
    public function getPossessionData(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_tcp', 'tcp', 'ews_department', 'super_admin', 'admin'])) {
            abort(403, 'Unauthorized.');
        }

        $scope = $request->input('scope', 'all');

        $query = DB::table('ews_allotted_8')
            ->select([
                'id',
                'secure_id',
                'application_number',
                'full_name',
                'mobile_number',
                'flat_no',
                'dist_name',
                'dist_id',
                'property_type',
                'phase',
                'is_possession_given',
                'possession_status',
                'possession_given_at',
            ]);

        // Filter by District
        if ($request->filled('district')) {
            $dist = $request->input('district');
            if (is_numeric($dist)) {
                $query->where('dist_id', $dist);
            } else {
                $query->where('dist_name', $dist);
            }
        }

        // Filter by Scope
        if ($scope === 'possession_given') {
            $query->where(function ($q) {
                $q->where('is_possession_given', 1)
                  ->orWhere('possession_status', 'Possession Given')
                  ->orWhere('possession_status', 'GIVEN');
            });
        } elseif ($scope === 'possession_pending') {
            $query->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('is_possession_given', 0)->orWhereNull('is_possession_given');
                })->where(function ($sq) {
                    $sq->whereNull('possession_status')
                      ->orWhere('possession_status', '!=', 'Possession Given')
                      ->where('possession_status', '!=', 'GIVEN');
                });
            });
        }

        // Global Search
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

        return \Yajra\DataTables\Facades\DataTables::of($query)
            ->smart(false)
            ->addIndexColumn()
            ->addColumn('dist_name', fn($r) => '<span class="font-bold text-slate-900 uppercase tracking-wide">' . e($r->dist_name ?: 'HARYANA') . '</span>')
            ->addColumn('application_number', fn($r) => '<span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">#' . e($r->application_number) . '</span>')
            ->addColumn('full_name', fn($r) => '<div class="font-bold text-slate-800">' . e($r->full_name) . '</div>')
            ->addColumn('mobile_number', function($r) {
                if (empty($r->mobile_number)) return '<span class="text-slate-400 font-mono text-[10px]">N/A</span>';
                $m = (string) $r->mobile_number;
                $masked = strlen($m) >= 6 ? substr($m, 0, 2) . '******' . substr($m, -2) : $m;
                return '<span class="font-mono text-slate-600 text-xs font-semibold">' . e($masked) . '</span>';
            })
            ->addColumn('flat_no', fn($r) => '<span class="text-violet-700 font-mono font-black text-xs">' . e($r->flat_no ?: '—') . '</span>')
            ->addColumn('possession_status', function($r) {
                $isGiven = ($r->is_possession_given == 1 || $r->possession_status === 'Possession Given' || $r->possession_status === 'GIVEN');
                if ($isGiven) {
                    return '<span class="px-2 py-0.5 rounded text-[8.5px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 inline-flex items-center gap-1 shadow-2xs"><i class="bi bi-patch-check-fill text-emerald-600"></i> Possession Given</span>';
                }
                return '<span class="px-2 py-0.5 rounded text-[8.5px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-300 inline-flex items-center gap-1 shadow-2xs"><i class="bi bi-clock-history text-amber-600"></i> Pending Possession</span>';
            })
            ->addColumn('possession_date', function($r) {
                if (!empty($r->possession_given_at)) {
                    return '<span class="font-mono text-[10.5px] text-emerald-700 font-bold">' . date('d M, Y', strtotime($r->possession_given_at)) . '</span>';
                }
                return '<span class="text-slate-400 font-mono text-[10px] italic">Awaiting Handover</span>';
            })
            ->addColumn('action', function($r) {
                $identifier = !empty($r->secure_id) ? $r->secure_id : $r->id;
                $viewUrl = route('ews.tcp.possession.show', $identifier);
                return '<a href="' . $viewUrl . '" class="px-2.5 py-1 bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white border border-sky-200 hover:border-sky-600 rounded-lg text-[10px] font-black uppercase transition-all inline-flex items-center gap-1 shadow-2xs whitespace-nowrap"><i class="bi bi-eye-fill"></i> View</a>';
            })
            ->rawColumns(['dist_name', 'application_number', 'full_name', 'mobile_number', 'flat_no', 'possession_status', 'possession_date', 'action'])
            ->make(true);
    }

    /**
     * Dedicated Physical Possession Beneficiary Dossier Page (Read-Only).
     */
    public function showPossession(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_tcp', 'tcp', 'ews_department', 'super_admin', 'admin'])) {
            abort(403, 'Unauthorized access to TCP portal.');
        }

        $beneficiary = null;
        $idStr = (string)$id;

        // If long token (secure_id is 32 alphanumeric chars), query secure_id directly
        if (strlen($idStr) > 10) {
            $beneficiary = DB::table('ews_allotted_8')->where('secure_id', $idStr)->first();
        }

        // If numeric ID (e.g. legacy /view/1 or /view/533), find by numeric ID and redirect to secure_id
        if (!$beneficiary && (is_numeric($id) || ctype_digit($idStr))) {
            $beneficiary = DB::table('ews_allotted_8')->where('id', (int)$id)->first();
            if ($beneficiary && !empty($beneficiary->secure_id)) {
                return redirect()->route('ews.tcp.possession.show', $beneficiary->secure_id);
            }
        }

        // Final fallback
        if (!$beneficiary) {
            $beneficiary = DB::table('ews_allotted_8')->where('secure_id', $idStr)->first();
        }

        if (!$beneficiary) {
            abort(404, 'Beneficiary possession record not found.');
        }

        $handover = DB::table('ews_beneficiary_possessions')
            ->where('beneficiary_id', $beneficiary->id)
            ->orWhere('application_number', $beneficiary->application_number)
            ->first();

        $stpUser = ($handover && !empty($handover->stp_user_id)) ? DB::table('users')->find($handover->stp_user_id) : null;

        $builderFlat = DB::table('ews_builder_flats')
            ->where('flat_code', $beneficiary->flat_no)
            ->orWhere('flat_number', $beneficiary->flat_no)
            ->first();

        $project = null;
        if ($builderFlat && !empty($builderFlat->project_id)) {
            $project = DB::table('ews_projects')->find($builderFlat->project_id);
        } elseif ($handover && !empty($handover->project_id)) {
            $project = DB::table('ews_projects')->find($handover->project_id);
        }

        $prevBeneficiary = DB::table('ews_allotted_8')->where('id', '<', $beneficiary->id)->orderBy('id', 'desc')->first(['id', 'secure_id']);
        $nextBeneficiary = DB::table('ews_allotted_8')->where('id', '>', $beneficiary->id)->orderBy('id', 'asc')->first(['id', 'secure_id']);

        $stats = [
            'total_allotted' => DB::table('ews_allotted_8')->count(),
            'possession_given' => DB::table('ews_allotted_8')->where('is_possession_given', 1)->count(),
            'possession_pending' => DB::table('ews_allotted_8')->where(function($q){
                $q->where('is_possession_given', 0)->orWhereNull('is_possession_given');
            })->count(),
        ];

        $backUrl = route('ews.tcp.possession');

        return view('ews.tcp.possession_view', compact(
            'user',
            'beneficiary',
            'handover',
            'stpUser',
            'builderFlat',
            'project',
            'prevBeneficiary',
            'nextBeneficiary',
            'stats',
            'backUrl'
        ));
    }

    /**
     * Fetch Single Beneficiary Physical Possession Bio-Data for Modal.
     */
    public function getPossessionBeneficiary(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_tcp', 'tcp', 'ews_department', 'super_admin', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $beneficiary = DB::table('ews_allotted_8')->where('id', $id)->first();
        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        $handover = DB::table('ews_beneficiary_possessions')
            ->where('beneficiary_id', $beneficiary->id)
            ->orWhere('application_number', $beneficiary->application_number)
            ->first();

        return response()->json([
            'success' => true,
            'beneficiary' => $beneficiary,
            'handover' => $handover,
        ]);
    }

    /**
     * Export Physical Possession Records to CSV.
     */
    public function exportPossession(Request $request, $format)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_tcp', 'tcp', 'ews_department', 'super_admin', 'admin'])) {
            abort(403, 'Unauthorized.');
        }

        $scope = $request->input('scope', 'all');

        $query = DB::table('ews_allotted_8')
            ->select([
                'id',
                'application_number',
                'full_name',
                'mobile_number',
                'flat_no',
                'dist_name',
                'property_type',
                'phase',
                'is_possession_given',
                'possession_status',
                'possession_given_at',
            ]);

        if ($request->filled('district')) {
            $dist = $request->input('district');
            if (is_numeric($dist)) {
                $query->where('dist_id', $dist);
            } else {
                $query->where('dist_name', $dist);
            }
        }

        if ($scope === 'possession_given') {
            $query->where(function ($q) {
                $q->where('is_possession_given', 1)
                  ->orWhere('possession_status', 'Possession Given')
                  ->orWhere('possession_status', 'GIVEN');
            });
        } elseif ($scope === 'possession_pending') {
            $query->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('is_possession_given', 0)->orWhereNull('is_possession_given');
                })->where(function ($sq) {
                    $sq->whereNull('possession_status')
                      ->orWhere('possession_status', '!=', 'Possession Given')
                      ->where('possession_status', '!=', 'GIVEN');
                });
            });
        }

        $records = $query->orderBy('dist_name')->orderBy('full_name')->get();

        $filename = 'tcp_physical_possession_records_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function() use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'S.No.',
                'District Name',
                'Application No',
                'Beneficiary Name',
                'Mobile Number',
                'Flat Number',
                'Property Type',
                'Phase',
                'Physical Possession Status',
                'Handover Date'
            ]);

            foreach ($records as $index => $row) {
                $isGiven = ($row->is_possession_given == 1 || $row->possession_status === 'Possession Given' || $row->possession_status === 'GIVEN');
                fputcsv($handle, [
                    $index + 1,
                    $row->dist_name ?: 'HARYANA',
                    $row->application_number,
                    $row->full_name,
                    $row->mobile_number,
                    $row->flat_no,
                    $row->property_type ?: 'Flat',
                    $row->phase ?: '1',
                    $isGiven ? 'Possession Given' : 'Pending Possession',
                    $row->possession_given_at ? date('d-m-Y H:i', strtotime($row->possession_given_at)) : 'N/A'
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Log out from TCP session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('ews.tcp.login')->with('success', 'TCP Officer session ended successfully.');
    }
}
