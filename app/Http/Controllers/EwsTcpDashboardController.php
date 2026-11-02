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

        // Summary Statistics (Haryana Statewide) - Exclude citizen welfare allotted pool
        $stats = $this->getMatrixSummaryStats();
        unset($stats['total_allotted'], $stats['state_allotted_total']);

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

        if ($request->input('ownership_scope') === 'allotted') {
            abort(403, 'State allotted pool is confidential to Housing Department.');
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

        $flat = \App\Models\EwsBuilderFlat::where('secure_id', $id)
            ->orWhere('id', $id)
            ->first();

        if (!$flat && is_numeric(\App\Helpers\EwsHelper::decodeSecureId($id))) {
            $flat = \App\Models\EwsBuilderFlat::find(\App\Helpers\EwsHelper::decodeSecureId($id));
        }

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

        if ($request->input('ownership_scope') === 'allotted') {
            abort(403, 'State allotted pool is confidential to Housing Department.');
        }

        if ($format === 'pdf') {
            return $this->exportMatrixPdfFile($request, 'tcp_ews');
        } elseif ($format === 'excel') {
            return $this->exportMatrixExcelFile($request, 'tcp_ews');
        }

        return $this->exportMatrixCsvFile($request, 'tcp_ews');
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
