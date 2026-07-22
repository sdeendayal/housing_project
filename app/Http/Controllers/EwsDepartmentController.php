<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use App\Models\Role;
use App\Models\RoleType;
use App\Models\EwsBuilderFlat;
use App\Models\EwsDeveloperLog;
use App\Helpers\EwsHelper;
use Illuminate\Support\Facades\Schema;

class EwsDepartmentController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('ews_department')) {
                return redirect()->route('ews.department.dashboard');
            }
        }
        return view('ews.department.login');
    }

    public function login(Request $request)
    {
        $loginInput = $request->input('login') ?? $request->input('email');

        $request->validate([
            'email' => 'required_without:login|string',
            'login' => 'required_without:email|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $credentials = [
            $field => $loginInput,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->hasRole('ews_department')) {
                $request->session()->regenerate();
                return redirect()->route('ews.department.dashboard')->with('success', 'Logged in successfully! Welcome back, ' . $user->name);
            }
            
            // Logout if authenticated user is not ews_department
            Auth::logout();
            return redirect()->back()->with('error', 'Unauthorized: Only EWS Department accounts are permitted.');
        }

        return redirect()->back()->with('error', 'Invalid login credentials.');
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $districtId = $request->input('district_id');
        $districts = DB::table('ews_districts')->whereIn(DB::raw('LOWER(name)'), ['sonipat', 'gurugram', 'sonepat'])->orderBy('name')->get();
        
        $registeredCount = DB::table('all_ews_data_1')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $allottedCount = DB::table('ews_allotted_8')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $pendingCount = DB::table('ews_waiting_list_9')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $rejectedPppCount = DB::table('ews_reject_ppp_exclusion_2')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $rejectedPropertyCount = DB::table('ews_reject_property_in_india_3')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $rejectedOwnershipCount = DB::table('ews_house_ownership_reject_4')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $eligibleDrawCount = DB::table('ews_eligible_draw_list_5')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $bookingCount = DB::table('ews_bookings_7')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        
        $notVisitedCount = DB::table('ews_eligible_draw_list_5')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->whereNotIn('mobile_number', function($query) use ($districtId) {
                $query->select('mobile_number')->from('ews_bookings_7')->whereNotNull('mobile_number')
                    ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
            })
            ->count();

        $adcPassedCount = DB::table('ews_eligible_6')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        
        $adcFailedCount = DB::table('ews_bookings_7')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->whereNotIn('application_number', function($query) use ($districtId) {
                $query->select('application_number')->from('ews_eligible_6')->whereNotNull('application_number')
                    ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
            })
            ->count();

        $drawRemainingCount = $adcPassedCount - ($allottedCount + $pendingCount);

        $totalCount = $allottedCount + $pendingCount;

        $developerCount = User::where('role', 'ews_developer')->count();
        $developerFlatsCount = DB::table('ews_builder_flats')->count();
        $developerLogsCount = DB::table('ews_developer_logs')->count();

        return view('ews.department.dashboard', compact(
            'user', 
            'registeredCount', 
            'allottedCount', 
            'pendingCount', 
            'rejectedPppCount', 
            'rejectedPropertyCount', 
            'rejectedOwnershipCount',
            'eligibleDrawCount',
            'bookingCount',
            'notVisitedCount',
            'adcPassedCount',
            'adcFailedCount',
            'drawRemainingCount',
            'totalCount',
            'districts',
            'districtId',
            'developerCount',
            'developerFlatsCount',
            'developerLogsCount'
        ));
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'all');
        $districtId = $request->input('district_id');
        $districts = DB::table('ews_districts')->whereIn(DB::raw('LOWER(name)'), ['sonipat', 'gurugram', 'sonepat'])->orderBy('name')->get();

        $registeredCount = DB::table('all_ews_data_1')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $allottedCount = DB::table('ews_allotted_8')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $pendingCount = DB::table('ews_waiting_list_9')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $rejectedPppCount = DB::table('ews_reject_ppp_exclusion_2')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $rejectedPropertyCount = DB::table('ews_reject_property_in_india_3')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $rejectedOwnershipCount = DB::table('ews_house_ownership_reject_4')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $eligibleDrawCount = DB::table('ews_eligible_draw_list_5')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        $bookingCount = DB::table('ews_bookings_7')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        
        $notVisitedCount = DB::table('ews_eligible_draw_list_5')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->whereNotIn('mobile_number', function($query) use ($districtId) {
                $query->select('mobile_number')->from('ews_bookings_7')->whereNotNull('mobile_number')
                    ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
            })
            ->count();

        $adcPassedCount = DB::table('ews_eligible_6')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->count();
        
        $adcFailedCount = DB::table('ews_bookings_7')
            ->when($districtId, fn($q) => $q->where('dist_id', $districtId))
            ->whereNotIn('application_number', function($query) use ($districtId) {
                $query->select('application_number')->from('ews_eligible_6')->whereNotNull('application_number')
                    ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
            })
            ->count();

        $drawRemainingCount = $adcPassedCount - ($allottedCount + $pendingCount);

        $totalCount = $allottedCount + $pendingCount;

        $developerCount = User::where('role', 'ews_developer')->count();
        $developerFlatsCount = DB::table('ews_builder_flats')->count();
        $developerLogsCount = DB::table('ews_developer_logs')->count();

        return view('ews.department.list', compact(
            'user', 
            'type',
            'registeredCount', 
            'allottedCount', 
            'pendingCount', 
            'rejectedPppCount', 
            'rejectedPropertyCount', 
            'rejectedOwnershipCount',
            'eligibleDrawCount',
            'bookingCount',
            'notVisitedCount',
            'adcPassedCount',
            'adcFailedCount',
            'drawRemainingCount',
            'totalCount',
            'districts',
            'districtId',
            'developerCount',
            'developerFlatsCount',
            'developerLogsCount'
        ));
    }

    public function getBeneficiaryData(Request $request)
    {
        $type = $request->input('type', 'all');
        $districtId = $request->input('district_id');

        if ($type === 'registered') {
            $query = DB::table('all_ews_data_1')
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'registered' as type"), DB::raw("'Verify in survey app' as status"), 'dist_name');
        } elseif ($type === 'allotted') {
            $query = DB::table('ews_allotted_8')
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', 'flat_no', DB::raw("'allotted' as type"), DB::raw("'Allotted' as status"), 'dist_name');
        } elseif ($type === 'pending') {
            $query = DB::table('ews_waiting_list_9')
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', 'flat_no', DB::raw("'pending' as type"), DB::raw("'Waiting' as status"), 'dist_name');
        } elseif ($type === 'rejected_ppp') {
            $query = DB::table('ews_reject_ppp_exclusion_2')
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'rejected_ppp' as type"), DB::raw("'Rejected' as status"), 'dist_name');
        } elseif ($type === 'rejected_property') {
            $query = DB::table('ews_reject_property_in_india_3')
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'rejected_property' as type"), DB::raw("'Rejected' as status"), 'dist_name');
        } elseif ($type === 'rejected_ownership') {
            $query = DB::table('ews_house_ownership_reject_4')
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'rejected_ownership' as type"), DB::raw("'Rejected' as status"), 'dist_name');
        } elseif ($type === 'eligible_draw') {
            $query = DB::table('ews_eligible_draw_list_5')
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'eligible_draw' as type"), DB::raw("'Eligible for booking' as status"), 'dist_name');
        } elseif ($type === 'booking') {
            $query = DB::table('ews_bookings_7')
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'booking' as type"), DB::raw("'Booking Amount Received' as status"), 'dist_name');
        } elseif ($type === 'not_visited') {
            $query = DB::table('ews_eligible_draw_list_5')
                ->whereNotIn('mobile_number', function($q) use ($districtId) {
                    $q->select('mobile_number')->from('ews_bookings_7')->whereNotNull('mobile_number')
                        ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
                })
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'not_visited' as type"), DB::raw("'Booking Amount Not Received' as status"), 'dist_name');
        } elseif ($type === 'adc_passed') {
            $query = DB::table('ews_eligible_6')
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'adc_passed' as type"), DB::raw("'Eligible' as status"), 'dist_name');
        } elseif ($type === 'adc_failed') {
            $query = DB::table('ews_bookings_7')
                ->whereNotIn('application_number', function($q) use ($districtId) {
                    $q->select('application_number')->from('ews_eligible_6')->whereNotNull('application_number')
                        ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
                })
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'adc_failed' as type"), DB::raw("'Not Eligible' as status"), 'dist_name');
        } elseif ($type === 'draw_remaining') {
            $query = DB::table('ews_eligible_6')
                ->whereNotIn('application_number', function($q) use ($districtId) {
                    $q->select('application_number')->from('ews_allotted_8')->whereNotNull('application_number')
                        ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
                })
                ->whereNotIn('application_number', function($q) use ($districtId) {
                    $q->select('application_number')->from('ews_waiting_list_9')->whereNotNull('application_number')
                        ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
                })
                ->select('secure_id', 'id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'draw_remaining' as type"), DB::raw("'Unallotted' as status"), 'dist_name');
        } else {
            $query = DB::table(DB::raw("(
                SELECT secure_id, id, application_number, full_name, aadhar_no, mobile_number, flat_no, 'allotted' as type, 'Allotted' as status, dist_name, dist_id FROM ews_allotted_8
                UNION ALL
                SELECT secure_id, id, application_number, full_name, aadhar_no, mobile_number, flat_no, 'pending' as type, 'Waiting' as status, dist_name, dist_id FROM ews_waiting_list_9
            ) as beneficiaries"));
        }

        if ($districtId) {
            $query->where('dist_id', $districtId);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($row) {
                $secId = !empty($row->secure_id) ? $row->secure_id : EwsHelper::encodeSecureId($row->id);
                $viewUrl = route('ews.department.beneficiary.show', ['type' => $row->type, 'secure_id' => $secId]);
                return '
                    <div class="text-right">
                        <a href="'.$viewUrl.'" class="px-2.5 py-1.5 bg-orange-50 hover:bg-orange-500 hover:text-white text-orange-600 rounded-lg text-[9px] font-black uppercase transition-all shadow-sm border border-orange-100">
                            View Details
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showBeneficiary($type, $secureId)
    {
        $user = Auth::user();

        $fetchBySecId = function($tableName) use ($secureId) {
            return DB::table($tableName)->where('secure_id', $secureId)->first();
        };

        if ($type === 'registered') {
            $beneficiary = $fetchBySecId('all_ews_data_1');
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'allotted') {
            $beneficiary = $fetchBySecId('ews_allotted_8');
        } elseif ($type === 'pending') {
            $beneficiary = $fetchBySecId('ews_waiting_list_9');
        } elseif ($type === 'rejected_ppp') {
            $beneficiary = $fetchBySecId('ews_reject_ppp_exclusion_2');
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'rejected_property') {
            $beneficiary = $fetchBySecId('ews_reject_property_in_india_3');
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'rejected_ownership') {
            $beneficiary = $fetchBySecId('ews_house_ownership_reject_4');
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'eligible_draw') {
            $beneficiary = $fetchBySecId('ews_eligible_draw_list_5');
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'booking') {
            $beneficiary = $fetchBySecId('ews_bookings_7');
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'not_visited') {
            $beneficiary = $fetchBySecId('ews_eligible_draw_list_5');
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'adc_passed') {
            $beneficiary = $fetchBySecId('ews_eligible_6');
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'adc_failed') {
            $beneficiary = DB::table('ews_bookings_7')
                ->where('secure_id', $secureId)
                ->first();
            if (!$beneficiary) {
                $beneficiary = $fetchBySecId('ews_bookings_7');
            }
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'draw_remaining') {
            $beneficiary = $fetchBySecId('ews_eligible_6');
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } else {
            abort(404, 'Invalid beneficiary type.');
        }

        if (!$beneficiary) {
            abort(404, 'Beneficiary record not found.');
        }

        // Match mobile or application number in all_ews_data_1 to get full record
        $fullRecord = null;
        if (!empty($beneficiary->mobile_number)) {
            $fullRecord = DB::table('all_ews_data_1')->where('mobile_number', $beneficiary->mobile_number)->first();
        }
        if (!$fullRecord && !empty($beneficiary->application_number)) {
            $fullRecord = DB::table('all_ews_data_1')->where('application_number', $beneficiary->application_number)->first();
        }

        // Merge full details into beneficiary object
        if ($fullRecord) {
            foreach (get_object_vars($fullRecord) as $key => $value) {
                if (!isset($beneficiary->$key) || $beneficiary->$key === null || $beneficiary->$key === '') {
                    $beneficiary->$key = $value;
                }
            }
        }

        $beneficiary->status = match ($type) {
            'registered' => 'Verify in survey app',
            'allotted' => 'Allotted',
            'pending' => 'Waiting',
            'rejected_ppp' => 'Rejected',
            'rejected_property' => 'Rejected',
            'rejected_ownership' => 'Rejected',
            'eligible_draw' => 'Eligible for booking',
            'booking' => 'Booking Amount Received',
            'not_visited' => 'Booking Amount Not Received',
            'adc_passed' => 'Eligible',
            'adc_failed' => 'Not Eligible',
            'draw_remaining' => 'Unallotted',
        };
        $beneficiary->type = $type;

        return view('ews.department.show_beneficiary', compact('user', 'beneficiary', 'type'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('ews.department.login')->with('success', 'Logged out successfully.');
    }

    // ─── EWS DEVELOPER MANAGEMENT METHODS ────────────────────────────

    public function developersIndex(Request $request)
    {
        $user = Auth::user();
        $districts = DB::table('ews_districts')->orderBy('name')->get();
        $developerCount = User::where('role', 'ews_developer')->count();
        $developerFlatsCount = DB::table('ews_builder_flats')->count();
        $developerLogsCount = DB::table('ews_developer_logs')->count();

        return view('ews.department.developers.index', compact(
            'user', 'districts', 'developerCount', 'developerFlatsCount', 'developerLogsCount'
        ));
    }

    public function getDevelopersData(Request $request)
    {
        $query = User::where('role', 'ews_developer')->orderBy('id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('flats_count', function ($row) {
                return DB::table('ews_builder_flats')->where('created_by', $row->id)->count();
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->Is_Active == '1') {
                    return '<span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[9px] font-black uppercase tracking-wide">Active</span>';
                }
                return '<span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-full text-[9px] font-black uppercase tracking-wide">Inactive</span>';
            })
            ->addColumn('actions', function ($row) {
                $secureId = !empty($row->secure_id) ? $row->secure_id : EwsHelper::encodeSecureId($row->id);
                $editDataStr = htmlspecialchars(json_encode([
                    'secure_id' => $secureId,
                    'name' => $row->name,
                    'email' => $row->email,
                    'mobile' => $row->mobile,
                    'district_name' => $row->district_name,
                    'Is_Active' => $row->Is_Active,
                ]), ENT_QUOTES, 'UTF-8');

                $deleteRoute = route('ews.department.developers.destroy', $secureId);
                $csrf = csrf_field();
                $method = method_field('DELETE');

                return '
                    <div class="flex items-center justify-end gap-2">
                        <button type="button" onclick=\'openEditModal('.$editDataStr.')\' class="px-2.5 py-1.5 bg-sky-50 text-sky-600 border border-sky-100 hover:bg-sky-500 hover:text-white rounded-lg text-[9px] font-black uppercase transition shadow-sm flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">edit</span> Edit
                        </button>
                        <form id="delete-form-'.$secureId.'" action="'.$deleteRoute.'" method="POST" class="inline m-0">
                            '.$csrf.'
                            '.$method.'
                            <button type="button" onclick="confirmDelete(\''.$secureId.'\')" class="px-2.5 py-1.5 bg-rose-50 text-rose-600 border border-rose-100 hover:bg-rose-500 hover:text-white rounded-lg text-[9px] font-black uppercase transition shadow-sm flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">delete</span> Delete
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function storeDeveloper(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|digits:10|unique:users,mobile',
            'district_name' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        $devRole = Role::where('slug', 'ews_developer')->first();
        if (!$devRole) {
            $devRole = Role::create([
                'name' => 'EWS Developer',
                'slug' => 'ews_developer',
                'dashboard_route' => 'ews.developer.dashboard',
                'Is_Active' => '1',
                'Is_Deleted' => '0',
            ]);
        }

        $secureId = md5(uniqid("dev_" . microtime() . rand(), true));

        $districtId = null;
        $districtName = $request->district_name ?? 'SONIPAT';
        if (!empty($districtName)) {
            $dist = DB::table('ews_districts')->where('name', strtoupper(trim($districtName)))->orWhere('id', $districtName)->first();
            if ($dist) {
                $districtId = $dist->id;
                $districtName = $dist->name;
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'role' => 'ews_developer',
            'scheme' => 'EWS',
            'Is_Active' => $request->input('Is_Active', '1'),
            'Is_Deleted' => '0',
            'district_id' => $districtId,
            'district_name' => $districtName,
            'secure_id' => $secureId,
        ]);

        RoleType::create([
            'user_id' => $user->id,
            'role_id' => $devRole->id,
            'Is_Active' => '1',
            'Is_Deleted' => '0',
        ]);

        // Log Developer Creation Activity
        EwsDeveloperLog::create([
            'user_id' => Auth::id(),
            'action' => 'DEVELOPER_CREATED',
            'details' => "Department Admin ('".(Auth::user()->name ?? 'Admin')."') created Developer Account '{$user->name}' (Mobile: {$user->mobile}, District: {$user->district_name}) with status " . ($user->Is_Active == '1' ? 'ACTIVE' : 'INACTIVE'),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Developer account created successfully!');
    }

    public function updateDeveloper(Request $request, $secureId)
    {
        $user = User::where('role', 'ews_developer')->where('secure_id', $secureId)->firstOrFail();

        $oldActive = $user->Is_Active;
        $oldName = $user->name;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'mobile' => 'required|string|digits:10|unique:users,mobile,'.$user->id,
            'district_name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;

        $reqDist = $request->district_name ?? $user->district_name;
        if (!empty($reqDist)) {
            $dist = DB::table('ews_districts')->where('name', strtoupper(trim($reqDist)))->orWhere('id', $reqDist)->first();
            if ($dist) {
                $user->district_id = $dist->id;
                $user->district_name = $dist->name;
            } else {
                $user->district_name = $reqDist;
            }
        }

        $user->Is_Active = $request->input('Is_Active', '1');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Log Update / Status Change Activity
        $actionName = 'DEVELOPER_UPDATED';
        $statusNote = '';
        if ($oldActive !== $user->Is_Active) {
            if ($user->Is_Active == '1') {
                $actionName = 'DEVELOPER_ACTIVATED';
                $statusNote = " (Status changed from INACTIVE to ACTIVE)";
            } else {
                $actionName = 'DEVELOPER_DEACTIVATED';
                $statusNote = " (Status changed from ACTIVE to INACTIVE)";
            }
        }

        EwsDeveloperLog::create([
            'user_id' => Auth::id(),
            'action' => $actionName,
            'details' => "Department Admin ('".(Auth::user()->name ?? 'Admin')."') updated Developer Account '{$user->name}' (Mobile: {$user->mobile}, District: {$user->district_name})" . $statusNote,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Developer account updated successfully!');
    }

    public function destroyDeveloper($secureId)
    {
        $user = User::where('role', 'ews_developer')->where('secure_id', $secureId)->firstOrFail();
        $devName = $user->name;
        $devMobile = $user->mobile;

        RoleType::where('user_id', $user->id)->delete();
        $user->delete();

        // Log Delete Activity
        EwsDeveloperLog::create([
            'user_id' => Auth::id(),
            'action' => 'DEVELOPER_DELETED',
            'details' => "Department Admin ('".(Auth::user()->name ?? 'Admin')."') deleted Developer Account '{$devName}' (Mobile: {$devMobile})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()->with('success', 'Developer account deleted successfully!');
    }

    public function developerFlatsIndex(Request $request)
    {
        $user = Auth::user();
        $districts = DB::table('ews_districts')->orderBy('name')->get();
        $developerCount = User::where('role', 'ews_developer')->count();
        $developerFlatsCount = DB::table('ews_builder_flats')->count();
        $developerLogsCount = DB::table('ews_developer_logs')->count();

        return view('ews.department.developers.flats', compact(
            'user', 'districts', 'developerCount', 'developerFlatsCount', 'developerLogsCount'
        ));
    }

    public function getDeveloperFlatsData(Request $request)
    {
        $query = DB::table('ews_builder_flats as f')
            ->leftJoin('users as u', 'f.created_by', '=', 'u.id')
            ->select('f.*', 'u.name as developer_name', 'u.mobile as developer_mobile')
            ->orderBy('f.id', 'desc');

        if ($request->filled('district_id')) {
            $query->where('f.district_id', $request->district_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('created_by_info', function ($row) {
                if ($row->developer_name) {
                    return '<span class="font-bold text-slate-800 uppercase">'.$row->developer_name.'</span><br><span class="font-mono text-[9px] text-slate-400">'.$row->developer_mobile.'</span>';
                }
                return '<span class="text-slate-400 font-mono text-[10px]">System Seeded</span>';
            })
            ->rawColumns(['created_by_info'])
            ->make(true);
    }

    public function developerLogsIndex(Request $request)
    {
        $user = Auth::user();
        $districts = DB::table('ews_districts')->orderBy('name')->get();
        $developerCount = User::where('role', 'ews_developer')->count();
        $developerFlatsCount = DB::table('ews_builder_flats')->count();
        $developerLogsCount = DB::table('ews_developer_logs')->count();

        return view('ews.department.developers.logs', compact(
            'user', 'districts', 'developerCount', 'developerFlatsCount', 'developerLogsCount'
        ));
    }

    public function getDeveloperLogsData(Request $request)
    {
        $query = DB::table('ews_developer_logs as l')
            ->leftJoin('users as u', 'l.user_id', '=', 'u.id')
            ->select('l.*', 'u.name as developer_name', 'u.mobile as developer_mobile')
            ->orderBy('l.id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('developer_info', function ($row) {
                if ($row->developer_name) {
                    return '<span class="font-bold text-slate-800 uppercase">'.$row->developer_name.'</span><br><span class="font-mono text-[9px] text-slate-400">'.$row->developer_mobile.'</span>';
                }
                return '<span class="text-slate-400 font-mono text-[10px]">User #'.$row->user_id.'</span>';
            })
            ->addColumn('action_badge', function ($row) {
                return '<span class="px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-lg text-[9px] font-mono font-extrabold uppercase">'.$row->action.'</span>';
            })
            ->rawColumns(['developer_info', 'action_badge'])
            ->make(true);
    }

    public function exportBeneficiaries(Request $request)
    {
        $type = $request->input('type', 'all');
        $districtId = $request->input('district_id');
        $search = $request->input('search');
        $format = strtolower($request->input('format', 'csv'));

        if ($type === 'registered') {
            $query = DB::table('all_ews_data_1')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'Verify in survey app' as status"), 'dist_name');
        } elseif ($type === 'allotted') {
            $query = DB::table('ews_allotted_8')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', 'flat_no', DB::raw("'Allotted' as status"), 'dist_name');
        } elseif ($type === 'pending') {
            $query = DB::table('ews_waiting_list_9')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', 'flat_no', DB::raw("'Waiting' as status"), 'dist_name');
        } elseif ($type === 'rejected_ppp') {
            $query = DB::table('ews_reject_ppp_exclusion_2')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'Rejected' as status"), 'dist_name');
        } elseif ($type === 'rejected_property') {
            $query = DB::table('ews_reject_property_in_india_3')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'Rejected' as status"), 'dist_name');
        } elseif ($type === 'rejected_ownership') {
            $query = DB::table('ews_house_ownership_reject_4')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'Rejected' as status"), 'dist_name');
        } elseif ($type === 'eligible_draw') {
            $query = DB::table('ews_eligible_draw_list_5')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'Eligible for booking' as status"), 'dist_name');
        } elseif ($type === 'booking') {
            $query = DB::table('ews_bookings_7')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'Booking Amount Received' as status"), 'dist_name');
        } elseif ($type === 'not_visited') {
            $query = DB::table('ews_eligible_draw_list_5')
                ->whereNotIn('mobile_number', function($q) use ($districtId) {
                    $q->select('mobile_number')->from('ews_bookings_7')->whereNotNull('mobile_number')
                        ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
                })
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'Booking Amount Not Received' as status"), 'dist_name');
        } elseif ($type === 'adc_passed') {
            $query = DB::table('ews_eligible_6')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'Eligible' as status"), 'dist_name');
        } elseif ($type === 'adc_failed') {
            $query = DB::table('ews_bookings_7')
                ->whereNotIn('application_number', function($q) use ($districtId) {
                    $q->select('application_number')->from('ews_eligible_6')->whereNotNull('application_number')
                        ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
                })
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'Not Eligible' as status"), 'dist_name');
        } elseif ($type === 'draw_remaining') {
            $query = DB::table('ews_eligible_6')
                ->whereNotIn('application_number', function($q) use ($districtId) {
                    $q->select('application_number')->from('ews_allotted_8')->whereNotNull('application_number')
                        ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
                })
                ->whereNotIn('application_number', function($q) use ($districtId) {
                    $q->select('application_number')->from('ews_waiting_list_9')->whereNotNull('application_number')
                        ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
                })
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'Unallotted' as status"), 'dist_name');
        } else {
            $query = DB::table(DB::raw("(
                SELECT id, application_number, full_name, aadhar_no, mobile_number, flat_no, 'allotted' as type, 'Allotted' as status, dist_name, dist_id FROM ews_allotted_8
                UNION ALL
                SELECT id, application_number, full_name, aadhar_no, mobile_number, flat_no, 'pending' as type, 'Waiting' as status, dist_name, dist_id FROM ews_waiting_list_9
            ) as beneficiaries"));
        }

        if ($districtId) {
            $query->where('dist_id', $districtId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('aadhar_no', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%")
                  ->orWhere('dist_name', 'like', "%{$search}%");
            });
        }

        $records = $query->limit(50000)->get();
        $filename = "ews_beneficiaries_{$type}_" . date('Y-m-d_H-i') . "." . ($format === 'pdf' ? 'html' : 'csv');

        $headers = ['S.No.', 'Application Number', 'Full Name', 'District', 'Aadhar Number', 'Mobile Number'];
        if ($type === 'allotted') {
            $headers[] = 'Flat Number';
        }
        $headers[] = 'Status';

        $mappedData = $records->map(function($row, $i) use ($type) {
            $item = [
                $i + 1,
                $row->application_number ?? 'N/A',
                $row->full_name ?? 'N/A',
                $row->dist_name ?? 'N/A',
                $row->aadhar_no ?? 'N/A',
                $row->mobile_number ?? 'N/A',
            ];
            if ($type === 'allotted') {
                $item[] = $row->flat_no ?? 'N/A';
            }
            $item[] = $row->status ?? 'N/A';
            return $item;
        });

        $typeTitle = $type;
        if ($type === 'registered') $typeTitle = 'Verify in survey app';
        elseif ($type === 'pending') $typeTitle = 'Waiting';
        elseif ($type === 'eligible_draw') $typeTitle = 'Eligible for booking';
        elseif ($type === 'booking') $typeTitle = 'Booking Amount Received';
        elseif ($type === 'not_visited') $typeTitle = 'Booking Amount Not Received';
        elseif ($type === 'adc_passed') $typeTitle = 'Eligible';
        elseif ($type === 'adc_failed') $typeTitle = 'Not Eligible';

        if ($format === 'pdf') {
            return $this->renderPrintPdfResponse("EWS BENEFICIARIES REGISTRY - " . strtoupper($typeTitle), $headers, $mappedData);
        }

        $exportFilename = "ews_beneficiaries_" . str_replace(' ', '_', strtolower($typeTitle)) . "_" . date('Y-m-d_H-i') . ".csv";
        return $this->streamCsvResponse($exportFilename, $headers, $mappedData);
    }

    public function exportDevelopers(Request $request)
    {
        $search = $request->input('search');
        $format = strtolower($request->input('format', 'csv'));

        $query = User::where('role', 'ews_developer')
            ->orderBy('id', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('district_name', 'like', "%{$search}%");
            });
        }

        $records = $query->get();
        $filename = "ews_developers_" . date('Y-m-d_H-i') . ".csv";

        if ($format === 'pdf') {
            return $this->renderPrintPdfResponse("EWS DEVELOPER ACCOUNTS REPORT", [
                'S.No.', 'Developer Name', 'Mobile ID', 'Email Address', 'District', 'Flat Submissions', 'Status'
            ], $records->map(function($row, $i) {
                $flatsCount = DB::table('ews_builder_flats')->where('created_by', $row->id)->count();
                return [
                    $i + 1,
                    $row->name,
                    $row->mobile,
                    $row->email,
                    strtoupper($row->district_name ?? 'N/A'),
                    $flatsCount,
                    $row->Is_Active ? 'Active' : 'Inactive',
                ];
            }));
        }

        return $this->streamCsvResponse($filename, ['S.No.', 'Developer Name', 'Mobile ID', 'Email Address', 'District', 'Flat Submissions', 'Status'], $records->map(function($row, $i) {
            $flatsCount = DB::table('ews_builder_flats')->where('created_by', $row->id)->count();
            return [
                $i + 1,
                $row->name,
                $row->mobile,
                $row->email,
                strtoupper($row->district_name ?? 'N/A'),
                $flatsCount,
                $row->Is_Active ? 'Active' : 'Inactive',
            ];
        }));
    }

    public function exportDeveloperFlats(Request $request)
    {
        $districtId = $request->input('district_id');
        $search = $request->input('search');
        $format = strtolower($request->input('format', 'csv'));

        $query = EwsBuilderFlat::with(['creator', 'district'])
            ->orderBy('id', 'desc');

        if ($districtId) {
            $query->where('district_id', $districtId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhere('block_tower_number', 'like', "%{$search}%")
                  ->orWhere('flat_number', 'like', "%{$search}%")
                  ->orWhere('town_name', 'like', "%{$search}%");
            });
        }

        $records = $query->get();
        $filename = "ews_builder_flats_" . date('Y-m-d_H-i') . ".csv";

        if ($format === 'pdf') {
            return $this->renderPrintPdfResponse("EWS BUILDER FLAT SUBMISSIONS REPORT", [
                'S.No.', 'District', 'Town Name', 'Project Name', 'Block / Tower', 'Floor', 'Flat Number', 'Submitted By Developer'
            ], $records->map(function($row, $i) {
                return [
                    $i + 1,
                    strtoupper($row->district->name ?? 'N/A'),
                    strtoupper($row->town_name ?? 'N/A'),
                    strtoupper($row->project_name ?? 'N/A'),
                    $row->block_tower_number ?? 'N/A',
                    $row->floor ?? 'N/A',
                    $row->flat_number ?? 'N/A',
                    ($row->creator->name ?? 'N/A') . ' (' . ($row->creator->mobile ?? '') . ')'
                ];
            }));
        }

        return $this->streamCsvResponse($filename, ['S.No.', 'District', 'Town Name', 'Project Name', 'Block / Tower', 'Floor', 'Flat Number', 'Submitted By Developer'], $records->map(function($row, $i) {
            return [
                $i + 1,
                strtoupper($row->district->name ?? 'N/A'),
                strtoupper($row->town_name ?? 'N/A'),
                strtoupper($row->project_name ?? 'N/A'),
                $row->block_tower_number ?? 'N/A',
                $row->floor ?? 'N/A',
                $row->flat_number ?? 'N/A',
                ($row->creator->name ?? 'N/A') . ' (' . ($row->creator->mobile ?? '') . ')'
            ];
        }));
    }

    public function exportDeveloperLogs(Request $request)
    {
        $search = $request->input('search');
        $format = strtolower($request->input('format', 'csv'));

        $query = DB::table('ews_developer_logs as l')
            ->leftJoin('users as u', 'l.user_id', '=', 'u.id')
            ->select('l.*', 'u.name as developer_name', 'u.mobile as developer_mobile')
            ->orderBy('l.id', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('l.action', 'like', "%{$search}%")
                  ->orWhere('l.details', 'like', "%{$search}%")
                  ->orWhere('l.ip_address', 'like', "%{$search}%")
                  ->orWhere('u.name', 'like', "%{$search}%")
                  ->orWhere('u.mobile', 'like', "%{$search}%");
            });
        }

        $records = $query->limit(10000)->get();
        $filename = "ews_developer_logs_" . date('Y-m-d_H-i') . ".csv";

        if ($format === 'pdf') {
            return $this->renderPrintPdfResponse("EWS DEVELOPER ACTIVITY LOGS REPORT", [
                'S.No.', 'Developer Name', 'Mobile ID', 'Action', 'Action Details', 'IP Address', 'Timestamp'
            ], $records->map(function($row, $i) {
                return [
                    $i + 1,
                    $row->developer_name ?? ('User #' . $row->user_id),
                    $row->developer_mobile ?? 'N/A',
                    strtoupper($row->action),
                    $row->details,
                    $row->ip_address,
                    $row->created_at,
                ];
            }));
        }

        return $this->streamCsvResponse($filename, ['S.No.', 'Developer Name', 'Mobile ID', 'Action', 'Action Details', 'IP Address', 'Timestamp'], $records->map(function($row, $i) {
            return [
                $i + 1,
                $row->developer_name ?? ('User #' . $row->user_id),
                $row->developer_mobile ?? 'N/A',
                strtoupper($row->action),
                $row->details,
                $row->ip_address,
                $row->created_at,
            ];
        }));
    }

    private function streamCsvResponse($filename, $headers, $data)
    {
        $responseHeaders = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $responseHeaders);
    }

    private function renderPrintPdfResponse($title, $headers, $data)
    {
        $rowsHtml = '';
        foreach ($data as $row) {
            $rowsHtml .= '<tr>';
            foreach ($row as $cell) {
                $rowsHtml .= '<td style="border: 1px solid #cbd5e1; padding: 8px; font-size: 11px;">' . htmlspecialchars($cell) . '</td>';
            }
            $rowsHtml .= '</tr>';
        }

        $headerHtml = '';
        foreach ($headers as $h) {
            $headerHtml .= '<th style="border: 1px solid #94a3b8; padding: 10px; background: #f1f5f9; font-size: 10px; text-transform: uppercase;">' . htmlspecialchars($h) . '</th>';
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>' . htmlspecialchars($title) . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; color: #1e293b; }
                .header { text-align: center; border-bottom: 2px solid #ea580c; padding-bottom: 15px; margin-bottom: 20px; }
                .header h2 { margin: 0; color: #ea580c; text-transform: uppercase; font-size: 18px; }
                .header p { margin: 5px 0 0 0; font-size: 12px; color: #64748b; font-weight: bold; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body onload="window.print()">
            <div class="no-print" style="margin-bottom: 15px; text-align: right;">
                <button onclick="window.print()" style="background: #ea580c; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">Print / Save as PDF</button>
            </div>
            <div class="header">
                <h2>HOUSING FOR ALL DEPARTMENT, HARYANA</h2>
                <p>' . htmlspecialchars($title) . ' (Generated on: ' . date('d-M-Y H:i A') . ')</p>
            </div>
            <table>
                <thead><tr>' . $headerHtml . '</tr></thead>
                <tbody>' . $rowsHtml . '</tbody>
            </table>
        </body>
        </html>
        ';

        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    public function showProfile($secureId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'ews_department') {
            abort(403);
        }

        if ($user->secure_id !== $secureId) {
            return redirect()->route('ews.department.profile.show', $user->secure_id);
        }

        return view('ews.department.profile', compact('user'));
    }

    public function updateProfile(Request $request, $secureId)
    {
        $user = User::where('role', 'ews_department')->where('secure_id', $secureId)->firstOrFail();

        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized profile update action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|digits:10',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $changes = [];
        $oldName = $user->name;

        if ($oldName !== $request->name) {
            $changes[] = "Name updated from '{$oldName}' to '{$request->name}'";
            $user->name = $request->name;
        }

        if ($request->filled('mobile') && $user->mobile !== $request->mobile) {
            $oldMobile = $user->mobile ?? 'NOT SET';
            $changes[] = "Mobile number updated from '{$oldMobile}' to '{$request->mobile}'";
            $user->mobile = $request->mobile;
        }

        if ($request->filled('password')) {
            $changes[] = "Password credential updated";
            $user->password = Hash::make($request->password);
        }

        if (empty($changes)) {
            return redirect()->back()->with('success', 'No changes were made to profile.');
        }

        $user->save();

        $changeSummary = implode('; ', $changes);

        // Create log entry in ews_developer_logs
        EwsDeveloperLog::create([
            'user_id' => $user->id,
            'action' => 'DEPT_ADMIN_PROFILE_UPDATED',
            'details' => "Department Admin Profile Updated for user '{$user->email}': {$changeSummary}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Department Admin profile updated successfully. Logged: ' . $changeSummary);
    }

    public function seederDataIndex(Request $request)
    {
        $user = Auth::user();
        $districtId = $request->query('district') ?? $request->query('district_id') ?? '';

        // Fetch counts for sidebar
        $totalCount = DB::table('ews_allotted_8')->when($districtId, fn($q) => $q->where('dist_id', $districtId))->count() +
            DB::table('ews_waiting_list_9')->when($districtId, fn($q) => $q->where('dist_id', $districtId))->count();
            
        $allottedCount = DB::table('ews_allotted_8')->when($districtId, fn($q) => $q->where('dist_id', $districtId))->count();
        $pendingCount = DB::table('ews_waiting_list_9')->when($districtId, fn($q) => $q->where('dist_id', $districtId))->count();
        
        $adcPassedCount = DB::table('ews_eligible_6')->when($districtId, fn($q) => $q->where('dist_id', $districtId))->count();
        $drawRemainingCount = $adcPassedCount - ($allottedCount + $pendingCount);
        
        $developerCount = User::where('role', 'ews_developer')->count();
        $developerFlatsCount = DB::table('ews_builder_flats')->count();
        $developerLogsCount = DB::table('ews_developer_logs')->count();
        
        $currentType = 'seeder';

        // EWS Raw Files configuration using exact original filenames
        $rawFiles = [
            [
                'name' => 'Registered Applicants Master (1. Verify in survey app)',
                'filename' => 'SurveyData_Sonipat_updated exclusion by ashish CRID.xlsx',
                'description' => 'Original master Excel file containing all citizen housing registrations, full applicant details, demographics, and assets data.',
                'sheets' => 'registered'
            ],
            [
                'name' => 'Survey Exclusions & Verification Master (2, 3, 4, 7, 8, 9 Stages)',
                'filename' => 'survey.xlsx',
                'description' => 'Comprehensive master database containing all EWS funnel tabs: exclusions, property checks, house ownership, draw lists, bookings, allotments, and waiting lists.',
                'sheets' => 'exclusion, prop, house, draw, eligible, booking, allotted, waiting'
            ],
            [
                'name' => 'Eligible Draw List Database (5. Eligible for booking)',
                'filename' => '794 eligible list with category for sonipat draw.xlsx',
                'description' => 'Excel registry containing verified candidates qualified for the lottery draw.',
                'sheets' => 'draw_eligible'
            ],
            [
                'name' => 'Developer Draw Allotments',
                'filename' => 'final draw sheet fo developeres.xlsx',
                'description' => 'Official draw list sheet structured developer-wise containing sector alignments, towers, and flat allocations.',
                'sheets' => 'developer_draw'
            ],
            [
                'name' => 'Master Draw Sonipat',
                'filename' => 'Master sheet for draw sonipat.xlsx',
                'description' => 'The raw draw outcome sheets filtered specifically for Sonipat region.',
                'sheets' => 'master_sonipat'
            ],
            [
                'name' => 'EWS Approved Flat Masters',
                'filename' => 'booking amount  flat final recevied data from sunit ji.xlsx',
                'description' => 'Inventory ledger mapping flat IDs, developer codes, sector allocations, and CRID verification keys.',
                'sheets' => 'flats_crid'
            ]
        ];

        // Hydrate files info (size, last modified time)
        $files = [];
        foreach ($rawFiles as $file) {
            $filename = $file['filename'];
            $path = database_path('seeders/data/' . $filename);
            
            if (file_exists($path)) {
                $bytes = filesize($path);
                $file['size'] = round($bytes / 1024, 1) . ' KB';
                if ($bytes >= 1048576) {
                    $file['size'] = round($bytes / 1048576, 2) . ' MB';
                }
                $file['modified'] = date('Y-m-d H:i:s', filemtime($path));
                $file['exists'] = true;
            } else {
                $file['size'] = 'N/A';
                $file['modified'] = 'N/A';
                $file['exists'] = false;
            }
            $files[] = $file;
        }

        return view('ews.department.seeder_data', compact(
            'user', 'files', 'districtId',
            'totalCount', 'allottedCount', 'pendingCount', 'drawRemainingCount',
            'developerCount', 'developerFlatsCount', 'developerLogsCount', 'currentType'
        ));
    }

    public function downloadSeederFile($filename)
    {
        $allowedFiles = [
            'SurveyData_Sonipat_updated exclusion by ashish CRID.xlsx',
            'survey.xlsx',
            '794 eligible list with category for sonipat draw.xlsx',
            'final draw sheet fo developeres.xlsx',
            'Master sheet for draw sonipat.xlsx',
            'booking amount  flat final recevied data from sunit ji.xlsx'
        ];

        if (!in_array($filename, $allowedFiles)) {
            abort(403, 'Unauthorized file access.');
        }

        $path = database_path('seeders/data/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'Requested raw Excel file not found on disk.');
        }

        return response()->download($path);
    }

    private function findDistrictHeaderRowAndCol($sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        $searchTerms = ['dist_name', 'districtname', 'district_name', 'district', 'dist', 'dist_id'];
        
        // Search first 5 rows
        for ($row = 1; $row <= 5; $row++) {
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellVal = strtolower(trim($sheet->getCell([$col, $row])->getValue() ?? ''));
                if (in_array($cellVal, $searchTerms)) {
                    return ['row' => $row, 'col' => $col];
                }
            }
        }
        return null;
    }

    private function isMatchingDistrict($value, $selectedDistrict)
    {
        if (empty($value)) return true; // Keep empty or metadata rows intact
        $v = strtolower(trim($value));
        $sel = strtolower(trim($selectedDistrict));
        if ($sel === 'sonipat') {
            return in_array($v, ['sonipat', 'sonepat', 'snp']);
        }
        return $v === $sel;
    }
}
