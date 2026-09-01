<?php

namespace App\Http\Controllers\MMGAY\Bdo;

use App\Http\Controllers\Controller;
use App\Models\MmgayPossessionApplication;
use App\Models\MmgayPossessionStatusLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MMGAYBdoPossessionController extends Controller
{
    /**
     * Show BDO Login Form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->scheme === 'MMGAY' && $user->hasRole('mmgav_bdeo')) {
                return redirect()->route('mmgay.bdo.dashboard');
            }
        }

        $captcha = rand(1000, 9999);
        session(['bdo_captcha' => $captcha]);

        return view('mmgay.bdo.login', compact('captcha'));
    }

    /**
     * Refresh BDO Captcha.
     */
    public function refreshCaptcha()
    {
        $captcha = rand(1000, 9999);
        session(['bdo_captcha' => $captcha]);
        return response()->json(['captcha' => $captcha]);
    }

    /**
     * Handle BDO Login Submit.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required',
        ]);

        if ($request->captcha != session('bdo_captcha')) {
            return back()
                ->withInput()
                ->with('error', 'Invalid captcha.');
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'scheme' => 'MMGAY',
            'Is_Active' => '1',
            'Is_Deleted' => '0',
        ];

        if (!Auth::attempt($credentials)) {
            return back()
                ->withInput()
                ->with('error', 'Invalid BDO login credentials.');
        }

        $user = Auth::user();
        if (!$user->hasRole('mmgav_bdeo')) {
            Auth::logout();
            return back()
                ->withInput()
                ->with('error', 'Unauthorized. Access is restricted to MMGAV BDO officers only.');
        }

        $request->session()->regenerate();

        return redirect()
            ->route('mmgay.bdo.dashboard')
            ->with('success', 'Welcome BDO Officer.');
    }

    /**
     * Handle BDO Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mmgay.bdo.login')
            ->with('success', 'Logged out successfully.');
    }

    /**
     * Show BDO Dashboard.
     */
    public function dashboard(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        // dd($blockMasterId);

        $ppaQuery = DB::table('mmgay_possession_applications as mpa')
            ->join('ownermaster as o', 'mpa.owner_id', '=', 'o.OwnerId')
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('villagemaster as v')
                    ->whereColumn('v.VillageId', 'o.VillageId')
                    ->whereNotNull('v.plots')
                    ->whereNotNull('v.phase');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->where(function($q) {
                        $q->where(function($sub) {
                            $sub->whereColumn('r.flatid', 'o.FlatId')
                                ->whereNotNull('r.flatid')
                                ->where('r.flatid', '!=', '');
                        })
                        ->orWhere(function($sub) {
                            $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                ->whereNotNull('r.SecondPartyMobile')
                                ->where('r.SecondPartyMobile', '!=', '')
                                ->where(function($sub2) {
                                    $sub2->whereNull('r.flatid')
                                         ->orWhere('r.flatid', '')
                                         ->orWhereNotExists(function($sub3) {
                                             $sub3->select(DB::raw(1))
                                                  ->from('ownermaster as o2')
                                                  ->whereColumn('o2.FlatId', 'r.flatid');
                                         });
                                });
                        });
                    });
            })
            ->select('mpa.*');

        if ($blockMasterId) {
            $ppaQuery->where('mpa.block_id', $blockMasterId);
        }

        // 1. Total Eligible (All registered owners in BDO block)
        $totalEligibleQuery = DB::table('ownermaster as o')
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('villagemaster as v')
                    ->whereColumn('v.VillageId', 'o.VillageId')
                    ->whereNotNull('v.plots')
                    ->whereNotNull('v.phase');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->where(function($q) {
                        $q->where(function($sub) {
                            $sub->whereColumn('r.flatid', 'o.FlatId')
                                ->whereNotNull('r.flatid')
                                ->where('r.flatid', '!=', '');
                        })
                        ->orWhere(function($sub) {
                            $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                ->whereNotNull('r.SecondPartyMobile')
                                ->where('r.SecondPartyMobile', '!=', '')
                                ->where(function($sub2) {
                                    $sub2->whereNull('r.flatid')
                                         ->orWhere('r.flatid', '')
                                         ->orWhereNotExists(function($sub3) {
                                             $sub3->select(DB::raw(1))
                                                  ->from('ownermaster as o2')
                                                  ->whereColumn('o2.FlatId', 'r.flatid');
                                         });
                                });
                        });
                    });
            });
        if ($blockMasterId) {
            $totalEligibleQuery->where('o.BlockId', $blockMasterId);
        }
        $totalEligibleCount = $totalEligibleQuery->count();

        // 2. Not Scheduled (All registered owners in BDO block who do not have scheduled physical possession)
        $notScheduledQuery = DB::table('ownermaster as o')
            ->leftJoin('mmgay_possession_applications as ppa', 'o.OwnerId', '=', 'ppa.owner_id')
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('villagemaster as v')
                    ->whereColumn('v.VillageId', 'o.VillageId')
                    ->whereNotNull('v.plots')
                    ->whereNotNull('v.phase');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->where(function($q) {
                        $q->where(function($sub) {
                            $sub->whereColumn('r.flatid', 'o.FlatId')
                                ->whereNotNull('r.flatid')
                                ->where('r.flatid', '!=', '');
                        })
                        ->orWhere(function($sub) {
                            $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                ->whereNotNull('r.SecondPartyMobile')
                                ->where('r.SecondPartyMobile', '!=', '')
                                ->where(function($sub2) {
                                    $sub2->whereNull('r.flatid')
                                         ->orWhere('r.flatid', '')
                                         ->orWhereNotExists(function($sub3) {
                                             $sub3->select(DB::raw(1))
                                                  ->from('ownermaster as o2')
                                                  ->whereColumn('o2.FlatId', 'r.flatid');
                                         });
                                });
                        });
                    });
            });
        if ($blockMasterId) {
            $notScheduledQuery->where('o.BlockId', $blockMasterId);
        }
        $notScheduledQuery->where(function($q) {
            $q->whereNull('ppa.id')
              ->orWhere('ppa.physical_possession_status', 'Eligible for Physical Possession');
        });
        $notScheduledCount = $notScheduledQuery->count();

        $stats = [
            'total_eligible' => $totalEligibleCount,
            'not_scheduled' => $notScheduledCount,
            'awaiting_citizen' => (clone $ppaQuery)->where('mpa.physical_possession_status', 'Visit Scheduled')->count(),
            'awaiting_coordinates' => (clone $ppaQuery)->where('mpa.physical_possession_status', 'Slot Selected')->count(),
            'awaiting_bdo_doc' => (clone $ppaQuery)->where('mpa.physical_possession_status', 'Site Verified')->count(),
            'verified' => (clone $ppaQuery)->where('mpa.physical_possession_status', 'Verified')->count(),
        ];

        $recentApplications = (clone $ppaQuery)
            ->select('mpa.*', 'o.Phase as owner_phase')
            ->latest('mpa.created_at')
            ->take(6)
            ->get();

        // Phase Wise Drill Down Analytics data
        $phases = DB::table('ownermaster')
            ->whereNotNull('Phase')
            ->distinct()
            ->orderBy('Phase', 'asc')
            ->pluck('Phase');

        $selectedPhase = $request->input('phase', $phases->first() ?: 1);

        $villages = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->where('o.Phase', $selectedPhase)
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereNotNull('v.plots')
            ->whereNotNull('v.phase')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->where(function($q) {
                        $q->where(function($sub) {
                            $sub->whereColumn('r.flatid', 'o.FlatId')
                                ->whereNotNull('r.flatid')
                                ->where('r.flatid', '!=', '');
                        })
                        ->orWhere(function($sub) {
                            $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                ->whereNotNull('r.SecondPartyMobile')
                                ->where('r.SecondPartyMobile', '!=', '')
                                ->where(function($sub2) {
                                    $sub2->whereNull('r.flatid')
                                         ->orWhere('r.flatid', '')
                                         ->orWhereNotExists(function($sub3) {
                                             $sub3->select(DB::raw(1))
                                                  ->from('ownermaster as o2')
                                                  ->whereColumn('o2.FlatId', 'r.flatid');
                                         });
                                });
                        });
                    });
            })
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            })
            ->when($blockMasterId, function ($q) use ($blockMasterId) {
                $q->where('o.BlockId', $blockMasterId);
            })
            ->select('v.VillageId', 'v.VillageName', 'v.map_pdf', DB::raw('count(distinct o.OwnerId) as total_beneficiaries'))
            ->groupBy('v.VillageId', 'v.VillageName', 'v.map_pdf')
            ->orderBy('v.VillageName', 'asc')
            ->get();

        $selectedVillageId = $request->input('village_id');
        if (!$selectedVillageId && $villages->isNotEmpty()) {
            $selectedVillageId = $villages->first()->VillageId;
        }
        $selectedVillageName = '';
        $selectedVillagePdf = '';
        $beneficiaries = [];
        $search = $request->input('search');

        if ($selectedVillageId) {
            $villageRecord = DB::table('villagemaster')->where('VillageId', $selectedVillageId)->first();
            $selectedVillageName = $villageRecord ? $villageRecord->VillageName : '';
            $selectedVillagePdf = $villageRecord ? $villageRecord->map_pdf : '';

            $query = DB::table('ownermaster as o')
                ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
                ->leftJoin('districtmaster as d', 'f.DistrictId', '=', 'd.DistrictId')
                ->leftJoin('blockmaster as b', 'f.BlockId', '=', 'b.BlockId')
                ->leftJoin('villagemaster as v', 'f.VillageId', '=', 'v.VillageId')
                ->where('o.IsApproved', 1)
                ->where('o.IsPaid', 1)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('villagemaster as v')
                        ->whereColumn('v.VillageId', 'o.VillageId')
                        ->whereNotNull('v.plots')
                        ->whereNotNull('v.phase');
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('flatmaster as f')
                        ->whereColumn('f.FlatId', 'o.FlatId');
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('registary as r')
                        ->where(function($q) {
                            $q->where(function($sub) {
                                $sub->whereColumn('r.flatid', 'o.FlatId')
                                    ->whereNotNull('r.flatid')
                                    ->where('r.flatid', '!=', '');
                            })
                            ->orWhere(function($sub) {
                                $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                    ->whereNotNull('r.SecondPartyMobile')
                                    ->where('r.SecondPartyMobile', '!=', '')
                                    ->where(function($sub2) {
                                        $sub2->whereNull('r.flatid')
                                             ->orWhere('r.flatid', '')
                                             ->orWhereNotExists(function($sub3) {
                                                 $sub3->select(DB::raw(1))
                                                      ->from('ownermaster as o2')
                                                      ->whereColumn('o2.FlatId', 'r.flatid');
                                             });
                                    });
                            });
                        });
                })
                ->whereIn('o.OwnerId', function ($q) {
                    $q->select(DB::raw('MIN(OwnerId)'))
                        ->from('ownermaster')
                        ->groupBy('FlatId');
                })
                ->leftJoin('mmgay_possession_applications as ppa', 'o.OwnerId', '=', 'ppa.owner_id')
                ->where('o.Phase', $selectedPhase)
                ->where('o.VillageId', $selectedVillageId)
                ->when($blockMasterId, function ($q) use ($blockMasterId) {
                    $q->where('o.BlockId', $blockMasterId);
                });

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('o.OwnerName', 'like', "%{$search}%")
                      ->orWhere('o.MobileNo', 'like', "%{$search}%")
                      ->orWhere('o.RegistrationNo', 'like', "%{$search}%");
                });
            }

            $beneficiaries = $query->select(
                'o.OwnerId',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.secure_id',
                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'f.FlatNo',
                DB::raw("COALESCE(ppa.physical_possession_status, 'Eligible for Physical Possession') as possession_status"),
                'ppa.application_number',
                'o.PPPId',
                'o.MemberId',
                'o.OwnerAddress'
            )
            ->paginate(50)
            ->withQueryString();

            $beneficiaries->through(function ($ben) {
                return $this->formatLocationDetails($ben);
            });
        }

        $activeMenu = 'dashboard';
        return view('mmgay.bdo.dashboard', compact(
            'bdo', 
            'stats', 
            'recentApplications', 
            'activeMenu',
            'phases',
            'selectedPhase',
            'villages',
            'selectedVillageId',
            'selectedVillageName',
            'selectedVillagePdf',
            'beneficiaries',
            'search'
        ));
    }

    /**
     * Show BDO Eligibility List of Applicants.
     */
    public function eligibilityList(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        $bypassApi = env('MMGAY_POSSESSION_BYPASS_API', app()->environment('local'));

        $query = DB::table('ownermaster as o')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('villagemaster as v')
                    ->whereColumn('v.VillageId', 'o.VillageId')
                    ->whereNotNull('v.plots')
                    ->whereNotNull('v.phase');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->where(function($q) {
                        $q->where(function($sub) {
                            $sub->whereColumn('r.flatid', 'o.FlatId')
                                ->whereNotNull('r.flatid')
                                ->where('r.flatid', '!=', '');
                        })
                        ->orWhere(function($sub) {
                            $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                ->whereNotNull('r.SecondPartyMobile')
                                ->where('r.SecondPartyMobile', '!=', '')
                                ->where(function($sub2) {
                                    $sub2->whereNull('r.flatid')
                                         ->orWhere('r.flatid', '')
                                         ->orWhereNotExists(function($sub3) {
                                             $sub3->select(DB::raw(1))
                                                  ->from('ownermaster as o2')
                                                  ->whereColumn('o2.FlatId', 'r.flatid');
                                         });
                                });
                        });
                    });
            })
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            })
            ->leftJoin('mmgay_possession_applications as ppa', function ($join) {
                $join->on('o.OwnerId', '=', 'ppa.owner_id');
            });

        if (!$request->has('all')) {
            $query->where(function($q) {
                $q->whereNull('ppa.id')
                  ->orWhereIn('ppa.physical_possession_status', ['Eligible for Physical Possession', 'Visit Scheduled']);
            });
        }

        if ($blockMasterId) {
            $query->where('o.BlockId', $blockMasterId);
        }

        // Search filter
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('o.OwnerName', 'like', "%{$search}%")
                  ->orWhere('o.MobileNo', 'like', "%{$search}%")
                  ->orWhere('o.RegistrationNo', 'like', "%{$search}%");
            });
        }

        $query->orderBy('d.DistrictName', 'asc')
            ->orderBy('b.BlockName', 'asc')
            ->orderBy('o.OwnerName', 'asc');

        $applications = $query->select(
            'o.OwnerId as id',
            'o.secure_id',
            'o.OwnerName as applicant_name',
            'o.FatherHusbandName as father_name',
            'o.MobileNo as mobile',
            'o.RegistrationNo as registration_no',
            'o.Phase as owner_phase',
            'd.DistrictName as district_name',
            'b.BlockName as block_name',
            'ppa.application_number',
            'ppa.created_at as app_created_at',
            DB::raw("COALESCE(ppa.physical_possession_status, 'Eligible for Physical Possession') as physical_possession_status")
        )->paginate(50)->withQueryString();

        $activeMenu = 'schedule_pending';
        return view('mmgay.bdo.eligibility', compact('applications', 'search', 'bdo', 'activeMenu'));
    }

    /**
     * Show BDO Schedule Form - Dynamically creates the application row on-demand if missing.
     */
    public function scheduleForm($secureId)
    {
        $bdo = Auth::user();

        // 1. Fetch the owner details by secure_id
        $owner = DB::table('ownermaster as o')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->leftJoin('blockmaster as fb', 'f.BlockId', '=', 'fb.BlockId')
            ->leftJoin('villagemaster as fv', 'f.VillageId', '=', 'fv.VillageId')
            ->leftJoin('districtmaster as fd', 'f.DistrictId', '=', 'fd.DistrictId')
            ->where('o.secure_id', $secureId)
            ->select(
                'o.*',
                'f.FlatNo',
                'fb.BlockName as BlockName',
                'fv.VillageName as VillageName',
                'fd.DistrictName as DistrictName'
            )
            ->first();

        if (!$owner) {
            abort(404, 'Beneficiary record not found.');
        }

        if ($bdo->block_id && $owner->BlockId !== $bdo->block_id) {
            abort(403, 'Unauthorized access to beneficiary in another block.');
        }

        if ($check = $this->restrictBySiteDevelopment($owner)) {
            return $check;
        }

        if (!\App\Models\MmgayPossessionApplication::isWhitelistedForPossession($owner->RegistrationNo)) {
            abort(400, 'Physical Possession is only available for beneficiaries verified under HFA land registration.');
        }

        // 2. Find or dynamically create the physical possession application row
        $application = MmgayPossessionApplication::where('owner_id', $owner->OwnerId)
            ->first();

        if (!$application) {
            $application = \Illuminate\Support\Facades\Cache::lock('pp_create_mmgay_app_' . $owner->OwnerId, 15)->block(5, function () use ($owner) {
                // Double check inside lock to prevent race condition duplicates
                $app = MmgayPossessionApplication::where('owner_id', $owner->OwnerId)->first();
                if ($app) {
                    return $app;
                }

                // Find or create villager user in users table
                $user = User::where('mobile', $owner->MobileNo)
                    ->where('scheme', 'MMGAY')
                    ->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $owner->OwnerName,
                        'mobile' => $owner->MobileNo,
                        'role' => 'villager',
                        'scheme' => 'MMGAY',
                        'Is_Active' => '1',
                        'Is_Deleted' => '0',
                        'district_id' => $owner->DistrictId,
                        'district_name' => $owner->DistrictName,
                        'block_id' => $owner->BlockId,
                        'block_name' => $owner->BlockName,
                    ]);

                    // Seed role type for this new user
                    $villagerRole = DB::table('roles')->where('slug', 'villager')->first();
                    if ($villagerRole) {
                        DB::table('role_types')->insert([
                            'user_id' => $user->id,
                            'role_id' => $villagerRole->id,
                            'Is_Active' => '1',
                            'Is_Deleted' => '0',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                return MmgayPossessionApplication::create([
                    'user_id' => $user->id,
                    'owner_id' => $owner->OwnerId,
                    'ppp_id' => $owner->PPPId ?? null,
                    'member_id' => $owner->MemberId ?? null,
                    'flat_id' => $owner->FlatId ?? null,
                    'scheme' => 'MMGAY',
                    'application_number' => 'PP-MMGAY-' . now()->format('Y') . '-' . ($owner->RegistrationNo ?? rand(1000, 9999)),
                    'secure_id' => $owner->secure_id, // Match owner's unique random 32-character secure_id
                    'slip_id' => 'SLIP-MMGAY-' . uniqid(),
                    'district_id' => $owner->DistrictId,
                    'district_name' => $owner->DistrictName,
                    'block_id' => $owner->BlockId,
                    'block_name' => $owner->BlockName,
                    'mobile' => $owner->MobileNo,
                    'applicant_name' => $owner->OwnerName,
                    'father_name' => $owner->FatherHusbandName ?? '',
                    'address' => $owner->OwnerAddress ?? '',
                    'flat_cost' => 0,
                    'received_amount' => 0,
                    'balance_amount' => 0,
                    'physical_possession_status' => 'Eligible for Physical Possession',
                    'status' => 'pending',
                ]);
            });
        }

        if (in_array($application->physical_possession_status, ['Slot Selected', 'Verified', 'Rejected'])) {
            return redirect()->route('mmgay.bdo.possession-applications')->with('error', 'Cannot schedule or update schedule after slot is confirmed by citizen or verified.');
        }

        $activeMenu = 'schedule_pending';
        return view('mmgay.bdo.schedule', compact('application', 'owner', 'bdo', 'secureId', 'activeMenu'));
    }

    /**
     * Save BDO Schedule visits.
     */
    public function scheduleSave(Request $request, $secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)
            ->firstOrFail();

        if ($bdo->block_id && $application->block_id !== $bdo->block_id) {
            abort(403, 'Unauthorized.');
        }

        $owner = DB::table('ownermaster as o')
            ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->where('o.OwnerId', $application->owner_id)
            ->select('o.*', 'v.VillageName')
            ->first();

        if ($check = $this->restrictBySiteDevelopment($owner)) {
            return $check;
        }

        if (in_array($application->physical_possession_status, ['Slot Selected', 'Verified', 'Rejected'])) {
            return redirect()->route('mmgay.bdo.possession-applications')->with('error', 'Cannot schedule or update schedule after slot is confirmed.');
        }

        $request->validate([
            'slot_date_1' => 'required|date|after:today',
            'slot_time_1' => 'required|string',
            'slot_date_2' => 'required|date|after:today',
            'slot_time_2' => 'required|string',
            'slot_date_3' => 'required|date|after:today',
            'slot_time_3' => 'required|string',
            'visit_instructions' => 'nullable|string|max:1000',
        ]);

        $dateTime1 = Carbon::parse($request->slot_date_1 . ' ' . $request->slot_time_1);
        $dateTime2 = Carbon::parse($request->slot_date_2 . ' ' . $request->slot_time_2);
        $dateTime3 = Carbon::parse($request->slot_date_3 . ' ' . $request->slot_time_3);

        $now = now();
        $todayStr = $now->toDateString();
        $currentHour = $now->hour;

        foreach ([$dateTime1, $dateTime2, $dateTime3] as $idx => $dt) {
            $slotNum = $idx + 1;
            
            if ($dt->isPast()) {
                return back()->withErrors(["slot_time_{$slotNum}" => "Slot {$slotNum} time cannot be in the past."])->withInput();
            }
            
            if ($dt->toDateString() === $todayStr && $dt->hour <= $currentHour) {
                return back()->withErrors(["slot_time_{$slotNum}" => "Slot {$slotNum} time cannot be in the past."])->withInput();
            }
            
            if ($dt->hour < 9 || $dt->hour > 16) {
                return back()->withErrors(["slot_time_{$slotNum}" => "Slot {$slotNum} must be between 09:00 AM and 05:00 PM."])->withInput();
            }
        }

        if (
            ($dateTime1->toDateString() === $dateTime2->toDateString() && $dateTime1->format('H:i') === $dateTime2->format('H:i')) ||
            ($dateTime1->toDateString() === $dateTime3->toDateString() && $dateTime1->format('H:i') === $dateTime3->format('H:i')) ||
            ($dateTime2->toDateString() === $dateTime3->toDateString() && $dateTime2->format('H:i') === $dateTime3->format('H:i'))
        ) {
            return back()->withErrors(['slot_date_1' => 'You cannot select the same date and time for more than one slot.'])->withInput();
        }

        // Capacity slot check (max 10 people per 1-hour window per block)
        $blockId = $application->block_id;
        foreach ([$dateTime1, $dateTime2, $dateTime3] as $idx => $dt) {
            $slotStart = $dt->copy()->startOfHour();
            $slotEnd = $slotStart->copy()->addHour();

            $existingCount = DB::table('mmgay_possession_applications')
                ->where('block_id', $blockId)
                ->where('id', '!=', $application->id)
                ->where(function($q) use ($slotStart, $slotEnd) {
                    $q->where(function($sub) use ($slotStart, $slotEnd) {
                        $sub->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
                            ->where(function($three) use ($slotStart, $slotEnd) {
                                $three->whereBetween('visit_slot_1', [$slotStart, $slotEnd])
                                      ->orWhereBetween('visit_slot_2', [$slotStart, $slotEnd])
                                      ->orWhereBetween('visit_slot_3', [$slotStart, $slotEnd]);
                            });
                    })
                    ->orWhere(function($sub) use ($slotStart, $slotEnd) {
                        $sub->whereIn('physical_possession_status', ['Slot Selected', 'Verified'])
                            ->whereBetween('citizen_visit_date', [$slotStart, $slotEnd]);
                    });
                })
                ->count();

            if ($existingCount >= 10) {
                $slotNum = $idx + 1;
                return back()->withErrors([
                    "slot_time_{$slotNum}" => "Slot {$slotNum} (" . $dt->format('d M Y, h:i A') . ") has {$existingCount} visits scheduled. Maximum 10 visits allowed per hour."
                ])->withInput();
            }
        }

        $oldStatus = $application->physical_possession_status;

        $application->update([
            'possession_date' => $request->slot_date_1,
            'meeting_slot' => $dateTime1->format('Y-m-d h:i A') . ' | ' . $dateTime2->format('Y-m-d h:i A') . ' | ' . $dateTime3->format('Y-m-d h:i A'),
            'citizen_visit_date' => $dateTime1,
            'visit_slot_1' => $dateTime1,
            'visit_slot_2' => $dateTime2,
            'visit_slot_3' => $dateTime3,
            'visit_instructions' => $request->visit_instructions,
            'status' => 'pending',
            'physical_possession_status' => 'Visit Scheduled',
        ]);

        MmgayPossessionStatusLog::create([
            'application_id' => $application->id,
            'asset_id' => $application->asset_id ?? 0,
            'old_status' => $oldStatus,
            'new_status' => 'Visit Scheduled',
            'remarks' => 'Visit scheduled by BDO. Offered slots: Slot 1: ' . $dateTime1->format('d M Y - h:i A') . ', Slot 2: ' . $dateTime2->format('d M Y - h:i A') . ', Slot 3: ' . $dateTime3->format('d M Y - h:i A'),
            'changed_by_type' => 'officer',
            'changed_by_id' => $bdo->id,
        ]);

        \App\Models\MmgayPossessionBdoStatus::create([
            'application_id' => $application->id,
            'application_number' => $application->application_number,
            'bdo_user_id' => $bdo->id,
            'bdo_name' => $bdo->name,
            'bdo_email' => $bdo->email,
            'bdo_mobile' => $bdo->mobile ?? null,
            'status' => 'Visit Scheduled',
            'remarks' => $request->visit_instructions ?? 'Visit scheduled by BDO.',
        ]);

        $smsService = app(\App\Services\LoginOtpSmsService::class);
        $smsConfig = config('otp-login.mmgay_possession_scheduled_sms');
        if ($smsConfig && !empty($application->mobile)) {
            $message = $smsConfig['message'];
            // Replace the first {#alp#} with the applicant's name
            $pos = strpos($message, '{#alp#}');
            if ($pos !== false) {
                $message = substr_replace($message, $application->applicant_name, $pos, strlen('{#alp#}'));
            }
            // Replace the second {#alp#} with the application number
            $pos = strpos($message, '{#alp#}');
            if ($pos !== false) {
                $message = substr_replace($message, $application->application_number, $pos, strlen('{#alp#}'));
            }

            $smsService->sendCustomMessage(
                $application->mobile,
                $message,
                $smsConfig['template_id'],
                'MMGAY Possession Schedule '.$application->application_number
            );
        }

        Log::info("MMGAY SMS Notification: Physical Possession slots scheduled for {$application->applicant_name} (Mobile: {$application->mobile}).");

        return redirect()->route('mmgay.bdo.eligibility-list')->with('success', 'Physical Possession visit has been successfully scheduled.');
    }

    /**
     * Show BDO Possession Applications.
     */
    public function applications(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        $query = MmgayPossessionApplication::query()
            ->join('ownermaster as o', 'mmgay_possession_applications.owner_id', '=', 'o.OwnerId')
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('villagemaster as v')
                    ->whereColumn('v.VillageId', 'o.VillageId')
                    ->whereNotNull('v.plots')
                    ->whereNotNull('v.phase');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->where(function($q) {
                        $q->where(function($sub) {
                            $sub->whereColumn('r.flatid', 'o.FlatId')
                                ->whereNotNull('r.flatid')
                                ->where('r.flatid', '!=', '');
                        })
                        ->orWhere(function($sub) {
                            $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                ->whereNotNull('r.SecondPartyMobile')
                                ->where('r.SecondPartyMobile', '!=', '')
                                ->where(function($sub2) {
                                    $sub2->whereNull('r.flatid')
                                         ->orWhere('r.flatid', '')
                                         ->orWhereNotExists(function($sub3) {
                                             $sub3->select(DB::raw(1))
                                                  ->from('ownermaster as o2')
                                                  ->whereColumn('o2.FlatId', 'r.flatid');
                                         });
                                });
                        });
                    });
            })
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            })
            ->select('mmgay_possession_applications.*', 'o.Phase as owner_phase')
            ->where('mmgay_possession_applications.physical_possession_status', '!=', 'Eligible for Physical Possession');

        if ($blockMasterId) {
            $query->where('mmgay_possession_applications.block_id', $blockMasterId);
        }

        $status = $request->input('status');
        if ($status) {
            $query->where('mmgay_possession_applications.physical_possession_status', $status);
        }

        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mmgay_possession_applications.applicant_name', 'like', "%{$search}%")
                  ->orWhere('mmgay_possession_applications.mobile', 'like', "%{$search}%")
                  ->orWhere('mmgay_possession_applications.application_number', 'like', "%{$search}%");
            });
        }
        $applications = $query->latest('mmgay_possession_applications.created_at')->paginate(50)->withQueryString();

        $activeMenu = '';
        if ($status === 'Visit Scheduled') {
            $activeMenu = 'awaiting_citizen';
        } elseif ($status === 'Slot Selected') {
            $activeMenu = 'field_visit_pending';
        } elseif ($status === 'Site Verified') {
            $activeMenu = 'epossession_pending';
        } elseif ($status === 'Verified') {
            $activeMenu = 'verified';
        }

        return view('mmgay.bdo.applications', compact('applications', 'search', 'status', 'bdo', 'activeMenu'));
    }

    /**
     * Show BDO Verify Form.
     */
    public function verifyForm($secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)
            ->firstOrFail();

        if ($bdo->block_id && $application->block_id !== $bdo->block_id) {
            abort(403, 'Unauthorized.');
        }

        // Get owner details from ownermaster
        $owner = DB::table('ownermaster as o')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->leftJoin('blockmaster as fb', 'f.BlockId', '=', 'fb.BlockId')
            ->leftJoin('villagemaster as fv', 'f.VillageId', '=', 'fv.VillageId')
            ->leftJoin('districtmaster as fd', 'f.DistrictId', '=', 'fd.DistrictId')
            ->where('o.OwnerId', $application->owner_id)
            ->select(
                'o.*',
                'f.FlatNo',
                'fb.BlockName as BlockName',
                'fv.VillageName as VillageName',
                'fd.DistrictName as DistrictName'
            )
            ->first();

        if ($check = $this->restrictBySiteDevelopment($owner)) {
            return $check;
        }

        $logs = MmgayPossessionStatusLog::where('application_id', $application->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $activeMenu = '';
        if ($application->physical_possession_status === 'Visit Scheduled') {
            $activeMenu = 'awaiting_citizen';
        } elseif ($application->physical_possession_status === 'Slot Selected') {
            $activeMenu = 'field_visit_pending';
        } elseif ($application->physical_possession_status === 'Site Verified') {
            $activeMenu = 'epossession_pending';
        } elseif ($application->physical_possession_status === 'Verified') {
            $activeMenu = 'verified';
        }

        return view('mmgay.bdo.verify', compact('application', 'owner', 'bdo', 'logs', 'activeMenu'));
    }

    public function verifySave(Request $request, $secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)
            ->firstOrFail();

        if ($bdo->block_id && $application->block_id !== $bdo->block_id) {
            abort(403, 'Unauthorized.');
        }

        $owner = DB::table('ownermaster as o')
            ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->where('o.OwnerId', $application->owner_id)
            ->select('o.*', 'v.VillageName')
            ->first();

        if ($check = $this->restrictBySiteDevelopment($owner)) {
            return $check;
        }

        $currentStatus = $application->physical_possession_status;

        if ($request->input('action') === 'reschedule') {
            $oldStatus = $application->physical_possession_status;

            // Capture the previous slot time before resetting
            $prevSlotInfo = "N/A";
            $visitDateStr = "N/A";
            if ($application->possession_date) {
                $dateFormatted = date('d M Y', strtotime($application->possession_date));
                $prevSlotInfo = $dateFormatted . " (" . ($application->meeting_slot ?? 'N/A') . ")";
                $visitDateStr = $dateFormatted;
            }

            // Send absent SMS
            $smsService = app(\App\Services\LoginOtpSmsService::class);
            $smsConfig = config('otp-login.mmgay_possession_absent_sms');
            if ($smsConfig && !empty($application->mobile)) {
                $message = $smsConfig['message'];
                // Replace the first {#alp#} with the applicant's name
                $pos = strpos($message, '{#alp#}');
                if ($pos !== false) {
                    $message = substr_replace($message, $application->applicant_name, $pos, strlen('{#alp#}'));
                }
                // Replace the second {#alp#} with the visit date
                $pos = strpos($message, '{#alp#}');
                if ($pos !== false) {
                    $message = substr_replace($message, $visitDateStr, $pos, strlen('{#alp#}'));
                }

                $smsService->sendCustomMessage(
                    $application->mobile,
                    $message,
                    $smsConfig['template_id'],
                    'MMGAY Possession Absent Reset '.$application->application_number
                );
            }

            $application->physical_possession_status = 'Eligible for Physical Possession';
            $application->possession_date = null;
            $application->meeting_slot = null;
            $application->citizen_visit_date = null;
            $application->visit_slot_1 = null;
            $application->visit_slot_2 = null;
            $application->visit_slot_3 = null;
            $application->visit_instructions = null;
            $application->save();

            MmgayPossessionStatusLog::create([
                'application_id' => $application->id,
                'asset_id' => $application->asset_id ?? 0,
                'old_status' => $oldStatus,
                'new_status' => 'Eligible for Physical Possession',
                'remarks' => "Applicant was absent / did not attend the scheduled visit slot: {$prevSlotInfo}. Visit slot has been reset for rescheduling by BDPO.",
                'changed_by_type' => 'officer',
                'changed_by_id' => $bdo->id,
            ]);

            return redirect()->route('mmgay.bdo.dashboard')->with('success', 'Visit slot reset successfully. You can now schedule a new visit from the dashboard.');
        }

        if ($currentStatus === 'Slot Selected') {
            // Stage 1: Coordinates and Plot image capture
            $request->validate([
                'remarks' => 'required|string|max:1000',
                'latitude' => 'required|string',
                'longitude' => 'required|string',
                'plot_image' => 'required|image|mimes:jpeg,jpg,png|max:500',
            ], [
                'remarks.required' => 'Please provide verification remarks/comments.',
                'latitude.required' => 'GPS Latitude is required. Please capture location.',
                'longitude.required' => 'GPS Longitude is required. Please capture location.',
                'plot_image.required' => 'Plot site photo with applicant is required.',
                'plot_image.max' => 'The plot site photo must not exceed 500 KB.',
            ]);

            if ($request->hasFile('plot_image')) {
                $plotImage = $request->file('plot_image');
                $plotImageName = 'plot_' . $application->id . '_' . time() . '.' . $plotImage->getClientOriginalExtension();
                $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
                $memberId = $owner ? trim($owner->MemberId) : '';
                $memberFolder = $memberId ? preg_replace('/[^A-Za-z0-9_-]/', '', $memberId) : 'member_' . $application->id;
                $plotImagePath = $plotImage->storeAs($memberFolder . '/plot_images', $plotImageName, 'public');
                $application->plot_image = $plotImagePath;
            }

            $application->latitude = $request->latitude;
            $application->longitude = $request->longitude;
            $application->image_capture_datetime = now();
            $application->remarks = $request->input('remarks');
            $application->physical_possession_status = 'Site Verified';
            $application->save();

            MmgayPossessionStatusLog::create([
                'application_id' => $application->id,
                'asset_id' => $application->asset_id ?? 0,
                'old_status' => $currentStatus,
                'new_status' => 'Site Verified',
                'remarks' => 'Site verification details (GPS coordinates and site photo) submitted by BDO: ' . $request->remarks,
                'changed_by_type' => 'officer',
                'changed_by_id' => $bdo->id,
            ]);

            \App\Models\MmgayPossessionBdoStatus::create([
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'bdo_user_id' => $bdo->id,
                'bdo_name' => $bdo->name,
                'bdo_email' => $bdo->email,
                'bdo_mobile' => $bdo->mobile ?? null,
                'status' => 'Site Verified',
                'remarks' => $request->remarks,
            ]);

            return redirect()->route('mmgay.bdo.verify-form', $application->secure_id)->with('success', 'Site coordinates captured successfully. Now proceed to E-Possession step.');

        } else {
            // Stage 2: Signed report upload & BDO Official Document (PDF only)
            $request->validate([
                'site_engineer_file' => 'required|file|mimes:pdf|max:500',
                'possession_certificate' => 'required|file|mimes:pdf|max:500',
            ], [
                'site_engineer_file.required' => 'BDO signed report (PDF) is required.',
                'site_engineer_file.mimes' => 'The signed report must be a PDF file.',
                'site_engineer_file.max' => 'The signed report must not exceed 500 KB.',
                'possession_certificate.required' => 'Final Possession Letter (PDF) is required.',
                'possession_certificate.mimes' => 'The Final Possession Letter must be a PDF file.',
                'possession_certificate.max' => 'The Final Possession Letter must not exceed 500 KB.',
            ]);

            if ($request->hasFile('site_engineer_file')) {
                $siteFile = $request->file('site_engineer_file');
                $siteFileName = 'bdo_verify_' . $application->id . '_' . time() . '.' . $siteFile->getClientOriginalExtension();
                $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
                $memberId = $owner ? trim($owner->MemberId) : '';
                $memberFolder = $memberId ? preg_replace('/[^A-Za-z0-9_-]/', '', $memberId) : 'member_' . $application->id;
                $siteFilePath = $siteFile->storeAs($memberFolder . '/bdo_verify', $siteFileName, 'public');
                $application->site_engineer_file = $siteFilePath;
            }

            if ($request->hasFile('possession_certificate')) {
                $certFile = $request->file('possession_certificate');
                $certFileName = 'bdo_cert_' . $application->id . '_' . time() . '.' . $certFile->getClientOriginalExtension();
                $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
                $memberId = $owner ? trim($owner->MemberId) : '';
                $memberFolder = $memberId ? preg_replace('/[^A-Za-z0-9_-]/', '', $memberId) : 'member_' . $application->id;
                $certFilePath = $certFile->storeAs($memberFolder . '/bdo_certs', $certFileName, 'public');
                $application->possession_certificate = $certFilePath;
            }

            $oldStatus = $application->physical_possession_status;

            $application->update([
                'physical_possession_status' => 'Verified',
                'status' => 'approved',
                'verified_by' => $bdo->id,
                'verified_at' => now(),
            ]);

            MmgayPossessionStatusLog::create([
                'application_id' => $application->id,
                'asset_id' => $application->asset_id ?? 0,
                'old_status' => $oldStatus,
                'new_status' => 'Verified',
                'remarks' => 'Physical Possession verified and completed by BDO Officer.',
                'changed_by_type' => 'officer',
                'changed_by_id' => $bdo->id,
            ]);

            \App\Models\MmgayPossessionBdoStatus::create([
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'bdo_user_id' => $bdo->id,
                'bdo_name' => $bdo->name,
                'bdo_email' => $bdo->email,
                'bdo_mobile' => $bdo->mobile ?? null,
                'status' => 'Verified',
                'remarks' => 'Physical Possession verified and completed by BDO Officer.',
            ]);

            return redirect()->route('mmgay.bdo.dashboard')->with('success', 'Application verified and approved successfully.');
        }
    }

    /**
     * Download Prefilled BDO/Citizen Possession Report PDF.
     */
    public function downloadCertificate(Request $request, $secureId)
    {
        $user = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)
            ->firstOrFail();

        if ($user) {
            if ($user->role === 'villager' || $user->hasRole('villager')) {
                if ($application->user_id !== $user->id) {
                    abort(403, 'Unauthorized.');
                }
            } else {
                if ($user->block_id && $application->block_id !== $user->block_id) {
                    abort(403, 'Unauthorized.');
                }
            }
        }

        // Get owner details from ownermaster
        $owner = DB::table('ownermaster as o')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->leftJoin('blockmaster as b', 'f.BlockId', '=', 'b.BlockId')
            ->leftJoin('villagemaster as v', 'f.VillageId', '=', 'v.VillageId')
            ->leftJoin('districtmaster as d', 'f.DistrictId', '=', 'd.DistrictId')
            ->where('o.OwnerId', $application->owner_id)
            ->select('o.*', 'b.BlockName', 'v.VillageName', 'd.DistrictName', 'f.FlatNo')
            ->first();

        $owner = $this->formatLocationDetails($owner);

        if (!$owner) {
            abort(404, 'Owner details not found.');
        }

        $bdoName = '—';
        if ($application->verified_by) {
            $verifyingUser = User::find($application->verified_by);
            if ($verifyingUser) {
                $bdoName = $verifyingUser->name;
            }
        }

        $pdfData = [
            'application' => $application,
            'owner' => $owner,
            'bdoName' => $bdoName,
            'verified_at' => $application->verified_at ? $application->verified_at->format('d M Y, h:i A') : '—',
        ];

        $pdf = Pdf::loadView('mmgay.bdo.pdf.prefilled-form', $pdfData)
            ->setPaper('a4');

        $safeAppNo = str_replace(['/', '\\'], '-', $application->application_number);

        if ($request->has('inline')) {
            return $pdf->stream('Possession-Report-MMGAY-'.$safeAppNo.'.pdf');
        }

        return $pdf->download('Possession-Report-MMGAY-'.$safeAppNo.'.pdf');
    }

    /**
     * AJAX slot capacity check.
     */
    public function getSlotCapacityCheck(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'exclude_id' => 'nullable|integer',
        ]);

        $date = $request->input('date');
        $excludeId = $request->input('exclude_id', 0);
        $bdo = Auth::user();
        
        $blockId = $bdo->block_id;
        if (!$blockId) {
            return response()->json(['success' => false, 'message' => 'BDO block not defined.']);
        }

        $counts = [];
        for ($hour = 9; $hour <= 16; $hour++) {
            $slotStart = Carbon::parse($date . ' ' . sprintf('%02d:00:00', $hour));
            $slotEnd = $slotStart->copy()->addHour();
            
            $count = DB::table('mmgay_possession_applications')
                ->where('block_id', $blockId)
                ->where('id', '!=', $excludeId)
                ->where(function($q) use ($slotStart, $slotEnd) {
                    $q->where(function($sub) use ($slotStart, $slotEnd) {
                        $sub->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
                            ->where(function($three) use ($slotStart, $slotEnd) {
                                $three->whereBetween('visit_slot_1', [$slotStart, $slotEnd])
                                      ->orWhereBetween('visit_slot_2', [$slotStart, $slotEnd])
                                      ->orWhereBetween('visit_slot_3', [$slotStart, $slotEnd]);
                            });
                    })
                    ->orWhere(function($sub) use ($slotStart, $slotEnd) {
                        $sub->whereIn('physical_possession_status', ['Slot Selected', 'Verified'])
                            ->whereBetween('citizen_visit_date', [$slotStart, $slotEnd]);
                    });
                })
                ->count();
            
            $counts[$hour] = $count;
        }

        return response()->json([
            'success' => true,
            'counts' => $counts
        ]);
    }

    /**
     * Show Citizen Submission Form to select visit slot.
     */
    public function submitPossessionForm()
    {
        $user = Auth::user();

        $application = MmgayPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
            ->latest()
            ->first();

        if (!$application) {
            return redirect()->route('mmgav.villager.dashboard')->with('error', 'No active scheduled visit found for your application.');
        }

        $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
        if (!$owner || !\App\Models\MmgayPossessionApplication::isWhitelistedForPossession($owner->RegistrationNo)) {
            return redirect()->route('mmgav.villager.dashboard')->with('error', 'Physical Possession is only available for verified HFA land registration entries.');
        }

        $logs = MmgayPossessionStatusLog::where('application_id', $application->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mmgay.citizen.submit-possession', compact('application', 'user', 'logs'));
    }

    /**
     * Save Citizen Slot selection.
     */
    public function submitPossession(Request $request)
    {
        $user = Auth::user();

        $application = MmgayPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
            ->latest()
            ->first();

        if (!$application) {
            return redirect()->route('mmgav.villager.dashboard')->with('error', 'No active scheduled visit found for your application.');
        }

        $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
        if (!$owner || !\App\Models\MmgayPossessionApplication::isWhitelistedForPossession($owner->RegistrationNo)) {
            return redirect()->route('mmgav.villager.dashboard')->with('error', 'Physical Possession is only available for verified HFA land registration entries.');
        }

        $request->validate([
            'selected_slot' => 'required|string',
        ], [
            'selected_slot.required' => 'Please select the scheduled meeting slot.',
        ]);

        $oldStatus = $application->physical_possession_status;
        $selectedSlot = $request->input('selected_slot');
        $dateTime = Carbon::parse($selectedSlot);

        $application->update([
            'meeting_slot' => $selectedSlot,
            'citizen_visit_date' => $dateTime,
            'possession_date' => $dateTime->toDateString(),
            'physical_possession_status' => 'Slot Selected',
        ]);

        MmgayPossessionStatusLog::create([
            'application_id' => $application->id,
            'asset_id' => $application->asset_id ?? 0,
            'old_status' => $oldStatus,
            'new_status' => 'Slot Selected',
            'remarks' => 'Visit slot selected by Citizen: ' . $dateTime->format('d M Y - h:i A'),
            'changed_by_type' => 'user',
            'changed_by_id' => $user->id,
        ]);

        return redirect()->route('mmgav.villager.dashboard')->with('success', 'You have successfully selected the visit slot: ' . $dateTime->format('d M Y - h:i A') . ' and submitted your request.');
    }

    /**
     * Download Citizen/Villager Physical Possession Appointment Slip PDF.
     */
    public function downloadSlip(Request $request)
    {
        $user = Auth::user();

        $application = MmgayPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Slot Selected', 'Site Verified', 'Verified'])
            ->latest()
            ->firstOrFail();

        // Get owner details from ownermaster
        $owner = DB::table('ownermaster as o')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->leftJoin('blockmaster as b', 'f.BlockId', '=', 'b.BlockId')
            ->leftJoin('villagemaster as v', 'f.VillageId', '=', 'v.VillageId')
            ->leftJoin('districtmaster as d', 'f.DistrictId', '=', 'd.DistrictId')
            ->where('o.OwnerId', $application->owner_id)
            ->select('o.*', 'b.BlockName', 'v.VillageName', 'd.DistrictName', 'f.FlatNo')
            ->first();

        $owner = $this->formatLocationDetails($owner);

        if (!$owner) {
            abort(404, 'Owner details not found.');
        }

        $pdfData = [
            'application' => $application,
            'owner' => $owner,
        ];

        $pdf = Pdf::loadView('mmgay.citizen.pdf.appointment-slip', $pdfData)
            ->setPaper('a4');

        $safeAppNo = str_replace(['/', '\\'], '-', $application->application_number);

        return $pdf->download('Possession-Slip-MMGAY-'.$safeAppNo.'.pdf');
    }

    public function phaseReport(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        // Fetch distinct phases
        $phases = DB::table('ownermaster')
            ->whereNotNull('Phase')
            ->distinct()
            ->orderBy('Phase', 'asc')
            ->pluck('Phase');

        $selectedPhase = $request->input('phase', $phases->first() ?: 1);

        // Fetch villages having entries in this phase
        $villages = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->where('o.Phase', $selectedPhase)
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereNotNull('v.plots')
            ->whereNotNull('v.phase')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->where(function($q) {
                        $q->where(function($sub) {
                            $sub->whereColumn('r.flatid', 'o.FlatId')
                                ->whereNotNull('r.flatid')
                                ->where('r.flatid', '!=', '');
                        })
                        ->orWhere(function($sub) {
                            $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                ->whereNotNull('r.SecondPartyMobile')
                                ->where('r.SecondPartyMobile', '!=', '')
                                ->where(function($sub2) {
                                    $sub2->whereNull('r.flatid')
                                         ->orWhere('r.flatid', '')
                                         ->orWhereNotExists(function($sub3) {
                                             $sub3->select(DB::raw(1))
                                                  ->from('ownermaster as o2')
                                                  ->whereColumn('o2.FlatId', 'r.flatid');
                                         });
                                });
                        });
                    });
            })
            ->when($blockMasterId, function ($q) use ($blockMasterId) {
                $q->where('o.BlockId', $blockMasterId);
            })
            ->select('v.VillageId', 'v.VillageName', 'v.map_pdf', DB::raw('count(distinct o.OwnerId) as total_beneficiaries'))
            ->groupBy('v.VillageId', 'v.VillageName', 'v.map_pdf')
            ->orderBy('v.VillageName', 'asc')
            ->get();

        $selectedVillageId = $request->input('village_id');
        if (!$selectedVillageId && $villages->isNotEmpty()) {
            $selectedVillageId = $villages->first()->VillageId;
        }
        $selectedVillageName = '';
        $selectedVillagePdf = '';
        $beneficiaries = [];
        $search = $request->input('search');

        if ($selectedVillageId) {
            $villageRecord = DB::table('villagemaster')->where('VillageId', $selectedVillageId)->first();
            $selectedVillageName = $villageRecord ? $villageRecord->VillageName : '';
            $selectedVillagePdf = $villageRecord ? $villageRecord->map_pdf : '';

            $query = DB::table('ownermaster as o')
                ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
                ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
                ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
                ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
                ->where('o.IsApproved', 1)
                ->where('o.IsPaid', 1)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('villagemaster as v')
                        ->whereColumn('v.VillageId', 'o.VillageId')
                        ->whereNotNull('v.plots')
                        ->whereNotNull('v.phase');
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('flatmaster as f')
                        ->whereColumn('f.FlatId', 'o.FlatId');
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('registary as r')
                        ->where(function($q) {
                            $q->where(function($sub) {
                                $sub->whereColumn('r.flatid', 'o.FlatId')
                                    ->whereNotNull('r.flatid')
                                    ->where('r.flatid', '!=', '');
                            })
                            ->orWhere(function($sub) {
                                $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                    ->whereNotNull('r.SecondPartyMobile')
                                    ->where('r.SecondPartyMobile', '!=', '')
                                    ->where(function($sub2) {
                                        $sub2->whereNull('r.flatid')
                                             ->orWhere('r.flatid', '')
                                             ->orWhereNotExists(function($sub3) {
                                                 $sub3->select(DB::raw(1))
                                                      ->from('ownermaster as o2')
                                                      ->whereColumn('o2.FlatId', 'r.flatid');
                                             });
                                    });
                            });
                        });
                })
                ->whereIn('o.OwnerId', function ($q) {
                    $q->select(DB::raw('MIN(OwnerId)'))
                        ->from('ownermaster')
                        ->groupBy('FlatId');
                })
                ->leftJoin('mmgay_possession_applications as ppa', 'o.OwnerId', '=', 'ppa.owner_id')
                ->where('o.Phase', $selectedPhase)
                ->where('o.VillageId', $selectedVillageId)
                ->when($blockMasterId, function ($q) use ($blockMasterId) {
                    $q->where('o.BlockId', $blockMasterId);
                });

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('o.OwnerName', 'like', "%{$search}%")
                      ->orWhere('o.MobileNo', 'like', "%{$search}%")
                      ->orWhere('o.RegistrationNo', 'like', "%{$search}%");
                });
            }

            $beneficiaries = $query->select(
                'o.OwnerId',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.secure_id',
                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'f.FlatNo',
                DB::raw("COALESCE(ppa.physical_possession_status, 'Eligible for Physical Possession') as possession_status"),
                'ppa.application_number',
                'o.PPPId',
                'o.MemberId',
                'o.OwnerAddress'
            )
            ->orderBy('o.OwnerName', 'asc')
            ->paginate(10)
            ->withQueryString();

            $beneficiaries->through(function ($ben) {
                return $this->formatLocationDetails($ben);
            });
        }

        $activeMenu = 'phase_report';
        return view('mmgay.bdo.phase_report', compact(
            'bdo',
            'phases',
            'selectedPhase',
            'villages',
            'selectedVillageId',
            'selectedVillageName',
            'selectedVillagePdf',
            'beneficiaries',
            'search',
            'activeMenu'
        ));
    }

    /**
     * Display BDO Villages Report.
     */
    public function villagesReport(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        if (!$blockMasterId) {
            abort(400, 'BDO block not defined.');
        }

        $selectedPhase = $request->input('phase');

        // Fetch Block Name
        $block = DB::table('blockmaster')->where('BlockId', $blockMasterId)->first();
        $blockName = $block->BlockName ?? 'Haryana';

        // Fetch distinct phases
        $phases = DB::table('ownermaster')
            ->whereNotNull('Phase')
            ->distinct()
            ->orderBy('Phase', 'asc')
            ->pluck('Phase');

        // Fetch villages having allotted flats under this BDO block with physical possession eligibility
        $villagesQuery = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->whereNotNull('v.plots')
            ->whereNotNull('v.phase')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            })
            ->when($blockMasterId, function ($q) use ($blockMasterId) {
                $q->where('o.BlockId', $blockMasterId);
            });

        if ($selectedPhase) {
            $villagesQuery->where('o.Phase', $selectedPhase);
        }

        $villages = $villagesQuery->select(
            'v.VillageId',
            'v.VillageName',
            'v.map_pdf',
            DB::raw("COUNT(DISTINCT o.OwnerId) as total_beneficiaries"),
            DB::raw("COUNT(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 AND o.IsRejected = 0 AND o.IsAllotmentCancelled = 0 THEN 1 END) as approved_paid"),
            DB::raw("COUNT(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 AND o.IsRejected = 0 AND o.IsAllotmentCancelled = 0 AND EXISTS (SELECT 1 FROM registary r WHERE (r.flatid = o.FlatId AND r.flatid IS NOT NULL AND r.flatid != '') OR (r.SecondPartyMobile = o.MobileNo AND r.SecondPartyMobile IS NOT NULL AND r.SecondPartyMobile != '' AND (r.flatid IS NULL OR r.flatid = '' OR NOT EXISTS (SELECT 1 FROM ownermaster o2 WHERE o2.FlatId = r.flatid)))) THEN 1 END) as approved_paid_matched"),
            DB::raw("COUNT(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 AND o.IsRejected = 0 AND o.IsAllotmentCancelled = 0 AND NOT EXISTS (SELECT 1 FROM registary r WHERE (r.flatid = o.FlatId AND r.flatid IS NOT NULL AND r.flatid != '') OR (r.SecondPartyMobile = o.MobileNo AND r.SecondPartyMobile IS NOT NULL AND r.SecondPartyMobile != '' AND (r.flatid IS NULL OR r.flatid = '' OR NOT EXISTS (SELECT 1 FROM ownermaster o2 WHERE o2.FlatId = r.flatid)))) THEN 1 END) as approved_paid_pending"),
            DB::raw("COUNT(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 0 AND o.IsRejected = 0 AND o.IsAllotmentCancelled = 0 THEN 1 END) as approved_unpaid"),
            DB::raw("COUNT(CASE WHEN o.IsApproved = 0 AND o.IsPaid = 0 AND o.IsRejected = 0 AND o.IsAllotmentCancelled = 0 THEN 1 END) as yet_to_be_done"),
            DB::raw("COUNT(CASE WHEN o.IsRejected = 1 AND o.IsAllotmentCancelled = 0 THEN 1 END) as rejected"),
            DB::raw("COUNT(CASE WHEN o.IsAllotmentCancelled = 1 THEN 1 END) as cancelled")
        )
        ->groupBy('v.VillageId', 'v.VillageName', 'v.map_pdf')
        ->orderBy('v.VillageName', 'asc')
        ->get();

        $activeMenu = 'villages_report';
        return view('mmgay.bdo.villages_report', compact(
            'bdo',
            'blockName',
            'phases',
            'selectedPhase',
            'villages',
            'activeMenu'
        ));
    }

    public function siteDevelopmentForm(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        // Fetch distinct phases
        $phases = DB::table('ownermaster')
            ->whereNotNull('Phase')
            ->distinct()
            ->orderBy('Phase', 'asc')
            ->pluck('Phase');

        $selectedPhase = $request->input('phase');
        if (!$selectedPhase && $phases->isNotEmpty()) {
            $selectedPhase = $phases->first();
        }

        // Fetch villages having entries in this phase under BDO's block
        $villages = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->where('o.Phase', $selectedPhase)
            ->where('o.BlockId', $blockMasterId)
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereNotNull('v.plots')
            ->whereNotNull('v.phase')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->where(function($q) {
                        $q->where(function($sub) {
                            $sub->whereColumn('r.flatid', 'o.FlatId')
                                ->whereNotNull('r.flatid')
                                ->where('r.flatid', '!=', '');
                        })
                        ->orWhere(function($sub) {
                            $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                ->whereNotNull('r.SecondPartyMobile')
                                ->where('r.SecondPartyMobile', '!=', '')
                                ->where(function($sub2) {
                                    $sub2->whereNull('r.flatid')
                                         ->orWhere('r.flatid', '')
                                         ->orWhereNotExists(function($sub3) {
                                             $sub3->select(DB::raw(1))
                                                  ->from('ownermaster as o2')
                                                  ->whereColumn('o2.FlatId', 'r.flatid');
                                         });
                                });
                        });
                    });
            })
            ->select('v.VillageId as VillageId', 'v.VillageName as VillageName', 'v.map_pdf as map_pdf')
            ->groupBy('v.VillageId', 'v.VillageName', 'v.map_pdf')
            ->orderBy('v.VillageName', 'asc')
            ->get();

        $selectedVillageId = $request->input('village_id');
        
        // Validate if selected village has entries in this phase
        $isValidVillageForPhase = $selectedVillageId && $villages->contains('VillageId', $selectedVillageId);

        if (!$isValidVillageForPhase) {
            if ($villages->isNotEmpty()) {
                $selectedVillageId = $villages->first()->VillageId;
            } else {
                $selectedVillageId = null;
            }
        }

        $selectedVillageName = '';
        $selectedVillagePdf = '';
        $siteDev = null;
        $photos = collect();
        $logs = collect();

        if ($selectedVillageId) {
            $villageRecord = DB::table('villagemaster')->where('VillageId', $selectedVillageId)->first();
            $selectedVillageName = $villageRecord ? $villageRecord->VillageName : '';
            $selectedVillagePdf = $villageRecord ? $villageRecord->map_pdf : '';

            $siteDev = \App\Models\MmgaySiteDevelopment::where('block_id', $blockMasterId)
                ->where('village_id', $selectedVillageId)
                ->where('phase', $selectedPhase)
                ->first();

            if ($siteDev) {
                $photos = $siteDev->photos;
                $logs = $siteDev->logs;
            }
        }

        $activeMenu = 'site_development';

        return view('mmgay.bdo.site_development', compact(
            'bdo',
            'villages',
            'phases',
            'selectedPhase',
            'selectedVillageId',
            'selectedVillageName',
            'selectedVillagePdf',
            'siteDev',
            'photos',
            'logs',
            'activeMenu'
        ));
    }

    public function siteDevelopmentSave(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        $villageId = $request->input('village_id');
        $phase = $request->input('phase');

        $siteDevExists = \App\Models\MmgaySiteDevelopment::where('block_id', $blockMasterId)
            ->where('village_id', $villageId)
            ->where('phase', $phase)
            ->first();

        $request->validate([
            'village_id' => 'required|integer',
            'phase' => 'required|string',
            'road_status' => 'required|string',
            'water_status' => 'required|string',
            'electricity_status' => 'required|string',
            'sewerage_status' => 'required|string',
            'remarks' => 'required|string',
            'road_photo' => ($siteDevExists && $siteDevExists->road_photo) ? 'nullable|image|mimes:jpg,jpeg,png|max:500' : 'required|image|mimes:jpg,jpeg,png|max:500',
            'water_photo' => ($siteDevExists && $siteDevExists->water_photo) ? 'nullable|image|mimes:jpg,jpeg,png|max:500' : 'required|image|mimes:jpg,jpeg,png|max:500',
            'electricity_photo' => ($siteDevExists && $siteDevExists->electricity_photo) ? 'nullable|image|mimes:jpg,jpeg,png|max:500' : 'required|image|mimes:jpg,jpeg,png|max:500',
            'sewerage_photo' => ($siteDevExists && $siteDevExists->sewerage_photo) ? 'nullable|image|mimes:jpg,jpeg,png|max:500' : 'required|image|mimes:jpg,jpeg,png|max:500',
        ]);

        // Verify if the village belongs to the BDO block and retrieve name
        $villageRecord = DB::table('villagemaster')
            ->where('VillageId', $villageId)
            ->where('BlockId', $blockMasterId)
            ->first();

        if (!$villageRecord) {
            return redirect()->back()->with('error', 'Unauthorized access to a village outside your block.');
        }

        $selectedVillageName = $villageRecord->VillageName;

        $districtId = $bdo->district_id;
        if (!$districtId && $blockMasterId) {
            $blockRecord = DB::table('blockmaster')->where('BlockId', $blockMasterId)->first();
            $districtId = $blockRecord ? $blockRecord->DistrictId : null;
        }

        $updateData = [
            'district_id' => $districtId,
            'block_id' => $blockMasterId,
            'village_id' => $villageId,
            'phase' => $phase,
            'road_status' => $request->input('road_status'),
            'water_status' => $request->input('water_status'),
            'electricity_status' => $request->input('electricity_status'),
            'sewerage_status' => $request->input('sewerage_status'),
            'remarks' => $request->input('remarks'),
            'updated_by' => $bdo->id,
        ];

        // Handle uploaded category photos using Laravel Storage public disk
        foreach (['road_photo', 'water_photo', 'electricity_photo', 'sewerage_photo'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                if ($file->isValid()) {
                    $sluggedVillageName = \Illuminate\Support\Str::slug($selectedVillageName);
                    $fileName = 'site_' . $field . '_' . $villageId . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    // Saves to: storage/app/public/site_developments/{village-name}/{filename}
                    $storedPath = $file->storeAs('site_developments/' . $sluggedVillageName, $fileName, 'public');
                    $updateData[$field] = $storedPath;
                }
            }
        }

        // Update or create site development record
        $siteDev = \App\Models\MmgaySiteDevelopment::updateOrCreate(
            [
                'district_id' => $districtId,
                'block_id' => $blockMasterId,
                'village_id' => $villageId,
                'phase' => $phase,
            ],
            $updateData
        );

        // Record the submission in audit log trail
        \App\Models\MmgaySiteDevelopmentLog::create([
            'site_development_id' => $siteDev->id,
            'district_id' => $districtId,
            'block_id' => $blockMasterId,
            'village_id' => $villageId,
            'phase' => $phase,
            'road_status' => $siteDev->road_status,
            'water_status' => $siteDev->water_status,
            'electricity_status' => $siteDev->electricity_status,
            'sewerage_status' => $siteDev->sewerage_status,
            'remarks' => $siteDev->remarks,
            'updated_by' => $bdo->id,
            'updated_by_name' => $bdo->name ?? 'BDO Officer',
        ]);

        return redirect()->route('mmgay.bdo.site-development', ['village_id' => $villageId, 'phase' => $phase])
            ->with('success', 'Site Development details updated successfully.');
    }

    /**
     * Show BDO user profile details and password change option.
     */
    public function profile(Request $request)
    {
        $bdo = Auth::user();
        $activeMenu = 'profile';
        return view('mmgay.bdo.profile', compact('bdo', 'activeMenu'));
    }

    /**
     * Handle password change request for BDO officer.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|different:current_password|confirmed',
        ], [
            'new_password.different' => 'The new password must be different from current password.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
        ]);

        $bdo = Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $bdo->password)) {
            return redirect()->back()->with('error', 'Your current password does not match our records.');
        }

        // Update password in database
        $bdo->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        
        // Save using Eloquent to update DB
        $user = \App\Models\User::find($bdo->id);
        $user->password = $bdo->password;
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    /**
     * Show HFA API testing tool page.
     */
    public function hfaApiTestForm(Request $request)
    {
        $bdo = Auth::user();
        $activeMenu = 'hfa_api_test';
        return view('mmgay.bdo.hfa_test_api', compact('bdo', 'activeMenu'));
    }

    /**
     * Handle submission and hit HFA API from the server.
     */
    public function hfaApiTestSubmit(Request $request)
    {
        $request->validate([
            'registration_no' => 'required_without_all:from_date,to_date|nullable|string',
            'from_date' => 'required_without:registration_no|required_with:to_date|nullable|date',
            'to_date' => 'required_without:registration_no|required_with:from_date|nullable|date',
        ]);

        $regNo = $request->input('registration_no');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $apiUrl = 'https://api.revenueharyana.gov.in/api/LandRegistration/getRegistrationforHFALand';
        $headers = [
            'X-API-KEY' => 'HFA26@hry#',
            'Accept' => 'application/json',
        ];

        $queryParams = [];
        if (!empty($regNo)) {
            $queryParams['RegistrationNo'] = trim($regNo);
        } else {
            $queryParams['RegFromDate'] = $fromDate;
            $queryParams['RegToDate'] = $toDate;
        }

        $startTime = microtime(true);
        $statusCode = null;
        $responseBody = null;
        $responseHeaders = [];
        $errorMessage = null;

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->withHeaders($headers)
                ->get($apiUrl, $queryParams);

            $statusCode = $response->status();
            $responseBody = $response->body();
            $responseHeaders = $response->headers();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        $responseTime = round((microtime(true) - $startTime) * 1000, 2); // in ms

        // Try to decode json for pretty printing
        $decodedJson = null;
        if ($responseBody) {
            $decodedJson = json_decode($responseBody, true);
        }

        return redirect()->back()->withInput()->with('api_result', [
            'url' => $apiUrl . '?' . http_build_query($queryParams),
            'headers_sent' => $headers,
            'status' => $statusCode,
            'time_ms' => $responseTime,
            'error' => $errorMessage,
            'response_headers' => $responseHeaders,
            'raw_body' => $responseBody,
            'decoded_json' => $decodedJson,
        ]);
    }

    /**
     * Helper to restrict BDO actions if Site Development is not complete.
     */
    private function restrictBySiteDevelopment($owner)
    {
        if (!$owner) {
            return null;
        }

        $siteDev = \App\Models\MmgaySiteDevelopment::where('block_id', $owner->BlockId)
            ->where('village_id', $owner->VillageId)
            ->where('phase', $owner->Phase)
            ->first();

        if (!$siteDev || !$siteDev->road_photo || !$siteDev->water_photo || !$siteDev->electricity_photo || !$siteDev->sewerage_photo) {
            return redirect()->back()->with('error', 'Action Restricted: Please upload Site Development progress and photos for village: ' . ($owner->VillageName ?? 'this village') . ' (Phase ' . ($owner->Phase ?? 'N/A') . ') | कार्रवाई प्रतिबंधित: कृपया पहले इस गांव के Phase ' . ($owner->Phase ?? 'N/A') . ' के लिए Site Development का विवरण और फोटो अपलोड करें।');
        }

        return null;
    }

    /**
     * Display BDO Owner Status Report.
     */
    /**
     * Display BDO Owner Status Report.
     */
    public function ownerStatusReport(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        if (!$blockMasterId) {
            abort(400, 'BDO block not defined.');
        }

        $selectedPhase = $request->input('phase');

        // Fetch Block Name
        $block = DB::table('blockmaster')->where('BlockId', $blockMasterId)->first();
        $blockName = $block->BlockName ?? 'Haryana';

        // 1. Fetch villages for BDO block having valid entries in villagemaster for this phase
        $villagesQuery = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->where('o.BlockId', $blockMasterId)
            ->whereNotNull('v.plots')
            ->whereNotNull('v.phase')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            });

        if ($selectedPhase) {
            $villagesQuery->where('o.Phase', $selectedPhase);
        }

        $villages = $villagesQuery->select('v.VillageId', 'v.VillageName')
            ->groupBy('v.VillageId', 'v.VillageName')
            ->orderBy('v.VillageName', 'asc')
            ->get();

        // 2. Fetch phase list
        $phases = DB::table('ownermaster')
            ->whereNotNull('Phase')
            ->distinct()
            ->orderBy('Phase', 'asc')
            ->pluck('Phase');

        $selectedVillageId = $request->input('village_id');
        if ($selectedVillageId && !$villages->contains('VillageId', $selectedVillageId)) {
            $selectedVillageId = null;
        }
        $search = $request->input('search');

        // Overall Block Statistics Queries
        // Total Villages Count matching phase
        $villagesCountQuery = DB::table('ownermaster as o')
            ->where('o.BlockId', $blockMasterId)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('villagemaster as v')
                    ->whereColumn('v.VillageId', 'o.VillageId')
                    ->whereNotNull('v.plots')
                    ->whereNotNull('v.phase');
            });
        if ($selectedPhase) {
            $villagesCountQuery->where('o.Phase', $selectedPhase);
        }
        $totalVillagesCount = $villagesCountQuery->distinct()->count('o.VillageId');

        // Total Applicants Count matching phase
        $applicantsCountQuery = DB::table('ownermaster as o')
            ->where('o.BlockId', $blockMasterId)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('villagemaster as v')
                    ->whereColumn('v.VillageId', 'o.VillageId')
                    ->whereNotNull('v.plots')
                    ->whereNotNull('v.phase');
            });
        if ($selectedPhase) {
            $applicantsCountQuery->where('o.Phase', $selectedPhase);
        }
        $totalApplicantsCount = $applicantsCountQuery->count();

        // Total Allotted Count matching filters
        $allottedCountQuery = DB::table('ownermaster as o')
            ->where('o.BlockId', $blockMasterId)
            ->whereNotNull('o.FlatId')
            ->where('o.FlatId', '>', 0)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('villagemaster as v')
                    ->whereColumn('v.VillageId', 'o.VillageId')
                    ->whereNotNull('v.plots')
                    ->whereNotNull('v.phase');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            });
        if ($selectedPhase) {
            $allottedCountQuery->where('o.Phase', $selectedPhase);
        }
        if ($selectedVillageId) {
            $allottedCountQuery->where('o.VillageId', $selectedVillageId);
        }
        if ($search) {
            $allottedCountQuery->where(function($q) use ($search) {
                $q->where('o.OwnerName', 'like', "%{$search}%")
                  ->orWhere('o.MobileNo', 'like', "%{$search}%")
                  ->orWhere('o.RegistrationNo', 'like', "%{$search}%");
            });
        }
        $totalAllottedCount = $allottedCountQuery->count();

        // Status Tabs counts (Conditional Count)
        $countQuery = DB::table('ownermaster as o')
            ->where('o.BlockId', $blockMasterId)
            ->whereNotNull('o.FlatId')
            ->where('o.FlatId', '>', 0)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('villagemaster as v')
                    ->whereColumn('v.VillageId', 'o.VillageId')
                    ->whereNotNull('v.plots')
                    ->whereNotNull('v.phase');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f')
                    ->whereColumn('f.FlatId', 'o.FlatId');
            })
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            });

        if ($selectedPhase) {
            $countQuery->where('o.Phase', $selectedPhase);
        }
        if ($selectedVillageId) {
            $countQuery->where('o.VillageId', $selectedVillageId);
        }
        if ($search) {
            $countQuery->where(function ($q) use ($search) {
                $q->where('o.OwnerName', 'like', "%{$search}%")
                  ->orWhere('o.MobileNo', 'like', "%{$search}%")
                  ->orWhere('o.RegistrationNo', 'like', "%{$search}%");
            });
        }

        $counts = $countQuery->select(
            DB::raw("COUNT(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 AND o.IsRejected = 0 AND o.IsAllotmentCancelled = 0 THEN 1 END) as approved_paid"),
            DB::raw("COUNT(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 0 AND o.IsRejected = 0 AND o.IsAllotmentCancelled = 0 THEN 1 END) as approved_unpaid"),
            DB::raw("COUNT(CASE WHEN o.IsApproved = 0 AND o.IsPaid = 0 AND o.IsRejected = 0 AND o.IsAllotmentCancelled = 0 THEN 1 END) as yet_to_be_done"),
            DB::raw("COUNT(CASE WHEN o.IsRejected = 1 AND o.IsAllotmentCancelled = 0 THEN 1 END) as rejected"),
            DB::raw("COUNT(CASE WHEN o.IsAllotmentCancelled = 1 THEN 1 END) as cancelled")
        )->first();

        $grossTotal = $counts->approved_paid + $counts->approved_unpaid + $counts->yet_to_be_done + $counts->rejected + $counts->cancelled;

        // 4. Fetch list based on active tab
        $activeTab = $request->input('status', 'approved_paid');

        if ($activeTab === 'applicants') {
            $listQuery = DB::table('ownermaster as o')
                ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
                ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
                ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
                ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
                ->where('o.BlockId', $blockMasterId)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('villagemaster as v2')
                        ->whereColumn('v2.VillageId', 'o.VillageId')
                        ->whereNotNull('v2.plots')
                        ->whereNotNull('v2.phase');
                });
        } else {
            $listQuery = DB::table('ownermaster as o')
                ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
                ->leftJoin('districtmaster as d', 'f.DistrictId', '=', 'd.DistrictId')
                ->leftJoin('blockmaster as b', 'f.BlockId', '=', 'b.BlockId')
                ->leftJoin('villagemaster as v', 'f.VillageId', '=', 'v.VillageId')
                ->where('o.BlockId', $blockMasterId)
                ->whereNotNull('o.FlatId')
                ->where('o.FlatId', '>', 0)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('villagemaster as v2')
                        ->whereColumn('v2.VillageId', 'o.VillageId')
                        ->whereNotNull('v2.plots')
                        ->whereNotNull('v2.phase');
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('flatmaster as f')
                        ->whereColumn('f.FlatId', 'o.FlatId');
                })
                ->whereIn('o.OwnerId', function ($q) {
                    $q->select(DB::raw('MIN(OwnerId)'))
                        ->from('ownermaster')
                        ->groupBy('FlatId');
                });
        }

        if ($selectedPhase) {
            $listQuery->where('o.Phase', $selectedPhase);
        }
        if ($selectedVillageId) {
            $listQuery->where('o.VillageId', $selectedVillageId);
        }
        if ($search) {
            $listQuery->where(function ($q) use ($search) {
                $q->where('o.OwnerName', 'like', "%{$search}%")
                  ->orWhere('o.MobileNo', 'like', "%{$search}%")
                  ->orWhere('o.RegistrationNo', 'like', "%{$search}%");
            });
        }

        if ($activeTab === 'approved_paid') {
            $listQuery->where('o.IsApproved', 1)->where('o.IsPaid', 1)->where('o.IsRejected', 0)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'approved_unpaid') {
            $listQuery->where('o.IsApproved', 1)->where('o.IsPaid', 0)->where('o.IsRejected', 0)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'yet_to_be_done') {
            $listQuery->where('o.IsApproved', 0)->where('o.IsPaid', 0)->where('o.IsRejected', 0)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'rejected') {
            $listQuery->where('o.IsRejected', 1)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'cancelled') {
            $listQuery->where('o.IsAllotmentCancelled', 1);
        }

        $owners = $listQuery->select(
            'o.OwnerId',
            'o.OwnerName',
            'o.FatherHusbandName',
            'o.MobileNo',
            'o.RegistrationNo',
            'o.Phase',
            'o.IsApproved',
            'o.IsPaid',
            'o.IsRejected',
            'o.IsAllotmentCancelled',
            DB::raw("EXISTS (
                SELECT 1 FROM registary as r 
                WHERE (r.flatid = o.FlatId AND r.flatid IS NOT NULL AND r.flatid != '')
                   OR (
                       r.SecondPartyMobile = o.MobileNo 
                       AND r.SecondPartyMobile IS NOT NULL 
                       AND r.SecondPartyMobile != ''
                       AND (r.flatid IS NULL OR r.flatid = '' OR NOT EXISTS (
                           SELECT 1 FROM ownermaster as o2 WHERE o2.FlatId = r.flatid
                       ))
                   )
            ) as registry_matched"),
            'd.DistrictName',
            'b.BlockName',
            'v.VillageName',
            'f.FlatNo'
        )
        ->paginate(25)
        ->withQueryString();

        $owners->through(function ($owner) {
            return $this->formatLocationDetails($owner);
        });

        $activeMenu = 'owner_status_report';
        return view('mmgay.bdo.owner_status_report', compact(
            'bdo',
            'blockName',
            'villages',
            'phases',
            'counts',
            'owners',
            'activeTab',
            'selectedPhase',
            'selectedVillageId',
            'search',
            'totalVillagesCount',
            'totalApplicantsCount',
            'totalAllottedCount',
            'grossTotal',
            'activeMenu'
        ));
    }

    /**
     * Get owner registry details by mobile number.
     */
    public function getOwnerRegistryDetails($mobile)
    {
        try {
            $registry = DB::table('registary')
                ->where('SecondPartyMobile', $mobile)
                ->first();

            if ($registry) {
                return response()->json([
                    'success' => true,
                    'registry' => $registry
                ]);
            }

            return response()->json([
                'success' => false,
                'registry' => null,
                'message' => 'No registry details found for this mobile number.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'registry' => null,
                'message' => 'Error fetching registry details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export owner status report to CSV format.
     */
    public function ownerStatusReportExportCsv(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        if (!$blockMasterId) {
            abort(400, 'BDO block not defined.');
        }

        $selectedPhase = $request->input('phase');
        $selectedVillageId = $request->input('village_id');
        $search = $request->input('search');
        $activeTab = $request->input('status', 'approved_paid');

        if ($activeTab === 'applicants') {
            $query = DB::table('ownermaster as o')
                ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
                ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
                ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
                ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
                ->where('o.BlockId', $blockMasterId)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('villagemaster as v2')
                        ->whereColumn('v2.VillageId', 'o.VillageId')
                        ->whereNotNull('v2.plots')
                        ->whereNotNull('v2.phase');
                });
        } else {
            $query = DB::table('ownermaster as o')
                ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
                ->leftJoin('districtmaster as d', 'f.DistrictId', '=', 'd.DistrictId')
                ->leftJoin('blockmaster as b', 'f.BlockId', '=', 'b.BlockId')
                ->leftJoin('villagemaster as v', 'f.VillageId', '=', 'v.VillageId')
                ->where('o.BlockId', $blockMasterId)
                ->whereNotNull('o.FlatId')
                ->where('o.FlatId', '>', 0)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('villagemaster as v2')
                        ->whereColumn('v2.VillageId', 'o.VillageId')
                        ->whereNotNull('v2.plots')
                        ->whereNotNull('v2.phase');
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('flatmaster as f')
                        ->whereColumn('f.FlatId', 'o.FlatId');
                })
                ->whereIn('o.OwnerId', function ($q) {
                    $q->select(DB::raw('MIN(OwnerId)'))
                        ->from('ownermaster')
                        ->groupBy('FlatId');
                });
        }

        if ($selectedPhase) {
            $query->where('o.Phase', $selectedPhase);
        }
        if ($selectedVillageId) {
            $query->where('o.VillageId', $selectedVillageId);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $query->where('o.OwnerName', 'like', "%{$search}%")
                  ->orWhere('o.MobileNo', 'like', "%{$search}%")
                  ->orWhere('o.RegistrationNo', 'like', "%{$search}%");
            });
        }

        if ($activeTab === 'approved_paid') {
            $query->where('o.IsApproved', 1)->where('o.IsPaid', 1)->where('o.IsRejected', 0)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'approved_unpaid') {
            $query->where('o.IsApproved', 1)->where('o.IsPaid', 0)->where('o.IsRejected', 0)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'yet_to_be_done') {
            $query->where('o.IsApproved', 0)->where('o.IsPaid', 0)->where('o.IsRejected', 0)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'rejected') {
            $query->where('o.IsRejected', 1)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'cancelled') {
            $query->where('o.IsAllotmentCancelled', 1);
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Owner_Status_Report_' . str_replace(' ', '_', $activeTab) . '_' . date('Ymd_His') . '.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($query) {
            $file = fopen('php://output', 'w');
            
            // Add CSV Headers
            fputcsv($file, [
                'SR NO.',
                'REGISTRATION NO.',
                'OWNER NAME',
                'FATHER/HUSBAND NAME',
                'MOBILE NO.',
                'PHASE',
                'VILLAGE',
                'FLAT NO.',
                'BLOCK',
                'DISTRICT'
            ]);

            $sr = 1;
            
            $query->select(
                'o.OwnerId',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.Phase',
                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'f.FlatNo'
            )->orderBy('o.OwnerId', 'asc')
            ->chunk(1000, function($rows) use (&$sr, $file) {
                foreach ($rows as $row) {
                    $row = $this->formatLocationDetails($row);
                    fputcsv($file, [
                        $sr++,
                        $row->RegistrationNo,
                        $row->OwnerName,
                        $row->FatherHusbandName,
                        $row->MobileNo,
                        'Phase ' . $row->Phase,
                        $row->VillageName,
                        $row->FlatNo ?? 'N/A',
                        $row->BlockName,
                        $row->DistrictName
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export owner status report to PDF format.
     */
    public function ownerStatusReportExportPdf(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        if (!$blockMasterId) {
            abort(400, 'BDO block not defined.');
        }

        $selectedPhase = $request->input('phase');
        $selectedVillageId = $request->input('village_id');
        $search = $request->input('search');
        $activeTab = $request->input('status', 'approved_paid');

        if ($activeTab === 'applicants') {
            $query = DB::table('ownermaster as o')
                ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
                ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
                ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
                ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
                ->where('o.BlockId', $blockMasterId)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('villagemaster as v2')
                        ->whereColumn('v2.VillageId', 'o.VillageId')
                        ->whereNotNull('v2.plots')
                        ->whereNotNull('v2.phase');
                });
        } else {
            $query = DB::table('ownermaster as o')
                ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
                ->leftJoin('districtmaster as d', 'f.DistrictId', '=', 'd.DistrictId')
                ->leftJoin('blockmaster as b', 'f.BlockId', '=', 'b.BlockId')
                ->leftJoin('villagemaster as v', 'f.VillageId', '=', 'v.VillageId')
                ->where('o.BlockId', $blockMasterId)
                ->whereNotNull('o.FlatId')
                ->where('o.FlatId', '>', 0)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('villagemaster as v2')
                        ->whereColumn('v2.VillageId', 'o.VillageId')
                        ->whereNotNull('v2.plots')
                        ->whereNotNull('v2.phase');
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('flatmaster as f')
                        ->whereColumn('f.FlatId', 'o.FlatId');
                })
                ->whereIn('o.OwnerId', function ($q) {
                    $q->select(DB::raw('MIN(OwnerId)'))
                        ->from('ownermaster')
                        ->groupBy('FlatId');
                });
        }

        if ($selectedPhase) {
            $query->where('o.Phase', $selectedPhase);
        }
        if ($selectedVillageId) {
            $query->where('o.VillageId', $selectedVillageId);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('o.OwnerName', 'like', "%{$search}%")
                  ->orWhere('o.MobileNo', 'like', "%{$search}%")
                  ->orWhere('o.RegistrationNo', 'like', "%{$search}%");
            });
        }

        if ($activeTab === 'approved_paid') {
            $query->where('o.IsApproved', 1)->where('o.IsPaid', 1)->where('o.IsRejected', 0)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'approved_unpaid') {
            $query->where('o.IsApproved', 1)->where('o.IsPaid', 0)->where('o.IsRejected', 0)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'yet_to_be_done') {
            $query->where('o.IsApproved', 0)->where('o.IsPaid', 0)->where('o.IsRejected', 0)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'rejected') {
            $query->where('o.IsRejected', 1)->where('o.IsAllotmentCancelled', 0);
        } elseif ($activeTab === 'cancelled') {
            $query->where('o.IsAllotmentCancelled', 1);
        }

        $totalCount = $query->count();
        if ($totalCount > 1000) {
            return redirect()->back()->with('error', 'The PDF export is limited to 1,000 records to prevent memory issues. Please filter your search, or download as CSV instead.');
        }

        $owners = $query->select(
            'o.OwnerId',
            'o.OwnerName',
            'o.FatherHusbandName',
            'o.MobileNo',
            'o.RegistrationNo',
            'o.Phase',
            'd.DistrictName',
            'b.BlockName',
            'v.VillageName',
            'f.FlatNo'
        )
        ->orderBy('o.OwnerName', 'asc')
        ->take(1000)
        ->get();

        $owners->map(function ($owner) {
            return $this->formatLocationDetails($owner);
        });

        $pdfData = [
            'bdo' => $bdo,
            'owners' => $owners,
            'status' => $activeTab,
            'report_date' => date('d M Y h:i A')
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mmgay.bdo.pdf.owner-status-pdf', $pdfData)
            ->setPaper('a4', 'landscape');

        return $pdf->download('Owner_Status_Report_' . str_replace(' ', '_', $activeTab) . '_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Display BDO Category-Wise Beneficiaries Report (Ghumantu, Widow, SC, Others)
     * Matches Beneficiary for Physical Possession & Registry logic.
     */
    public function categoryBeneficiariesReport(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        // Fetch Block Details
        $block = $blockMasterId ? DB::table('blockmaster')->where('BlockId', $blockMasterId)->first() : null;
        $blockName = $block->BlockName ?? 'Block';

        // Fetch District Details
        $district = $block ? DB::table('districtmaster')->where('DistrictId', $block->DistrictId)->first() : null;
        $districtName = $district->DistrictName ?? '';

        // Query parameters
        $selectedCategory = strtolower(trim($request->input('category', 'all')));
        $selectedPhase = $request->input('phase');
        $selectedVillageId = $request->input('village_id');
        $search = trim($request->input('search', ''));

        // Distinct Phases
        $phases = DB::table('ownermaster')
            ->whereNotNull('Phase')
            ->distinct()
            ->orderBy('Phase', 'asc')
            ->pluck('Phase');

        // Base query for BDO block with exact registry & possession eligibility logic
        $baseQuery = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('mmgay_possession_applications as ppa', 'o.OwnerId', '=', 'ppa.owner_id')
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            })
            ->whereNotNull('v.plots')
            ->whereNotNull('v.phase')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f2')
                    ->whereColumn('f2.FlatId', 'o.FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->whereColumn('r.flatid', 'o.FlatId')
                                ->whereNotNull('r.flatid')
                                ->where('r.flatid', '!=', '');
                        })
                        ->orWhere(function ($sub) {
                            $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                ->whereNotNull('r.SecondPartyMobile')
                                ->where('r.SecondPartyMobile', '!=', '')
                                ->where(function ($sub2) {
                                    $sub2->whereNull('r.flatid')
                                         ->orWhere('r.flatid', '')
                                         ->orWhereNotExists(function ($sub3) {
                                             $sub3->select(DB::raw(1))
                                                  ->from('ownermaster as o2')
                                                  ->whereColumn('o2.FlatId', 'r.flatid');
                                         });
                                });
                        });
                    });
            });

        if ($blockMasterId) {
            $baseQuery->where('o.BlockId', $blockMasterId);
        }

        // Available Villages in this block that meet the criteria
        $villages = (clone $baseQuery)
            ->select('v.VillageId', 'v.VillageName')
            ->distinct()
            ->orderBy('v.VillageName', 'asc')
            ->get();

        // Calculate KPI category metrics for this block
        $kpiQuery = clone $baseQuery;
        if ($selectedPhase) {
            $kpiQuery->where('o.Phase', $selectedPhase);
        }
        if ($selectedVillageId) {
            $kpiQuery->where('o.VillageId', $selectedVillageId);
        }

        $statsRaw = (clone $kpiQuery)
            ->select(
                DB::raw('COUNT(DISTINCT o.OwnerId) as total_count'),
                DB::raw("COUNT(DISTINCT CASE WHEN o.Caste = 'Ghumantu' THEN o.OwnerId END) as ghumantu_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN o.Caste = 'Widow' THEN o.OwnerId END) as widow_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN o.Caste = 'SC' THEN o.OwnerId END) as sc_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN o.Caste NOT IN ('Ghumantu', 'Widow', 'SC') OR o.Caste IS NULL OR o.Caste = '' THEN o.OwnerId END) as others_count")
            )->first();

        $stats = [
            'total' => (int) ($statsRaw->total_count ?? 0),
            'ghumantu' => (int) ($statsRaw->ghumantu_count ?? 0),
            'widow' => (int) ($statsRaw->widow_count ?? 0),
            'sc' => (int) ($statsRaw->sc_count ?? 0),
            'others' => (int) ($statsRaw->others_count ?? 0),
        ];

        // Village-wise Breakdown for the block
        $villageBreakdownQuery = clone $baseQuery;
        if ($selectedPhase) {
            $villageBreakdownQuery->where('o.Phase', $selectedPhase);
        }
        $villageBreakdown = $villageBreakdownQuery
            ->select(
                'v.VillageId',
                'v.VillageName',
                DB::raw('COUNT(DISTINCT o.OwnerId) as total'),
                DB::raw("COUNT(DISTINCT CASE WHEN o.Caste = 'Ghumantu' THEN o.OwnerId END) as ghumantu"),
                DB::raw("COUNT(DISTINCT CASE WHEN o.Caste = 'Widow' THEN o.OwnerId END) as widow"),
                DB::raw("COUNT(DISTINCT CASE WHEN o.Caste = 'SC' THEN o.OwnerId END) as sc"),
                DB::raw("COUNT(DISTINCT CASE WHEN o.Caste NOT IN ('Ghumantu', 'Widow', 'SC') OR o.Caste IS NULL OR o.Caste = '' THEN o.OwnerId END) as others")
            )
            ->groupBy('v.VillageId', 'v.VillageName')
            ->orderBy('v.VillageName', 'asc')
            ->get();

        // Build list query with all active filters
        $listQuery = clone $baseQuery;

        if ($selectedPhase) {
            $listQuery->where('o.Phase', $selectedPhase);
        }

        if ($selectedVillageId) {
            $listQuery->where('o.VillageId', $selectedVillageId);
        }

        // Category filter
        if ($selectedCategory === 'ghumantu') {
            $listQuery->where('o.Caste', 'Ghumantu');
        } elseif ($selectedCategory === 'widow') {
            $listQuery->where('o.Caste', 'Widow');
        } elseif ($selectedCategory === 'sc') {
            $listQuery->where('o.Caste', 'SC');
        } elseif ($selectedCategory === 'others') {
            $listQuery->where(function ($q) {
                $q->whereNotIn('o.Caste', ['Ghumantu', 'Widow', 'SC'])
                  ->orWhereNull('o.Caste')
                  ->orWhere('o.Caste', '');
            });
        }

        // Search filter
        if ($search !== '') {
            $listQuery->where(function ($q) use ($search) {
                $q->where('o.OwnerName', 'like', "%{$search}%")
                  ->orWhere('o.FatherHusbandName', 'like', "%{$search}%")
                  ->orWhere('o.MobileNo', 'like', "%{$search}%")
                  ->orWhere('o.RegistrationNo', 'like', "%{$search}%")
                  ->orWhere('o.PPPId', 'like', "%{$search}%")
                  ->orWhere('f.FlatNo', 'like', "%{$search}%");
            });
        }

        $beneficiaries = $listQuery->select(
            'o.OwnerId',
            'o.secure_id',
            'o.OwnerName',
            'o.FatherHusbandName',
            'o.MobileNo',
            'o.RegistrationNo',
            'o.PPPId',
            'o.MemberId',
            'o.Caste',
            'o.Phase',
            'v.VillageId',
            'v.VillageName',
            'f.FlatNo',
            'b.BlockName',
            'd.DistrictName',
            DB::raw("COALESCE(ppa.physical_possession_status, 'Eligible for Physical Possession') as possession_status"),
            'ppa.application_number'
        )
        ->orderBy('v.VillageName', 'asc')
        ->orderBy('o.OwnerName', 'asc')
        ->paginate(50)
        ->withQueryString();

        $activeMenu = 'category_beneficiaries';

        return view('mmgay.bdo.category_beneficiaries', compact(
            'bdo',
            'blockName',
            'districtName',
            'phases',
            'selectedPhase',
            'villages',
            'selectedVillageId',
            'selectedCategory',
            'search',
            'stats',
            'villageBreakdown',
            'beneficiaries',
            'activeMenu'
        ));
    }

    /**
     * Export Category Beneficiaries list to CSV
     */
    public function categoryBeneficiariesExportCsv(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        $selectedCategory = strtolower(trim($request->input('category', 'all')));
        $selectedPhase = $request->input('phase');
        $selectedVillageId = $request->input('village_id');
        $search = trim($request->input('search', ''));

        $query = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('mmgay_possession_applications as ppa', 'o.OwnerId', '=', 'ppa.owner_id')
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereIn('o.OwnerId', function ($q) {
                $q->select(DB::raw('MIN(OwnerId)'))
                    ->from('ownermaster')
                    ->groupBy('FlatId');
            })
            ->whereNotNull('v.plots')
            ->whereNotNull('v.phase')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flatmaster as f2')
                    ->whereColumn('f2.FlatId', 'o.FlatId');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->whereColumn('r.flatid', 'o.FlatId')
                                ->whereNotNull('r.flatid')
                                ->where('r.flatid', '!=', '');
                        })
                        ->orWhere(function ($sub) {
                            $sub->whereColumn('r.SecondPartyMobile', 'o.MobileNo')
                                ->whereNotNull('r.SecondPartyMobile')
                                ->where('r.SecondPartyMobile', '!=', '')
                                ->where(function ($sub2) {
                                    $sub2->whereNull('r.flatid')
                                         ->orWhere('r.flatid', '')
                                         ->orWhereNotExists(function ($sub3) {
                                             $sub3->select(DB::raw(1))
                                                  ->from('ownermaster as o2')
                                                  ->whereColumn('o2.FlatId', 'r.flatid');
                                         });
                                });
                        });
                    });
            });

        if ($blockMasterId) {
            $query->where('o.BlockId', $blockMasterId);
        }

        if ($selectedPhase) {
            $query->where('o.Phase', $selectedPhase);
        }

        if ($selectedVillageId) {
            $query->where('o.VillageId', $selectedVillageId);
        }

        if ($selectedCategory === 'ghumantu') {
            $query->where('o.Caste', 'Ghumantu');
        } elseif ($selectedCategory === 'widow') {
            $query->where('o.Caste', 'Widow');
        } elseif ($selectedCategory === 'sc') {
            $query->where('o.Caste', 'SC');
        } elseif ($selectedCategory === 'others') {
            $query->where(function ($q) {
                $q->whereNotIn('o.Caste', ['Ghumantu', 'Widow', 'SC'])
                  ->orWhereNull('o.Caste')
                  ->orWhere('o.Caste', '');
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('o.OwnerName', 'like', "%{$search}%")
                  ->orWhere('o.FatherHusbandName', 'like', "%{$search}%")
                  ->orWhere('o.MobileNo', 'like', "%{$search}%")
                  ->orWhere('o.RegistrationNo', 'like', "%{$search}%")
                  ->orWhere('o.PPPId', 'like', "%{$search}%")
                  ->orWhere('f.FlatNo', 'like', "%{$search}%");
            });
        }

        $records = $query->select(
            'o.RegistrationNo',
            'o.OwnerName',
            'o.FatherHusbandName',
            'o.MobileNo',
            'o.PPPId',
            'o.Caste',
            'v.VillageName',
            'f.FlatNo',
            'o.Phase',
            DB::raw("COALESCE(ppa.physical_possession_status, 'Eligible for Physical Possession') as possession_status")
        )
        ->orderBy('v.VillageName', 'asc')
        ->orderBy('o.OwnerName', 'asc')
        ->get();

        $filename = 'Category_Beneficiaries_' . ($selectedCategory ?: 'all') . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($handle, ['Sr. No.', 'Registration ID', 'Beneficiary Name', 'Father/Husband Name', 'Mobile No', 'PPP ID', 'Category', 'Village', 'Plot / Flat No', 'Phase', 'Possession Status']);

            $i = 1;
            foreach ($records as $r) {
                $resolvedCategory = match ($r->Caste) {
                    'Ghumantu' => 'Ghumantu',
                    'Widow' => 'Widow',
                    'SC' => 'Scheduled Caste (SC)',
                    default => 'Others (' . ($r->Caste ?: 'General') . ')'
                };

                fputcsv($handle, [
                    $i++,
                    $r->RegistrationNo ?? '—',
                    $r->OwnerName ?? '—',
                    $r->FatherHusbandName ?? '—',
                    $r->MobileNo ?? '—',
                    $r->PPPId ?? '—',
                    $resolvedCategory,
                    $r->VillageName ?? '—',
                    $r->FlatNo ?? '—',
                    $r->Phase ? 'Phase ' . $r->Phase : '—',
                    $r->possession_status ?? '—',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    private function formatLocationDetails($owner)
    {
        if (!$owner) return $owner;

        $owner->BlockName = "Property Allotted Block: " . ($owner->BlockName ?? '—');
        $owner->VillageName = "Property Allotted Village: " . ($owner->VillageName ?? '—');
        $owner->DistrictName = "Property Allotted District: " . ($owner->DistrictName ?? '—');

        if (isset($owner->FlatNo)) {
            $owner->FlatNo = "Property Allotted Flat No: " . $owner->FlatNo;
        }
        if (isset($owner->OwnerAddress)) {
            $owner->OwnerAddress = "Applicant Address: " . $owner->OwnerAddress;
        }

        return $owner;
    }
}
