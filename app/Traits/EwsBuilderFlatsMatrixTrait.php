<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EwsBuilderFlat;
use App\Models\EwsProject;
use App\Models\EwsTown;
use App\Models\User;
use App\Helpers\EwsHelper;

trait EwsBuilderFlatsMatrixTrait
{
    /**
     * Compute statewide or zone-specific summary telemetry for Offered Flats and DTP Approvals.
     * Uses a single optimized aggregate query backed by indexes.
     */
    public function getMatrixSummaryStats($zoneId = null)
    {
        $flatsQuery = DB::table('ews_builder_flats');
        if (!empty($zoneId)) {
            $zoneDistIds = DB::table('ews_districts')->where('zone_id', $zoneId)->pluck('id')->toArray();
            $zoneDistNames = DB::table('ews_districts')->where('zone_id', $zoneId)->pluck('name')->toArray();
            $zoneModel = DB::table('ews_stp_districts')->where('id', $zoneId)->first();
            $zoneName = $zoneModel ? $zoneModel->name : null;

            $flatsQuery->where(function ($q) use ($zoneId, $zoneDistIds, $zoneDistNames, $zoneName) {
                $q->where('zone_id', $zoneId);
                if (!empty($zoneDistIds)) {
                    $q->orWhereIn('district_id', $zoneDistIds)
                      ->orWhereIn('dist_id', $zoneDistIds);
                }
                if (!empty($zoneDistNames)) {
                    $q->orWhereIn('district_name', $zoneDistNames)
                      ->orWhereIn('dist_name', $zoneDistNames);
                }
                if ($zoneName) {
                    $q->orWhere('zone_name', 'LIKE', "%{$zoneName}%");
                }
            });
        }

        $row = $flatsQuery->selectRaw('
            COUNT(*) as total_flats,
            COALESCE(SUM(CASE WHEN is_aproved_by_dtp = 1 THEN 1 ELSE 0 END), 0) as dtp_approved_flats,
            COALESCE(SUM(CASE WHEN is_aproved_by_dtp = 0 OR is_aproved_by_dtp IS NULL THEN 1 ELSE 0 END), 0) as dtp_pending_flats
        ')->first();

        // 4,211 Master Allotted Pool Telemetry
        $allottedQuery = DB::table('ews_allotted_8');
        if (!empty($zoneId)) {
            $zoneDistIds = DB::table('ews_districts')->where('zone_id', $zoneId)->pluck('id')->toArray();
            if (!empty($zoneDistIds)) {
                $allottedQuery->whereIn('dist_id', $zoneDistIds);
            }
        }

        $totalAllotted = (clone $allottedQuery)->count();
        $possessionGiven = (clone $allottedQuery)->where(function ($q) {
            $q->where('is_possession_given', 1)
              ->orWhere('possession_status', 'Possession Given')
              ->orWhere('possession_status', 'GIVEN');
        })->count();
        $possessionPending = max(0, $totalAllotted - $possessionGiven);

        return [
            'total_flats' => (int) ($row->total_flats ?? 0),
            'dtp_approved_flats' => (int) ($row->dtp_approved_flats ?? 0),
            'dtp_pending_flats' => (int) ($row->dtp_pending_flats ?? 0),
            'total_allotted' => (int) $totalAllotted,
            'possession_given' => (int) $possessionGiven,
            'possession_pending' => (int) $possessionPending,
            'state_allotted_total' => (int) DB::table('ews_allotted_8')->count(),
        ];
    }

    /**
     * Get distinct licenses list across flats and projects.
     */
    public function getMatrixLicensesList($zoneId = null, $districtId = null, $townId = null, $projectId = null)
    {
        $flatQuery = DB::table('ews_builder_flats')
            ->whereNotNull('license_no')
            ->where('license_no', '!=', '');

        if ($zoneId) {
            $zoneDistIds = DB::table('ews_districts')->where('zone_id', $zoneId)->pluck('id')->toArray();
            $zoneDistNames = DB::table('ews_districts')->where('zone_id', $zoneId)->pluck('name')->toArray();
            $zoneModel = DB::table('ews_stp_districts')->where('id', $zoneId)->first();
            $zoneName = $zoneModel ? $zoneModel->name : null;

            $flatQuery->where(function($q) use ($zoneId, $zoneDistIds, $zoneDistNames, $zoneName) {
                $q->where('zone_id', $zoneId);
                if (!empty($zoneDistIds)) {
                    $q->orWhereIn('district_id', $zoneDistIds)
                      ->orWhereIn('dist_id', $zoneDistIds);
                }
                if (!empty($zoneDistNames)) {
                    $q->orWhereIn('district_name', $zoneDistNames)
                      ->orWhereIn('dist_name', $zoneDistNames);
                }
                if ($zoneName) {
                    $q->orWhere('zone_name', 'LIKE', "%{$zoneName}%");
                }
            });
        }

        if ($districtId) $flatQuery->where('district_id', $districtId);
        if ($townId) $flatQuery->where('town_id', $townId);
        if ($projectId) $flatQuery->where('project_id', $projectId);

        $flatLics = $flatQuery->distinct()->pluck('license_no')->toArray();

        $projQuery = DB::table('ews_projects')
            ->whereNotNull('license_no')
            ->where('license_no', '!=', '');

        if ($zoneId) {
            $zoneDistIds = DB::table('ews_districts')->where('zone_id', $zoneId)->pluck('id')->toArray();
            $projQuery->where(function($q) use ($zoneId, $zoneDistIds) {
                $q->where('zone_id', $zoneId);
                if (!empty($zoneDistIds)) {
                    $q->orWhereIn('district_id', $zoneDistIds);
                }
            });
        }

        if ($districtId) $projQuery->where('district_id', $districtId);
        if ($townId) $projQuery->where('town_id', $townId);
        if ($projectId) $projQuery->where('id', $projectId);

        $projLics = $projQuery->distinct()->pluck('license_no')->toArray();

        $allLics = array_values(array_unique(array_filter(array_merge($flatLics, $projLics))));
        sort($allLics);

        return $allLics;
    }

    /**
     * Get filtered Query for Offered Flats Matrix.
     */
    public function getFilteredMatrixQuery(Request $request)
    {
        $query = EwsBuilderFlat::query();

        // 1. Zone Filter (Include all flats matching zone_id OR any of its constituent districts)
        if ($request->filled('zone_id')) {
            $zoneId = $request->zone_id;
            $zoneDistIds = DB::table('ews_districts')->where('zone_id', $zoneId)->pluck('id')->toArray();
            $zoneDistNames = DB::table('ews_districts')->where('zone_id', $zoneId)->pluck('name')->toArray();
            $zoneModel = DB::table('ews_stp_districts')->where('id', $zoneId)->first();
            $zoneName = $zoneModel ? $zoneModel->name : null;

            $query->where(function ($q) use ($zoneId, $zoneDistIds, $zoneDistNames, $zoneName) {
                $q->where('zone_id', $zoneId);
                if (!empty($zoneDistIds)) {
                    $q->orWhereIn('district_id', $zoneDistIds)
                      ->orWhereIn('dist_id', $zoneDistIds);
                }
                if (!empty($zoneDistNames)) {
                    $q->orWhereIn('district_name', $zoneDistNames)
                      ->orWhereIn('dist_name', $zoneDistNames);
                }
                if ($zoneName) {
                    $q->orWhere('zone_name', 'LIKE', "%{$zoneName}%");
                }
            });
        }

        // 2. District Filter (Direct indexed lookup or fallback to name/dist_id)
        if ($request->filled('district_id')) {
            $distId = $request->district_id;
            $distName = DB::table('ews_districts')->where('id', $distId)->value('name');
            $query->where(function ($q) use ($distId, $distName) {
                $q->where('district_id', $distId)
                  ->orWhere('dist_id', $distId);
                if ($distName) {
                    $q->orWhere('district_name', $distName)
                      ->orWhere('dist_name', $distName);
                }
            });
        }

        // 3. Town Filter (Direct indexed lookup)
        if ($request->filled('town_id')) {
            $query->where('town_id', $request->town_id);
        }

        // 4. Project Filter (Direct indexed lookup)
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // 5. License No Filter (Direct indexed lookup)
        if ($request->filled('license_no')) {
            $query->where('license_no', trim($request->license_no));
        }

        // 6. DTP Status Filter (High-speed composite index)
        if ($request->input('ownership_scope') === 'dtp_approved' || $request->input('dtp_filter') === 'approved') {
            $query->where('is_aproved_by_dtp', 1);
        } elseif ($request->input('ownership_scope') === 'dtp_pending' || $request->input('dtp_filter') === 'pending') {
            $query->where(function($q) {
                $q->where('is_aproved_by_dtp', 0)->orWhereNull('is_aproved_by_dtp');
            });
        }

        // 7. Global Search Filter
        $searchValue = '';
        if ($request->has('search')) {
            $searchParam = $request->search;
            $searchValue = trim(is_array($searchParam) ? ($searchParam['value'] ?? '') : $searchParam);
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

    /**
     * Build DataTable response for read-only Matrix (TCP, STP, and Department).
     */
    public function buildMatrixDataTable(Request $request)
    {
        $ownershipScope = $request->input('ownership_scope');

        // Check if viewing Allotted Pool (from ews_allotted_8)
        if (in_array($ownershipScope, ['allotted', 'possession_given', 'possession_pending'])) {
            $query = DB::table('ews_allotted_8');
            if ($request->filled('zone_id')) {
                $zoneDistIds = DB::table('ews_districts')->where('zone_id', $request->zone_id)->pluck('id')->toArray();
                if (!empty($zoneDistIds)) {
                    $query->whereIn('dist_id', $zoneDistIds);
                }
            }
            if ($request->filled('district_id')) {
                $query->where('dist_id', $request->district_id);
            }

            if ($ownershipScope === 'possession_given') {
                $query->where(function ($q) {
                    $q->where('is_possession_given', 1)
                      ->orWhere('possession_status', 'Possession Given')
                      ->orWhere('possession_status', 'GIVEN');
                });
            } elseif ($ownershipScope === 'possession_pending') {
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
                ->smart(false)
                ->addIndexColumn()
                ->addColumn('district_name', fn($r) => '<span class="font-bold text-slate-900 uppercase">' . e($r->dist_name) . '</span>')
                ->addColumn('town_name', fn($r) => '<span class="text-slate-400 font-mono text-[10px]">State Allotted Pool</span>')
                ->addColumn('project_name', function($r) {
                    $parts = explode('-', $r->flat_no);
                    return count($parts) > 1 ? '<span class="text-slate-700 font-bold">' . e($parts[1]) . '</span>' : '<span class="text-slate-600 font-bold">EWS Flat</span>';
                })
                ->addColumn('license_no', fn($r) => '<span class="text-slate-400 font-mono text-[9px] italic">Pool Allotment</span>')
                ->addColumn('block_tower_number', fn($r) => '<span class="text-indigo-600 font-mono font-bold">App #' . e($r->application_number) . '</span>')
                ->addColumn('floor', fn($r) => '<span class="font-bold text-slate-800">' . e($r->full_name) . '</span>')
                ->addColumn('flat_number', fn($r) => '<span class="text-violet-700 font-black font-mono">' . e($r->flat_no) . '</span>')
                ->addColumn('flat_code', fn($r) => '<span class="text-emerald-700 font-bold font-mono">' . ($r->mobile_number ? substr($r->mobile_number, 0, 2) . '******' . substr($r->mobile_number, -2) : 'N/A') . '</span>')
                ->addColumn('dtp_status', function($r) {
                    $isGiven = ($r->is_possession_given == 1 || $r->possession_status === 'Possession Given' || $r->possession_status === 'GIVEN');
                    $status = $isGiven ? 'Possession Given' : ($r->possession_status ?: 'Pending Possession');
                    $color = $isGiven ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200';
                    $icon = $isGiven ? 'bi-check2-circle' : 'bi-hourglass-split';
                    return '<span class="px-2 py-0.5 rounded text-[9px] font-black uppercase ' . $color . ' border inline-flex items-center gap-1"><i class="bi ' . $icon . ' text-[9px]"></i> ' . e($status) . '</span>';
                })
                ->addColumn('actions', fn($r) => '<span class="text-slate-400 font-mono text-[9px] italic">Read-only Record</span>')
                ->rawColumns(['district_name', 'town_name', 'project_name', 'license_no', 'block_tower_number', 'floor', 'flat_number', 'flat_code', 'dtp_status', 'actions'])
                ->make(true);
        }

        // Fast column projection (avoids pulling unneeded heavy columns)
        $query = $this->getFilteredMatrixQuery($request)
            ->select([
                'id',
                'secure_id',
                'zone_id',
                'district_id',
                'district_name',
                'town_name',
                'project_name',
                'license_no',
                'block_tower_number',
                'floor',
                'flat_number',
                'flat_code',
                'is_aproved_by_dtp',
            ]);

        return DataTables::of($query)
            ->smart(false)
            ->addIndexColumn()
            ->addColumn('district_name', fn($r) => '<span class="font-bold text-slate-900 uppercase">' . e($r->district_name) . '</span>')
            ->addColumn('town_name', fn($r) => '<span class="text-slate-700 font-semibold uppercase">' . e($r->town_name ?? '—') . '</span>')
            ->addColumn('project_name', fn($r) => '<span class="text-slate-800 font-bold uppercase">' . e($r->project_name ?? '—') . '</span>')
            ->addColumn('license_no', function ($r) {
                if (!empty($r->license_no)) {
                    return '<span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200 inline-flex items-center gap-1 shadow-2xs"><i class="bi bi-card-checklist text-sky-600"></i> ' . e($r->license_no) . '</span>';
                }
                return '<span class="text-slate-400 font-mono text-[9px] italic">N/A</span>';
            })
            ->addColumn('block_tower_number', fn($r) => '<span class="text-indigo-650 font-mono font-bold">' . e($r->block_tower_number ?? '—') . '</span>')
            ->addColumn('floor', fn($r) => '<span class="font-bold text-slate-800">' . e($r->floor ?? '—') . '</span>')
            ->addColumn('flat_number', fn($r) => '<span class="text-violet-655 font-black font-mono">' . e($r->flat_number ?? '—') . '</span>')
            ->addColumn('flat_code', fn($r) => '<span class="text-emerald-600 font-bold font-mono">' . e($r->flat_code ?? '—') . '</span>')
            ->addColumn('dtp_status', function ($r) {
                if ($r->is_aproved_by_dtp == 1) {
                    return '<span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded text-[9px] font-black uppercase inline-flex items-center gap-1 shadow-2xs" title="Approved by DTP"><i class="bi bi-patch-check-fill text-emerald-600"></i> Approved</span>';
                }
                return '<span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 border border-amber-300 rounded text-[9px] font-black uppercase inline-flex items-center gap-1 shadow-2xs"><i class="bi bi-hourglass-split text-amber-600"></i> Pending</span>';
            })
            ->addColumn('actions', function ($r) {
                $flatId = $r->id;
                $viewUrl = route('ews.tcp.flats.show', $flatId);
                $isStp = request()->routeIs('ews.stp.*') || (Auth::check() && in_array(Auth::user()->role, ['ews_stp', 'stp']));

                if (request()->routeIs('ews.department.*')) {
                    $viewUrl = route('ews.department.builder-flats.show', $flatId);
                } elseif ($isStp) {
                    $viewUrl = route('ews.stp.flats.show', $flatId);
                    $editUrl = route('ews.stp.flats.edit', $flatId);
                    $isApproved = (int)$r->is_aproved_by_dtp === 1;

                    $editBtn = $isApproved
                        ? '<span class="px-2 py-0.5 bg-slate-100 text-slate-400 border border-slate-200 rounded text-[9px] font-bold uppercase inline-flex items-center gap-1 shadow-2xs cursor-not-allowed" title="Locked: Flat has been approved and cannot be edited"><i class="bi bi-lock-fill text-[9px]"></i> Locked</span>'
                        : '<a href="'.$editUrl.'" class="px-2 py-0.5 bg-amber-50 hover:bg-amber-500 text-amber-700 hover:text-white border border-amber-300 hover:border-amber-500 rounded text-[9px] font-black uppercase tracking-wider transition-all inline-flex items-center gap-1 shadow-2xs whitespace-nowrap"><i class="bi bi-pencil-square text-[9px]"></i> <span>Edit</span></a>';

                    return '
                        <div class="inline-flex items-center gap-1">
                            <a href="'.$viewUrl.'" class="px-2 py-0.5 bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white border border-sky-200 hover:border-sky-600 rounded text-[9px] font-black uppercase tracking-wider transition-all inline-flex items-center gap-1 shadow-2xs whitespace-nowrap">
                                <i class="bi bi-eye text-[9px]"></i> <span>View</span>
                            </a>
                            '.$editBtn.'
                        </div>
                    ';
                }

                return '
                    <a href="'.$viewUrl.'"
                        class="px-2 py-0.5 bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white border border-sky-200 hover:border-sky-600 rounded text-[9px] font-black uppercase tracking-wider transition-all inline-flex items-center gap-1 shadow-2xs whitespace-nowrap">
                        <i class="bi bi-eye text-[9px]"></i>
                        <span>View</span>
                    </a>
                ';
            })
            ->rawColumns(['district_name', 'town_name', 'project_name', 'license_no', 'block_tower_number', 'floor', 'flat_number', 'flat_code', 'dtp_status', 'actions'])
            ->make(true);
    }

    /**
     * Fetch complete bio-data of a single flat for the read-only modal.
     */
    public function getFlatBioDataPayload($secureId)
    {
        $flat = EwsBuilderFlat::where('secure_id', $secureId)
            ->orWhere('id', $secureId)
            ->first();

        if (!$flat && is_numeric(EwsHelper::decodeSecureId($secureId))) {
            $flat = EwsBuilderFlat::find(EwsHelper::decodeSecureId($secureId));
        }

        if (!$flat) {
            return response()->json(['success' => false, 'message' => 'Flat record not found.'], 404);
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
            $creator = User::find($flat->created_by);
        }

        $buildingPlanPdfUrl = $flat->building_plan_copy_pdf ? asset($flat->building_plan_copy_pdf) : null;
        $ocPdfUrl = $flat->copy_attached_oc_pdf ? asset($flat->copy_attached_oc_pdf) : null;
        $developerPlanPdfUrl = ($project && $project->developer_plan_pdf) ? asset($project->developer_plan_pdf) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $flat->id,
                'secure_id' => $flat->secure_id,
                'flat_number' => $flat->flat_number,
                'floor' => $flat->floor,
                'block_tower_number' => $flat->block_tower_number,
                'flat_code' => $flat->flat_code,
                'district_name' => $flat->district_name,
                'town_name' => $flat->town_name,
                'project_name' => $flat->project_name,
                'license_no' => $flat->license_no,
                'sector' => $project->sector ?? $flat->sector ?? '—',
                'colonizer_name' => $project->colonizer_name ?? $creator->name ?? '—',
                'total_project_area' => $project->total_project_area ?? '—',
                'is_approved_of_building_plan' => $flat->is_approved_of_building_plan,
                'date_of_approved_building_plan' => $flat->date_of_approved_building_plan,
                'building_plan_pdf_url' => $buildingPlanPdfUrl,
                'developer_plan_pdf_url' => $developerPlanPdfUrl,
                'status_of_oc' => $flat->status_of_oc,
                'oc_category' => $flat->oc_category,
                'date_of_oc' => $flat->date_of_oc,
                'oc_pdf_url' => $ocPdfUrl,
                'total_flats_in_project' => $flat->total_flats_in_project,
                'total_ews_flats_in_project' => $flat->total_ews_flats_in_project,
                'flats_offered_for_draw' => $flat->flats_offered_for_draw,
                'remaining_flats_for_oc' => $flat->remaining_flats_for_oc,
                'is_aproved_by_dtp' => (bool)$flat->is_aproved_by_dtp,
                'dtp_approved_at' => $flat->dtp_approved_at ? date('d M, Y h:i A', strtotime($flat->dtp_approved_at)) : null,
                'dtp_user_name' => $flat->dtp_user_name ?? 'Competent DTP Authority',
                'created_at' => $flat->created_at ? $flat->created_at->format('d M, Y h:i A') : '—',
                'created_by_name' => $creator->name ?? 'Authorized Officer',
                'created_by_role' => $creator->role ?? 'Officer',
            ]
        ]);
    }

    /**
     * Export Offered Flats to CSV.
     */
    public function exportMatrixCsvFile(Request $request, $prefix = 'ews_matrix')
    {
        $ownershipScope = $request->input('ownership_scope');
        if (in_array($ownershipScope, ['allotted', 'possession_given', 'possession_pending'])) {
            $query = DB::table('ews_allotted_8');
            if ($request->filled('zone_id')) {
                $zoneDistIds = DB::table('ews_districts')->where('zone_id', $request->zone_id)->pluck('id')->toArray();
                if (!empty($zoneDistIds)) {
                    $query->whereIn('dist_id', $zoneDistIds);
                }
            }
            if ($request->filled('district_id')) {
                $query->where('dist_id', $request->district_id);
            }
            if ($ownershipScope === 'possession_given') {
                $query->where(function ($q) {
                    $q->where('is_possession_given', 1)
                      ->orWhere('possession_status', 'Possession Given')
                      ->orWhere('possession_status', 'GIVEN');
                });
            } elseif ($ownershipScope === 'possession_pending') {
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
                'Content-Disposition' => 'attachment; filename="' . $prefix . '_allotted_' . date('Ymd_His') . '.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            return response()->stream(function () use ($allotted) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['S.No.', 'District Name', 'Application Number', 'Allottee Name', 'Mobile Number', 'Flat No.', 'Possession Status']);
                foreach ($allotted as $index => $row) {
                    $isGiven = ($row->is_possession_given == 1 || $row->possession_status === 'Possession Given' || $row->possession_status === 'GIVEN');
                    fputcsv($file, [
                        $index + 1,
                        $row->dist_name,
                        $row->application_number,
                        $row->full_name,
                        $row->mobile_number,
                        $row->flat_no,
                        $isGiven ? 'Possession Given' : ($row->possession_status ?: 'Pending Possession'),
                    ]);
                }
                fclose($file);
            }, 200, $headers);
        }

        $flats = $this->getFilteredMatrixQuery($request)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $prefix . '_flats_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($flats) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['S.No.', 'District Name', 'Town Name', 'Project Name', 'License No.', 'Block/Tower No.', 'Floor Details', 'Flat No.', 'Flat Code', 'STP Status', 'Registered At']);
            foreach ($flats as $index => $flat) {
                fputcsv($file, [
                    $index + 1,
                    $flat->district_name,
                    $flat->town_name ?? '—',
                    $flat->project_name ?? '—',
                    $flat->license_no ?? '—',
                    $flat->block_tower_number ?? '—',
                    $flat->floor ?? '—',
                    $flat->flat_number ?? '—',
                    $flat->flat_code ?? '—',
                    $flat->is_aproved_by_dtp ? 'APPROVED' : 'PENDING',
                    $flat->created_at ? $flat->created_at->format('Y-m-d H:i:s') : '—',
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    /**
     * Export Offered Flats to Excel.
     */
    public function exportMatrixExcelFile(Request $request, $prefix = 'ews_matrix')
    {
        return $this->exportMatrixCsvFile($request, $prefix);
    }

    /**
     * Export Offered Flats to PDF.
     */
    public function exportMatrixPdfFile(Request $request, $prefix = 'ews_matrix')
    {
        $flats = $this->getFilteredMatrixQuery($request)->get();
        $pdf = Pdf::loadView('ews.stp.pdf_report', compact('flats'));
        return $pdf->download($prefix . '_flats_' . date('Ymd_His') . '.pdf');
    }
}

