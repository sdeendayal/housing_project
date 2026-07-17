<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->hasRole('ews_department')) {
                $request->session()->regenerate();
                return redirect()->route('ews.department.dashboard');
            }
            
            // Logout if authenticated user is not ews_department
            Auth::logout();
            return redirect()->back()->with('error', 'Unauthorized: Only EWS Department accounts are permitted.');
        }

        return redirect()->back()->with('error', 'Invalid email or password.');
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $districtId = $request->input('district_id');
        $districts = DB::table('ews_districts')->orderBy('name')->get();
        
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
            'districtId'
        ));
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'all');
        $districtId = $request->input('district_id');
        $districts = DB::table('ews_districts')->orderBy('name')->get();

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
            'districtId'
        ));
    }

    public function getBeneficiaryData(Request $request)
    {
        $type = $request->input('type', 'all');
        $districtId = $request->input('district_id');

        if ($type === 'registered') {
            $query = DB::table('all_ews_data_1')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'registered' as type"), DB::raw("'Registered' as status"), 'dist_name');
        } elseif ($type === 'allotted') {
            $query = DB::table('ews_allotted_8')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', 'flat_no', DB::raw("'allotted' as type"), DB::raw("'Allotted' as status"), 'dist_name');
        } elseif ($type === 'pending') {
            $query = DB::table('ews_waiting_list_9')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', 'flat_no', DB::raw("'pending' as type"), DB::raw("'Pending' as status"), 'dist_name');
        } elseif ($type === 'rejected_ppp') {
            $query = DB::table('ews_reject_ppp_exclusion_2')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'rejected_ppp' as type"), DB::raw("'Rejected' as status"), 'dist_name');
        } elseif ($type === 'rejected_property') {
            $query = DB::table('ews_reject_property_in_india_3')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'rejected_property' as type"), DB::raw("'Rejected' as status"), 'dist_name');
        } elseif ($type === 'rejected_ownership') {
            $query = DB::table('ews_house_ownership_reject_4')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'rejected_ownership' as type"), DB::raw("'Rejected' as status"), 'dist_name');
        } elseif ($type === 'eligible_draw') {
            $query = DB::table('ews_eligible_draw_list_5')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'eligible_draw' as type"), DB::raw("'Eligible' as status"), 'dist_name');
        } elseif ($type === 'booking') {
            $query = DB::table('ews_bookings_7')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'booking' as type"), DB::raw("'Visited' as status"), 'dist_name');
        } elseif ($type === 'not_visited') {
            $query = DB::table('ews_eligible_draw_list_5')
                ->whereNotIn('mobile_number', function($q) use ($districtId) {
                    $q->select('mobile_number')->from('ews_bookings_7')->whereNotNull('mobile_number')
                        ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
                })
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'not_visited' as type"), DB::raw("'Not Visited' as status"), 'dist_name');
        } elseif ($type === 'adc_passed') {
            $query = DB::table('ews_eligible_6')
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'adc_passed' as type"), DB::raw("'Passed' as status"), 'dist_name');
        } elseif ($type === 'adc_failed') {
            $query = DB::table('ews_bookings_7')
                ->whereNotIn('application_number', function($q) use ($districtId) {
                    $q->select('application_number')->from('ews_eligible_6')->whereNotNull('application_number')
                        ->when($districtId, fn($q) => $q->where('dist_id', $districtId));
                })
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'adc_failed' as type"), DB::raw("'Failed' as status"), 'dist_name');
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
                ->select('id', 'application_number', 'full_name', 'aadhar_no', 'mobile_number', DB::raw("'N/A' as flat_no"), DB::raw("'draw_remaining' as type"), DB::raw("'Unallotted' as status"), 'dist_name');
        } else {
            $query = DB::table(DB::raw("(
                SELECT id, application_number, full_name, aadhar_no, mobile_number, flat_no, 'allotted' as type, 'Allotted' as status, dist_name, dist_id FROM ews_allotted_8
                UNION ALL
                SELECT id, application_number, full_name, aadhar_no, mobile_number, flat_no, 'pending' as type, 'Pending' as status, dist_name, dist_id FROM ews_waiting_list_9
            ) as beneficiaries"));
        }

        if ($districtId) {
            $query->where('dist_id', $districtId);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($row) {
                $viewUrl = route('ews.department.beneficiary.show', ['type' => $row->type, 'id' => $row->id]);
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

    public function showBeneficiary($type, $id)
    {
        $user = Auth::user();

        if ($type === 'registered') {
            $beneficiary = DB::table('all_ews_data_1')->where('id', $id)->first();
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'allotted') {
            $beneficiary = DB::table('ews_allotted_8')->where('id', $id)->first();
        } elseif ($type === 'pending') {
            $beneficiary = DB::table('ews_waiting_list_9')->where('id', $id)->first();
        } elseif ($type === 'rejected_ppp') {
            $beneficiary = DB::table('ews_reject_ppp_exclusion_2')->where('id', $id)->first();
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'rejected_property') {
            $beneficiary = DB::table('ews_reject_property_in_india_3')->where('id', $id)->first();
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'rejected_ownership') {
            $beneficiary = DB::table('ews_house_ownership_reject_4')->where('id', $id)->first();
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'eligible_draw') {
            $beneficiary = DB::table('ews_eligible_draw_list_5')->where('id', $id)->first();
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'booking') {
            $beneficiary = DB::table('ews_bookings_7')->where('id', $id)->first();
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'not_visited') {
            $beneficiary = DB::table('ews_eligible_draw_list_5')->where('id', $id)->first();
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'adc_passed') {
            $beneficiary = DB::table('ews_eligible_6')->where('id', $id)->first();
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'adc_failed') {
            $beneficiary = DB::table('ews_bookings_7')
                ->whereNotIn('application_number', function($q) {
                    $q->select('application_number')->from('ews_eligible_6')->whereNotNull('application_number');
                })
                ->where('id', $id)
                ->first();
            if ($beneficiary) {
                $beneficiary->flat_no = 'N/A';
            }
        } elseif ($type === 'draw_remaining') {
            $beneficiary = DB::table('ews_eligible_6')
                ->whereNotIn('application_number', function($q) {
                    $q->select('application_number')->from('ews_allotted_8')->whereNotNull('application_number');
                })
                ->whereNotIn('application_number', function($q) {
                    $q->select('application_number')->from('ews_waiting_list_9')->whereNotNull('application_number');
                })
                ->where('id', $id)
                ->first();
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
            'registered' => 'Registered',
            'allotted' => 'Allotted',
            'pending' => 'Pending',
            'rejected_ppp' => 'Rejected',
            'rejected_property' => 'Rejected',
            'rejected_ownership' => 'Rejected',
            'eligible_draw' => 'Eligible',
            'booking' => 'Visited',
            'not_visited' => 'Not Visited',
            'adc_passed' => 'Passed',
            'adc_failed' => 'Failed',
            'draw_remaining' => 'Unallotted',
        };
        $beneficiary->type = $type;

        return view('ews.department.show_beneficiary', compact('user', 'beneficiary'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('ews.department.login')->with('success', 'Logged out successfully.');
    }
}
