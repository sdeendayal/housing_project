<?php

namespace App\Http\Controllers;

use App\Models\EwsDistrict;
use App\Models\EwsTown;
use App\Models\EwsProject;
use App\Models\EwsBlock;
use App\Models\EwsBeneficiaryPossession;
use App\Models\EwsPossessionAuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class EwsStpPossessionWebController extends Controller
{
    /**
     * Resolve Assigned Zone and Districts for logged-in STP user (exact same as API)
     */
    private function resolveStpZone($user): array
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

        $zoneId = $zone ? $zone->id : ($user->zone_id ?? null);
        $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : (!empty($user->zone_name) ? strtoupper($user->zone_name) : 'ROHTAK ZONE');

        // Fetch ONLY districts belonging to this assigned zone
        $districts = collect();
        if ($zoneId) {
            $districts = DB::table('ews_districts')
                ->where('zone_id', $zoneId)
                ->orderBy('name', 'asc')
                ->get();
        } elseif (!empty($user->district_id)) {
            $districts = DB::table('ews_districts')
                ->where('id', $user->district_id)
                ->get();
        }

        return [
            'zone_id' => $zoneId,
            'zone_name' => $zoneName,
            'districts' => $districts,
        ];
    }

    /**
     * Main Possession Management Web View
    /**
     * Helper to collect project abbreviations based on zone, district, or project filters
     */
    private function getFilterProjectAbbrs($zoneDistricts, $districtId = null, $projectId = null): array
    {
        if (!empty($projectId)) {
            $project = EwsProject::find($projectId);
            if (!$project) return [];
            $abbr = $project->project_abbr ?: DB::table('ews_flat_abbreviations')->where('project_name', $project->name)->value('project_abbr');
            return $abbr ? [$abbr] : [];
        }

        if (!empty($districtId)) {
            $projects = EwsProject::where('district_id', $districtId)->get();
        } else {
            $districtIds = collect($zoneDistricts)->pluck('id')->toArray();
            $projects = EwsProject::whereIn('district_id', $districtIds)->get();
        }

        $abbrs = [];
        foreach ($projects as $p) {
            $abbr = $p->project_abbr ?: DB::table('ews_flat_abbreviations')->where('project_name', $p->name)->value('project_abbr');
            if ($abbr) {
                $abbrs[] = $abbr;
            }
        }
        return array_values(array_unique($abbrs));
    }

    /**
     * Main Possession Management Web View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403, 'Unauthorized access to STP Possession module.');
        }

        // Resolve logged-in user's assigned zone & districts
        $zoneData = $this->resolveStpZone($user);
        $assignedZoneId = $zoneData['zone_id'];
        $displayZoneName = $zoneData['zone_name'];
        $districts = $zoneData['districts'];

        // Get all projects belonging to districts in this assigned zone
        $districtIds = $districts->pluck('id')->toArray();
        $projects = EwsProject::whereIn('district_id', $districtIds)
            ->orderBy('name', 'asc')
            ->get(['id', 'district_id', 'name', 'project_abbr']);
        foreach ($projects as $p) {
            if (!$p->project_abbr) {
                $p->project_abbr = DB::table('ews_flat_abbreviations')
                    ->where('project_name', $p->name)
                    ->value('project_abbr');
            }
        }

        // Calculate initial stats: ALL flats in this assigned zone!
        $abbrs = $this->getFilterProjectAbbrs($districts);
        $stats = [
            'total_allotted' => 0,
            'possession_given' => 0,
            'possession_pending' => 0,
        ];

        if (!empty($abbrs)) {
            $baseQuery = DB::table('ews_allotted_8')->where(function ($q) use ($abbrs) {
                foreach ($abbrs as $a) {
                    $q->orWhere('flat_no', 'LIKE', "%-{$a}-%");
                }
            });
            $stats['total_allotted'] = (clone $baseQuery)->count();
            $stats['possession_given'] = (clone $baseQuery)->where('is_possession_given', 1)->count();
            $stats['possession_pending'] = (clone $baseQuery)->where(function ($q) {
                $q->where('is_possession_given', 0)->orWhereNull('is_possession_given');
            })->count();
        }

        $scopeName = "All {$displayZoneName} Projects ({$projects->count()} Projects)";

        return view('ews.developer.possession', compact(
            'user',
            'assignedZoneId',
            'displayZoneName',
            'districts',
            'projects',
            'stats',
            'scopeName'
        ));
    }

    /**
     * AJAX: Get Projects for a selected District (or all projects in zone if no district specified)
     */
    public function getDistrictProjects(Request $request): JsonResponse
    {
        $user = Auth::user();
        $zoneData = $this->resolveStpZone($user);
        $districtId = $request->query('district_id');

        if ($districtId) {
            $projects = EwsProject::where('district_id', $districtId)
                ->orderBy('name', 'asc')
                ->get(['id', 'district_id', 'name', 'project_abbr']);
        } else {
            $districtIds = $zoneData['districts']->pluck('id')->toArray();
            $projects = EwsProject::whereIn('district_id', $districtIds)
                ->orderBy('name', 'asc')
                ->get(['id', 'district_id', 'name', 'project_abbr']);
        }

        foreach ($projects as $p) {
            if (!$p->project_abbr) {
                $p->project_abbr = DB::table('ews_flat_abbreviations')
                    ->where('project_name', $p->name)
                    ->value('project_abbr');
            }
        }

        return response()->json([
            'success' => true,
            'projects' => $projects
        ]);
    }

    /**
     * AJAX: Get Statistics and Info for selected scope (Zone, District, or specific Project)
     */
    public function getProjectStats(Request $request): JsonResponse
    {
        $user = Auth::user();
        $zoneData = $this->resolveStpZone($user);
        $districtId = $request->query('district_id');
        $projectId = $request->query('project_id');

        $abbrs = $this->getFilterProjectAbbrs($zoneData['districts'], $districtId, $projectId);

        $stats = [
            'total_allotted' => 0,
            'possession_given' => 0,
            'possession_pending' => 0,
        ];

        if (!empty($abbrs)) {
            $baseQuery = DB::table('ews_allotted_8')->where(function ($q) use ($abbrs) {
                foreach ($abbrs as $a) {
                    $q->orWhere('flat_no', 'LIKE', "%-{$a}-%");
                }
            });
            $stats['total_allotted'] = (clone $baseQuery)->count();
            $stats['possession_given'] = (clone $baseQuery)->where('is_possession_given', 1)->count();
            $stats['possession_pending'] = (clone $baseQuery)->where(function ($q) {
                $q->where('is_possession_given', 0)->orWhereNull('is_possession_given');
            })->count();
        }

        // Determine scope display name and project abbreviation
        $scopeName = "All " . $zoneData['zone_name'] . " Projects";
        $projectAbbr = null;
        if (!empty($projectId)) {
            $p = EwsProject::find($projectId);
            if ($p) {
                $projectAbbr = $abbrs[0] ?? null;
                $scopeName = $p->name . ($projectAbbr ? " [{$projectAbbr}]" : '');
            }
        } elseif (!empty($districtId)) {
            $d = DB::table('ews_districts')->find($districtId);
            if ($d) {
                $scopeName = "All Projects in " . strtoupper($d->name);
            }
        }

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'scope_name' => $scopeName,
            'project_abbr' => $projectAbbr,
        ]);
    }

    /**
     * DataTables AJAX Endpoint for Beneficiaries
     */
    public function getBeneficiariesData(Request $request)
    {
        $user = Auth::user();
        $zoneData = $this->resolveStpZone($user);
        $districtId = $request->query('district_id');
        $projectId = $request->query('project_id');

        $abbrs = $this->getFilterProjectAbbrs($zoneData['districts'], $districtId, $projectId);
        if (empty($abbrs)) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $query = DB::table('ews_allotted_8')
            ->where(function ($q) use ($abbrs) {
                foreach ($abbrs as $a) {
                    $q->orWhere('flat_no', 'LIKE', "%-{$a}-%");
                }
            });

        // Filter by block
        if ($request->filled('block')) {
            $query->where('flat_no', 'LIKE', "%-" . trim($request->block) . "-%");
        }

        // Filter by possession status
        if ($request->filled('possession_status')) {
            $status = strtoupper(trim($request->possession_status));
            if ($status === 'GIVEN') {
                $query->where('is_possession_given', 1);
            } elseif ($status === 'PENDING') {
                $query->where(function ($q) {
                    $q->where('is_possession_given', 0)->orWhereNull('is_possession_given');
                });
            }
        }

        // General search
        if ($request->filled('search_keyword')) {
            $search = trim($request->search_keyword);
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'LIKE', "%{$search}%")
                  ->orWhere('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('flat_no', 'LIKE', "%{$search}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('flat_breakdown', function ($row) {
                $parts = explode('-', $row->flat_no ?? '');
                $floor = $parts[2] ?? '-';
                if (count($parts) >= 6) {
                    $block = $parts[3] . ($parts[4] !== '' ? '-' . $parts[4] : '');
                    $unit = $parts[5];
                } elseif (count($parts) == 5) {
                    $block = $parts[3];
                    $unit = $parts[4];
                } elseif (count($parts) == 4) {
                    $block = '-';
                    $unit = $parts[3];
                } else {
                    $block = '-';
                    $unit = '-';
                }
                return [
                    'flat_no' => $row->flat_no,
                    'floor' => $floor,
                    'block' => $block,
                    'unit' => $unit,
                ];
            })
            ->addColumn('possession_badge', function ($row) {
                $isGiven = (int)($row->is_possession_given ?? 0);
                if ($isGiven === 1) {
                    return '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs whitespace-nowrap"><i class="bi bi-check-circle-fill text-emerald-500 text-[10px]"></i> Given</span>';
                }
                return '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 shadow-xs whitespace-nowrap"><i class="bi bi-hourglass-split text-amber-500 text-[10px]"></i> Pending</span>';
            })
            ->addColumn('action', function ($row) {
                $isGiven = (int)($row->is_possession_given ?? 0);
                $pageUrl = url("/ews/developer/possession/{$row->id}");
                if ($isGiven) {
                    $btn = '<a href="' . $pageUrl . '" title="View Possession Details" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-bold transition-all shadow-xs inline-flex items-center gap-1 whitespace-nowrap"><i class="bi bi-eye text-[10px]"></i> View</a>';
                } else {
                    $btn = '<a href="' . $pageUrl . '" title="Handover Physical Possession" class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded text-[11px] font-bold transition-all shadow-xs inline-flex items-center gap-1 whitespace-nowrap"><i class="bi bi-box-arrow-up-right text-[10px]"></i> Handover</a>';
                }
                return '<div class="inline-flex items-center gap-1 whitespace-nowrap">' . $btn . '</div>';
            })
            ->rawColumns(['possession_badge', 'action'])
            ->make(true);
    }

    /**
     * Show Dedicated Beneficiary Possession Handover Page
     */
    public function show($id)
    {
        $user = Auth::user();
        $zoneData = $this->resolveStpZone($user);

        $beneficiary = DB::table('ews_allotted_8')
            ->where('id', $id)
            ->first();

        if (!$beneficiary) {
            return redirect()->route('ews.developer.possession.index')
                ->with('error', 'Beneficiary record not found.');
        }

        // Project Info
        $flatParts = explode('-', $beneficiary->flat_no ?? '');
        $projectAbbr = $flatParts[1] ?? null;
        $project = null;
        if ($projectAbbr) {
            $project = EwsProject::where('project_abbr', $projectAbbr)->first();
        }

        // Flat Breakdown
        $floor = $flatParts[2] ?? '-';
        if (count($flatParts) >= 6) {
            $block = $flatParts[3] . ($flatParts[4] !== '' ? '-' . $flatParts[4] : '');
            $unit = $flatParts[5];
        } elseif (count($flatParts) == 5) {
            $block = $flatParts[3];
            $unit = $flatParts[4];
        } elseif (count($flatParts) == 4) {
            $block = '-';
            $unit = $flatParts[3];
        } else {
            $block = '-';
            $unit = '-';
        }

        $flatBreakdown = [
            'flat_no' => $beneficiary->flat_no,
            'floor' => $floor,
            'block' => $block,
            'unit' => $unit,
        ];

        $possession = EwsBeneficiaryPossession::where('beneficiary_id', $beneficiary->id)->first();
        $auditLogs = EwsPossessionAuditLog::where('beneficiary_id', $beneficiary->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('ews.developer.possession_detail', [
            'user' => $user,
            'zoneData' => $zoneData,
            'displayZoneName' => $zoneData['zone_name'] ?? 'Assigned Zone',
            'beneficiary' => $beneficiary,
            'project' => $project,
            'flatBreakdown' => $flatBreakdown,
            'possession' => $possession,
            'auditLogs' => $auditLogs,
        ]);
    }

    /**
     * Get Single Beneficiary Possession Details & Audit Trail (AJAX)
     */
    public function getBeneficiaryDetails(Request $request, $id): JsonResponse
    {
        $beneficiary = DB::table('ews_allotted_8')
            ->where('id', $id)
            ->first();

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficiary record not found.',
            ], 404);
        }

        $possession = EwsBeneficiaryPossession::where('beneficiary_id', $beneficiary->id)->first();
        $auditLogs = EwsPossessionAuditLog::where('beneficiary_id', $beneficiary->id)
            ->orderBy('id', 'desc')
            ->get();

        $flatParts = explode('-', $beneficiary->flat_no ?? '');
        $projectAbbr = $flatParts[1] ?? null;
        $project = null;
        if ($projectAbbr) {
            $project = EwsProject::where('project_abbr', $projectAbbr)->first();
        }

        return response()->json([
            'success' => true,
            'beneficiary' => [
                'id' => $beneficiary->id,
                'application_number' => $beneficiary->application_number,
                'full_name' => $beneficiary->full_name,
                'mobile_number' => $beneficiary->mobile_number,
                'district' => $beneficiary->dist_name,
                'flat_number' => $beneficiary->flat_no,
                'project_name' => $project ? $project->name : null,
                'is_possession_given' => (int)($beneficiary->is_possession_given ?? 0),
                'possession_status' => $beneficiary->possession_status ?: 'PENDING',
                'possession_given_at' => $beneficiary->possession_given_at ? date('d M Y, h:i A', strtotime($beneficiary->possession_given_at)) : null,
            ],
            'possession' => $possession ? [
                'id' => $possession->id,
                'possession_status' => $possession->possession_status,
                'possession_letter_url' => $possession->possession_letter_url,
                'possession_letter_name' => $possession->possession_letter_original_name,
                'beneficiary_flat_photo_url' => $possession->beneficiary_flat_photo_url,
                'latitude' => $possession->latitude,
                'longitude' => $possession->longitude,
                'remarks' => $possession->remarks,
                'possession_given_at' => $possession->possession_given_at ? $possession->possession_given_at->format('d M Y, h:i A') : null,
                'updated_at' => $possession->updated_at ? $possession->updated_at->format('d M Y, h:i A') : null,
            ] : null,
            'audit_history' => $auditLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'old_status' => $log->old_status,
                    'new_status' => $log->new_status,
                    'performed_by' => $log->stp_user_name,
                    'latitude' => $log->latitude,
                    'longitude' => $log->longitude,
                    'ip_address' => $log->ip_address,
                    'timestamp' => $log->created_at ? $log->created_at->format('d M Y, h:i A') : null,
                ];
            }),
        ]);
    }

    /**
     * Submit Physical Possession Form via Web UI
     */
    public function submitPossession(Request $request, $id): JsonResponse
    {
        $beneficiary = DB::table('ews_allotted_8')
            ->where('id', $id)
            ->first();

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficiary record not found.',
            ], 404);
        }

        $rawStatus = strtoupper(trim($request->input('possession_status', '')));
        if (!in_array($rawStatus, ['GIVEN', 'PENDING'])) {
            return response()->json([
                'success' => false,
                'message' => 'The possession_status field must be either GIVEN or PENDING.',
            ], 422);
        }

        $existingPossession = EwsBeneficiaryPossession::where('beneficiary_id', $beneficiary->id)->first();

        // Build validation rules
        $rules = [
            'possession_status' => 'required|in:GIVEN,PENDING',
        ];

        if ($rawStatus === 'GIVEN') {
            // If already uploaded, file can be optional during update
            $rules['possession_letter'] = ($existingPossession && $existingPossession->possession_letter_path) 
                ? 'nullable|file|mimes:pdf|max:500' 
                : 'required|file|mimes:pdf|max:500';

            $rules['beneficiary_flat_photo'] = ($existingPossession && $existingPossession->beneficiary_flat_photo_path)
                ? 'nullable|image|mimes:jpeg,jpg,png|max:500'
                : 'required|image|mimes:jpeg,jpg,png|max:500';

            $rules['latitude'] = 'required|numeric|between:-90,90';
            $rules['longitude'] = 'required|numeric|between:-180,180';
            $rules['remarks'] = 'nullable|string|max:5000';
        } else {
            $rules['remarks'] = 'required|string|max:5000';
            $rules['possession_letter'] = 'nullable|file|mimes:pdf|max:500';
            $rules['beneficiary_flat_photo'] = 'nullable|image|mimes:jpeg,jpg,png|max:500';
            $rules['latitude'] = 'nullable|numeric|between:-90,90';
            $rules['longitude'] = 'nullable|numeric|between:-180,180';
        }

        $messages = [
            'possession_letter.required' => 'Possession letter PDF document is required when possession is given.',
            'possession_letter.mimes' => 'The possession letter must be a valid PDF file.',
            'possession_letter.max' => 'The possession letter PDF file size must not exceed 500 KB.',
            'beneficiary_flat_photo.required' => 'Photo of beneficiary with flat is required when possession is given.',
            'beneficiary_flat_photo.image' => 'The flat photo must be an image file (jpeg, jpg, png).',
            'beneficiary_flat_photo.max' => 'The flat photo file size must not exceed 500 KB.',
            'latitude.required' => 'GPS latitude coordinate is required.',
            'longitude.required' => 'GPS longitude coordinate is required.',
            'remarks.required' => 'Please provide the pending reason / remarks.',
            'remarks.max' => 'Remarks cannot exceed 1000 words (5000 characters).',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        // Handle file uploads
        $letterPath = null;
        $letterOriginalName = null;
        if ($request->hasFile('possession_letter')) {
            $letterFile = $request->file('possession_letter');
            $letterOriginalName = $letterFile->getClientOriginalName();
            $letterFileName = 'possession_letter_' . $beneficiary->id . '_' . time() . '.' . $letterFile->getClientOriginalExtension();
            $letterPath = $letterFile->storeAs('ews_possession/' . $beneficiary->id, $letterFileName, 'public');
        }

        $photoPath = null;
        if ($request->hasFile('beneficiary_flat_photo')) {
            $photoFile = $request->file('beneficiary_flat_photo');
            $photoFileName = 'flat_photo_' . $beneficiary->id . '_' . time() . '.' . $photoFile->getClientOriginalExtension();
            $photoPath = $photoFile->storeAs('ews_possession/' . $beneficiary->id, $photoFileName, 'public');
        }

        // Resolve Project Name from Flat No Abbreviation
        $flatParts = explode('-', $beneficiary->flat_no ?? '');
        $projectAbbr = $flatParts[1] ?? null;
        $projectName = null;
        if ($projectAbbr) {
            $projectName = DB::table('ews_projects')->where('project_abbr', $projectAbbr)->value('name');
        }

        try {
            $possession = DB::transaction(function () use (
                $beneficiary,
                $request,
                $user,
                $rawStatus,
                $letterPath,
                $letterOriginalName,
                $photoPath,
                $projectName
            ) {
                $possession = EwsBeneficiaryPossession::firstOrNew(['beneficiary_id' => $beneficiary->id]);
                $oldStatus = $possession->exists ? $possession->possession_status : ($beneficiary->possession_status ?? 'PENDING');

                $possession->beneficiary_id = $beneficiary->id;
                $possession->application_number = $beneficiary->application_number;
                $possession->citizen_name = $beneficiary->full_name;
                $possession->citizen_mobile = $beneficiary->mobile_number;
                $possession->flat_no = $beneficiary->flat_no;
                $possession->district_name = $beneficiary->dist_name;
                $possession->project_name = $projectName;
                $possession->stp_user_id = $user->id;
                $possession->possession_status = $rawStatus;

                if ($letterPath) {
                    $possession->possession_letter_path = $letterPath;
                    $possession->possession_letter_original_name = $letterOriginalName;
                }
                if ($photoPath) {
                    $possession->beneficiary_flat_photo_path = $photoPath;
                }
                if ($request->filled('latitude')) {
                    $possession->latitude = $request->latitude;
                }
                if ($request->filled('longitude')) {
                    $possession->longitude = $request->longitude;
                }
                if ($request->filled('remarks')) {
                    $possession->remarks = trim($request->remarks);
                }
                $possession->app_version = 'Web Portal 2.4';
                $possession->device_info = $request->userAgent();

                if ($rawStatus === 'GIVEN') {
                    $possession->possession_given_at = now();
                }
                $possession->save();

                // Update ews_allotted_8
                $isGiven = ($rawStatus === 'GIVEN') ? 1 : 0;
                $allottedUpdate = [
                    'is_possession_given' => $isGiven,
                    'possession_status' => $rawStatus,
                    'updated_at' => now(),
                ];
                if ($rawStatus === 'GIVEN') {
                    $allottedUpdate['possession_given_at'] = now();
                }
                DB::table('ews_allotted_8')
                    ->where('id', $beneficiary->id)
                    ->update($allottedUpdate);

                // Create Audit Log
                EwsPossessionAuditLog::create([
                    'possession_id' => $possession->id,
                    'beneficiary_id' => $beneficiary->id,
                    'application_number' => $beneficiary->application_number,
                    'action' => 'WEB_POSSESSION_' . $rawStatus,
                    'old_status' => $oldStatus,
                    'new_status' => $rawStatus,
                    'stp_user_id' => $user->id,
                    'stp_user_name' => $user->name,
                    'latitude' => $request->latitude ? (string)$request->latitude : null,
                    'longitude' => $request->longitude ? (string)$request->longitude : null,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'app_version' => 'Web Portal 2.4',
                    'device_info' => 'Browser',
                    'payload_snapshot' => [
                        'status' => $rawStatus,
                        'remarks' => $request->remarks,
                        'has_possession_letter' => (bool)$letterPath,
                        'has_flat_photo' => (bool)$photoPath,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'source' => 'WEB_PORTAL',
                        'submitted_at' => now()->toIso8601String(),
                    ],
                ]);

                return $possession;
            });

            return response()->json([
                'success' => true,
                'message' => $rawStatus === 'GIVEN'
                    ? 'Physical possession recorded as GIVEN successfully.'
                    : 'Possession status updated as PENDING.',
                'possession' => [
                    'id' => $possession->id,
                    'beneficiary_id' => $beneficiary->id,
                    'possession_status' => $possession->possession_status,
                    'is_possession_given' => ($possession->possession_status === 'GIVEN') ? 1 : 0,
                    'possession_letter_url' => $possession->possession_letter_url,
                    'beneficiary_flat_photo_url' => $possession->beneficiary_flat_photo_url,
                    'latitude' => $possession->latitude,
                    'longitude' => $possession->longitude,
                ]
            ]);

        } catch (\Throwable $e) {
            if ($letterPath && Storage::disk('public')->exists($letterPath)) {
                Storage::disk('public')->delete($letterPath);
            }
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to save possession: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Audit Logs Dedicated View
     */
    public function auditLogs(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['ews_developer', 'ews_stp', 'stp'])) {
            abort(403, 'Unauthorized access.');
        }

        $zoneData = $this->resolveStpZone($user);
        $displayZoneName = $zoneData['zone_name'];

        $logs = EwsPossessionAuditLog::orderBy('id', 'desc')->paginate(30);

        return view('ews.developer.possession_logs', compact('user', 'displayZoneName', 'logs'));
    }
}
