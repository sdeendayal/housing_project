<?php

namespace App\Http\Controllers\Api\Stp;

use App\Http\Controllers\Controller;
use App\Models\EwsDistrict;
use App\Models\EwsTown;
use App\Models\EwsProject;
use App\Models\EwsBlock;
use App\Models\EwsBeneficiaryPossession;
use App\Models\EwsPossessionAuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StpApiController extends Controller
{
    /**
     * Get All Zones (for Zone Selection Dropdown)
     * GET /api/stp/zones
     */
    public function getZones(Request $request): JsonResponse
    {
        $zones = DB::table('ews_stp_districts')
            ->orderBy('id', 'asc')
            ->get(['id', 'name']);

        $formatted = $zones->map(function ($z) {
            $name = strtoupper(trim($z->name));
            $displayName = str_contains($name, 'ZONE') ? $name : $name . ' ZONE';
            $districtCount = DB::table('ews_districts')->where('zone_id', $z->id)->count();

            return [
                'id' => $z->id,
                'name' => $displayName,
                'total_districts' => $districtCount,
            ];
        });

        return response()->json([
            'success' => true,
            'total_zones' => $formatted->count(),
            'zones' => $formatted,
        ]);
    }

    /**
     * Get STP Zone Details and Districts (Assigned or by selected zone_id)
     * GET /api/stp/districts?zone_id=X or GET /api/stp/zone-districts
     */
    public function getZoneDistricts(Request $request): JsonResponse
    {
        $zoneId = $request->query('zone_id');

        if ($zoneId) {
            $zone = DB::table('ews_stp_districts')->where('id', $zoneId)->first();
            if (!$zone) {
                return response()->json([
                    'success' => false,
                    'message' => "Zone with ID {$zoneId} not found.",
                ], 404);
            }

            $cleanName = strtoupper(trim($zone->name));
            $zoneName = str_contains($cleanName, 'ZONE') ? $cleanName : $cleanName . ' ZONE';
            $districts = DB::table('ews_districts')
                ->where('zone_id', $zone->id)
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'zone' => [
                    'id' => $zone->id,
                    'name' => $zoneName,
                ],
                'total_districts' => $districts->count(),
                'districts' => $districts,
            ]);
        }

        $user = $request->user();
        $zoneData = $this->resolveStpZone($user);

        return response()->json([
            'success' => true,
            'zone' => [
                'id' => $zoneData['zone_id'],
                'name' => $zoneData['zone_name'],
            ],
            'total_districts' => count($zoneData['districts']),
            'districts' => $zoneData['districts'],
        ]);
    }

    /**
     * Get Towns for a selected District (or all towns in zone if district_id omitted)
     * GET /api/stp/towns?district_id=X
     */
    public function getTowns(Request $request): JsonResponse
    {
        $user = $request->user();
        $zoneData = $this->resolveStpZone($user);
        $zoneDistrictIds = array_column($zoneData['districts'], 'id');

        $districtId = $request->query('district_id');

        $query = EwsTown::query();

        if ($districtId) {
            $districtId = (int)$districtId;
            // Ensure the requested district belongs to this STP's zone
            if (!empty($zoneDistrictIds) && !in_array($districtId, $zoneDistrictIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: This district does not belong to your assigned zone.',
                ], 403);
            }
            $query->where('district_id', $districtId);
        } else {
            if (!empty($zoneDistrictIds)) {
                $query->whereIn('district_id', $zoneDistrictIds);
            }
        }

        $towns = $query->orderBy('name', 'asc')->get(['id', 'district_id', 'name', 'type']);

        return response()->json([
            'success' => true,
            'district_id' => $districtId ? (int)$districtId : null,
            'total_towns' => $towns->count(),
            'towns' => $towns,
        ]);
    }

    /**
     * Get Projects for a selected District & Town
     * GET /api/stp/projects?district_id=X&town_id=Y
     */
    public function getProjects(Request $request): JsonResponse
    {
        $districtId = $request->query('district_id');

        if (!$districtId) {
            return response()->json([
                'success' => false,
                'message' => 'district_id parameter is required.',
            ], 422);
        }

        // District-wise Project Master: Always returns all projects registered for this district
        $projects = EwsProject::where('district_id', $districtId)
            ->orderBy('name', 'asc')
            ->get(['id', 'district_id', 'town_id', 'town_name', 'name', 'project_abbr']);

        return response()->json([
            'success' => true,
            'district_id' => (int)$districtId,
            'total_projects' => $projects->count(),
            'projects' => $projects,
        ]);
    }

    /**
     * Get Blocks for a selected Project
     * GET /api/stp/blocks?project_id=X
     */
    public function getBlocks(Request $request): JsonResponse
    {
        $projectId = $request->query('project_id');

        if (!$projectId) {
            return response()->json([
                'success' => false,
                'message' => 'project_id parameter is required.',
            ], 422);
        }

        $blocks = EwsBlock::where('project_id', $projectId)
            ->orderBy('name', 'asc')
            ->get(['id', 'project_id', 'name']);

        return response()->json([
            'success' => true,
            'project_id' => (int)$projectId,
            'total_blocks' => $blocks->count(),
            'blocks' => $blocks,
        ]);
    }

    /**
     * Store a New Project via Mobile API
     * POST /api/stp/projects
     */
    public function storeProject(Request $request): JsonResponse
    {
        $request->validate([
            'district_id' => 'required|exists:ews_districts,id',
            'town_id' => 'nullable|exists:ews_towns,id',
            'name' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $zoneData = $this->resolveStpZone($user);
        $district = DB::table('ews_districts')->where('id', $request->district_id)->first();
        $town = $request->town_id ? EwsTown::find($request->town_id) : null;
        $cleanName = trim($request->name);

        $exists = EwsProject::where('district_id', $request->district_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($cleanName)])
            ->first();

        if ($exists) {
            return response()->json([
                'success' => true,
                'message' => "Project '{$exists->name}' already exists in this district.",
                'project' => [
                    'id' => $exists->id,
                    'district_id' => $exists->district_id,
                    'name' => $exists->name,
                ]
            ]);
        }

        $project = EwsProject::create([
            'zone_id' => $zoneData['zone_id'],
            'zone_name' => $zoneData['zone_name'],
            'district_id' => $district->id,
            'district_name' => $district->name,
            'town_id' => $town ? $town->id : null,
            'town_name' => $town ? $town->name : null,
            'name' => $cleanName,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Project '{$cleanName}' created successfully.",
            'project' => [
                'id' => $project->id,
                'district_id' => $project->district_id,
                'name' => $project->name,
            ]
        ], 201);
    }

    /**
     * Store a New Block/Tower via Mobile API
     * POST /api/stp/blocks
     */
    public function storeBlock(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|exists:ews_projects,id',
            'name' => 'required|string|max:255',
        ]);

        $projectId = (int)$request->project_id;
        $cleanName = trim($request->name);

        $exists = EwsBlock::where('project_id', $projectId)
            ->whereRaw('LOWER(name) = ?', [strtolower($cleanName)])
            ->first();

        if ($exists) {
            return response()->json([
                'success' => true,
                'message' => "Block/Tower '{$exists->name}' already exists under this project.",
                'block' => [
                    'id' => $exists->id,
                    'project_id' => $exists->project_id,
                    'name' => $exists->name,
                ]
            ]);
        }

        $block = EwsBlock::create([
            'project_id' => $projectId,
            'name' => $cleanName,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Block/Tower '{$cleanName}' created successfully.",
            'block' => [
                'id' => $block->id,
                'project_id' => $block->project_id,
                'name' => $block->name,
            ]
        ], 201);
    }

    /**
     * Helper to resolve Zone and its child Districts for STP User
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
        $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : (!empty($user->zone_name) ? strtoupper($user->zone_name) : 'ZONE');

        // Fetch districts mapped to this zone
        $districts = [];
        if ($zoneId) {
            $districts = DB::table('ews_districts')
                ->where('zone_id', $zoneId)
                ->orderBy('name', 'asc')
                ->get(['id', 'name'])
                ->toArray();
        } elseif (!empty($user->district_id)) {
            $districts = DB::table('ews_districts')
                ->where('id', $user->district_id)
                ->get(['id', 'name'])
                ->toArray();
        }

        return [
            'zone_id' => $zoneId,
            'zone_name' => $zoneName,
            'districts' => $districts,
        ];
    }

    /**
     * Get Allotted Beneficiaries for a Project (Card 8 Source)
     * GET /api/stp/beneficiaries?project_id=X
     * or GET /api/stp/projects/{id}/beneficiaries
     */
    public function getBeneficiaries(Request $request, $projectId = null): JsonResponse
    {
        $projectId = $projectId ?: $request->query('project_id');

        if (!$projectId) {
            return response()->json([
                'success' => false,
                'message' => 'project_id parameter is required.',
            ], 422);
        }

        $project = EwsProject::find($projectId);
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => "Project with ID {$projectId} not found.",
            ], 404);
        }

        $projectAbbr = $project->project_abbr;
        if (!$projectAbbr) {
            $projectAbbr = DB::table('ews_flat_abbreviations')
                ->where('project_name', $project->name)
                ->value('project_abbr');
        }

        if (!$projectAbbr) {
            return response()->json([
                'success' => false,
                'message' => "No abbreviation mapping found for project '{$project->name}'.",
            ], 404);
        }

        // Query ews_allotted_8 (Source table for Card 8 4,211 allotted beneficiaries)
        $query = DB::table('ews_allotted_8')
            ->where('flat_no', 'LIKE', "%-{$projectAbbr}-%");

        // Filter by block if requested
        $block = $request->query('block') ?: $request->query('block_name');
        if ($block) {
            $filterBlock = trim($block);
            $projectHasThisBlock = DB::table('ews_flat_abbreviations')
                ->where('project_abbr', $projectAbbr)
                ->where(function($q) use ($filterBlock) {
                    $q->where('block_tower', $filterBlock)
                      ->orWhere('block_abbr', $filterBlock);
                })
                ->exists();

            if ($projectHasThisBlock) {
                $query->where(function($q) use ($filterBlock) {
                    $q->where('flat_no', 'LIKE', "%-{$filterBlock}-%")
                      ->orWhereRaw('1 = 1');
                });
            } else {
                $query->where('flat_no', 'LIKE', "%-{$filterBlock}-%");
            }
        }

        // Filter by possession status (GIVEN / PENDING)
        $possessionFilter = $request->query('possession_status');
        if ($possessionFilter) {
            $status = strtoupper(trim($possessionFilter));
            if ($status === 'GIVEN') {
                $query->where(function ($q) {
                    $q->where('is_possession_given', 1)
                      ->orWhere('possession_status', 'GIVEN');
                });
            } elseif ($status === 'PENDING') {
                $query->where(function ($q) {
                    $q->where('is_possession_given', 0)
                      ->orWhereNull('is_possession_given');
                })->where(function ($q) {
                    $q->where('possession_status', '!=', 'GIVEN')
                      ->orWhereNull('possession_status');
                });
            }
        }

        // Filter by is_possession_given (1 / 0)
        $isPossessionGivenFilter = $request->query('is_possession_given');
        if ($isPossessionGivenFilter !== null && $isPossessionGivenFilter !== '') {
            if ((int)$isPossessionGivenFilter === 1) {
                $query->where(function ($q) {
                    $q->where('is_possession_given', 1)
                      ->orWhere('possession_status', 'GIVEN');
                });
            } else {
                $query->where(function ($q) {
                    $q->where('is_possession_given', 0)
                      ->orWhereNull('is_possession_given');
                })->where(function ($q) {
                    $q->where('possession_status', '!=', 'GIVEN')
                      ->orWhereNull('possession_status');
                });
            }
        }

        // Search query (application number, name, flat number, mobile)
        $search = trim($request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'LIKE', "%{$search}%")
                  ->orWhere('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('flat_no', 'LIKE', "%{$search}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$search}%")
                  ->orWhere('secure_id', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 50);
        $totalCount = (clone $query)->count();

        if (strtolower((string)$perPage) === 'all') {
            $beneficiaries = $query->orderBy('id', 'asc')->get();
            $pagination = [
                'total' => $totalCount,
                'per_page' => $totalCount,
                'current_page' => 1,
                'last_page' => 1,
            ];
        } else {
            $perPage = max(1, min(500, (int)$perPage));
            $paginated = $query->orderBy('id', 'asc')->paginate($perPage);
            $beneficiaries = $paginated->items();
            $pagination = [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ];
        }

        // Preload default project block if flat_no doesn't have an embedded block
        $defaultProjectBlock = DB::table('ews_flat_abbreviations')
            ->where('project_abbr', $projectAbbr)
            ->whereNotNull('block_tower')
            ->value('block_tower');

        // Format items matching mobile app list requirement
        $formatted = collect($beneficiaries)->map(function ($item, $index) use ($pagination, $defaultProjectBlock) {
            $sNo = (($pagination['current_page'] - 1) * $pagination['per_page']) + ($index + 1);
            
            // Parse flat number segments
            // Example formats: SNP-PIPD-4F-405, SNP-IRWO-2F-BO-207, SNP-PDPL-GF-05
            $flatParts = explode('-', $item->flat_no ?? '');
            $floor = $flatParts[2] ?? null;
            if (count($flatParts) >= 6) {
                $block = $flatParts[3] . ($flatParts[4] !== '' ? '-' . $flatParts[4] : '');
                $unit = $flatParts[5];
            } elseif (count($flatParts) == 5) {
                $block = $flatParts[3];
                $unit = $flatParts[4];
            } elseif (count($flatParts) == 4) {
                $block = $defaultProjectBlock ?? null;
                $unit = $flatParts[3];
            } else {
                $block = null;
                $unit = null;
            }

            $breakdown = [
                'town_code' => $flatParts[0] ?? null,
                'project_abbr' => $flatParts[1] ?? null,
                'floor' => $floor,
                'block' => $block,
                'flat_unit' => $unit,
            ];

            return [
                's_no' => $sNo,
                'id' => $item->id,
                'secure_id' => $item->secure_id ?? null,
                'application_number' => $item->application_number,
                'full_name' => $item->full_name,
                'district' => $item->dist_name,
                'mobile_number' => $item->mobile_number,
                'flat_number' => $item->flat_no,
                'flat_breakdown' => $breakdown,
                'status' => 'ALLOTTED',
                'is_possession_given' => (int)($item->is_possession_given ?? 0),
                'possession_status' => !empty($item->possession_status)
                    ? $item->possession_status
                    : ((int)($item->is_possession_given ?? 0) === 1 ? 'GIVEN' : 'PENDING'),
                'property_type' => $item->property_type ?? 'EWS Flat',
                'phase' => $item->phase ?? null,
            ];
        });

        // Compute project overall possession stats
        $baseProjectQuery = DB::table('ews_allotted_8')
            ->where('flat_no', 'LIKE', "%-{$projectAbbr}-%");
        if ($block) {
            $baseProjectQuery->where('flat_no', 'LIKE', "%-{$block}-%");
        }
        $projectTotalAllotted = (clone $baseProjectQuery)->count();
        $projectPossessionGiven = (clone $baseProjectQuery)->where(function ($q) {
            $q->where('is_possession_given', 1)->orWhere('possession_status', 'GIVEN');
        })->count();
        $projectPossessionPending = max(0, $projectTotalAllotted - $projectPossessionGiven);

        $stats = [
            'total_allotted' => $projectTotalAllotted,
            'possession_given' => $projectPossessionGiven,
            'possession_pending' => $projectPossessionPending,
        ];

        return response()->json([
            'success' => true,
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'project_abbr' => $projectAbbr,
                'district_name' => $project->district_name,
            ],
            'stats' => $stats,
            'total_allotted' => $projectTotalAllotted,
            'possession_given' => $projectPossessionGiven,
            'possession_pending' => $projectPossessionPending,
            'pagination' => $pagination,
            'beneficiaries' => $formatted,
        ]);
    }

    /**
     * Get Single Beneficiary Details
     * GET /api/stp/beneficiaries/{id}
     */
    public function getBeneficiaryDetails(Request $request, $secureId): JsonResponse
    {
        $beneficiary = DB::table('ews_allotted_8')
            ->where('secure_id', $secureId)
            ->first();

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficiary record not found. Access is strictly restricted to valid 32-digit Secure ID.',
            ], 404);
        }

        $flatParts = explode('-', $beneficiary->flat_no ?? '');
        $projectAbbr = $flatParts[1] ?? null;
        $project = null;
        if ($projectAbbr) {
            $project = EwsProject::where('project_abbr', $projectAbbr)->first();
        }
        $floor = $flatParts[2] ?? null;
        if (count($flatParts) >= 6) {
            $block = $flatParts[3] . ($flatParts[4] !== '' ? '-' . $flatParts[4] : '');
            $unit = $flatParts[5];
        } elseif (count($flatParts) == 5) {
            $block = $flatParts[3];
            $unit = $flatParts[4];
        } elseif (count($flatParts) == 4) {
            $block = DB::table('ews_flat_abbreviations')
                ->where('project_abbr', $projectAbbr)
                ->whereNotNull('block_tower')
                ->value('block_tower') ?? null;
            $unit = $flatParts[3];
        } else {
            $block = null;
            $unit = null;
        }

        $flatBreakdown = [
            'town_code' => $flatParts[0] ?? null,
            'project_abbr' => $projectAbbr,
            'floor' => $floor,
            'block' => $block,
            'flat_unit' => $unit,
        ];

        // Fetch current possession details if available
        $possession = EwsBeneficiaryPossession::where('beneficiary_id', $beneficiary->id)->first();

        // Fetch recent audit trail
        $auditLogs = EwsPossessionAuditLog::where('beneficiary_id', $beneficiary->id)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'beneficiary' => [
                'id' => $beneficiary->id,
                'secure_id' => $beneficiary->secure_id,
                'application_number' => $beneficiary->application_number,
                'full_name' => $beneficiary->full_name,
                'aadhar_no' => $beneficiary->aadhar_no ? (substr($beneficiary->aadhar_no, 0, 4) . 'XXXX' . substr($beneficiary->aadhar_no, -4)) : null,
                'mobile_number' => $beneficiary->mobile_number,
                'district' => $beneficiary->dist_name,
                'dist_id' => $beneficiary->dist_id,
                'flat_number' => $beneficiary->flat_no,
                'flat_breakdown' => $flatBreakdown,
                'project_name' => $project ? $project->name : null,
                'status' => 'ALLOTTED',
                'is_possession_given' => (int)($beneficiary->is_possession_given ?? 0),
                'possession_status' => !empty($beneficiary->possession_status)
                    ? $beneficiary->possession_status
                    : ((int)($beneficiary->is_possession_given ?? 0) === 1 ? 'GIVEN' : 'PENDING'),
                'possession_record' => $possession ? [
                    'id' => $possession->id,
                    'possession_status' => $possession->possession_status,
                    'possession_letter_url' => $possession->possession_letter_url,
                    'possession_letter_name' => $possession->possession_letter_original_name,
                    'beneficiary_flat_photo_url' => $possession->beneficiary_flat_photo_url,
                    'latitude' => $possession->latitude,
                    'longitude' => $possession->longitude,
                    'remarks' => $possession->remarks,
                    'possession_given_at' => $possession->possession_given_at ? $possession->possession_given_at->toDateTimeString() : null,
                    'updated_at' => $possession->updated_at ? $possession->updated_at->toDateTimeString() : null,
                ] : null,
                'possession_audit_logs' => $auditLogs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'action' => $log->action,
                        'old_status' => $log->old_status,
                        'new_status' => $log->new_status,
                        'performed_by' => $log->stp_user_name,
                        'latitude' => $log->latitude,
                        'longitude' => $log->longitude,
                        'created_at' => $log->created_at ? $log->created_at->toDateTimeString() : null,
                    ];
                }),
                'property_type' => $beneficiary->property_type,
                'phase' => $beneficiary->phase,
                'created_at' => $beneficiary->created_at,
            ],
        ]);
    }

    /**
     * Submit Physical Possession Form for Beneficiary
     * POST /api/stp/beneficiaries/{id}/possession
     */
    public function submitPossession(Request $request, $secureId): JsonResponse
    {
        $beneficiary = DB::table('ews_allotted_8')
            ->where('secure_id', $secureId)
            ->first();

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficiary record not found. Access is strictly restricted to valid 32-digit Secure ID.',
            ], 404);
        }

        $rawStatus = strtoupper(trim($request->input('possession_status', '')));
        if (!in_array($rawStatus, ['GIVEN', 'PENDING'])) {
            return response()->json([
                'success' => false,
                'message' => 'The possession_status field must be either GIVEN or PENDING.',
                'errors' => [
                    'possession_status' => ['The possession_status field must be either GIVEN or PENDING.'],
                ],
            ], 422);
        }

        $existingPossession = EwsBeneficiaryPossession::where('beneficiary_id', $beneficiary->id)->first();

        // Build validation rules
        $rules = [
            'possession_status' => 'required|in:GIVEN,PENDING',
        ];

        if ($rawStatus === 'GIVEN') {
            // As required: only PDF <= 500 KB, photo with beneficiary <= 500 KB, lat, long
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
            // For PENDING: pending reason / remarks is required
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
            'latitude.required' => 'Device GPS latitude coordinate is required.',
            'longitude.required' => 'Device GPS longitude coordinate is required.',
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

        $user = $request->user();

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

        $appVersion = $request->input('app_version', $request->header('X-App-Version'));
        $deviceInfo = $request->input('device_info', $request->header('X-Device-Info'));

        // Resolve Project & Administrative Hierarchy from Flat No Abbreviation
        $flatParts = explode('-', $beneficiary->flat_no ?? '');
        $projectAbbr = $flatParts[1] ?? null;
        $project = null;
        if ($projectAbbr) {
            $project = DB::table('ews_projects')->where('project_abbr', $projectAbbr)->first();
        }

        $projectId = $project ? $project->id : null;
        $projectName = $project ? $project->name : null;
        $distId = $beneficiary->dist_id ?: ($project ? $project->district_id : null);
        $districtName = $beneficiary->dist_name ?: ($project ? $project->district_name : null);
        $townId = $project ? $project->town_id : null;
        $townName = $project ? $project->town_name : null;
        $zoneId = $project ? $project->zone_id : null;
        $zoneName = $project ? $project->zone_name : null;

        try {
            $possession = DB::transaction(function () use (
                $beneficiary,
                $request,
                $user,
                $rawStatus,
                $letterPath,
                $letterOriginalName,
                $photoPath,
                $appVersion,
                $deviceInfo,
                $distId,
                $districtName,
                $projectId,
                $projectName,
                $townId,
                $townName,
                $zoneId,
                $zoneName
            ) {
                // 1. Fetch existing possession or instantiate new
                $possession = EwsBeneficiaryPossession::firstOrNew(['beneficiary_id' => $beneficiary->id]);
                $oldStatus = $possession->exists ? $possession->possession_status : ($beneficiary->possession_status ?? 'PENDING');

                $possession->beneficiary_id = $beneficiary->id;
                $possession->beneficiary_secure_id = $beneficiary->secure_id ?? null;
                $possession->application_number = $beneficiary->application_number;
                $possession->citizen_name = $beneficiary->full_name;
                $possession->citizen_mobile = $beneficiary->mobile_number;
                $possession->flat_no = $beneficiary->flat_no;
                $possession->zone_id = $zoneId;
                $possession->zone_name = $zoneName;
                $possession->dist_id = $distId;
                $possession->district_name = $districtName;
                $possession->town_id = $townId;
                $possession->town_name = $townName;
                $possession->project_id = $projectId;
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
                if ($appVersion) {
                    $possession->app_version = $appVersion;
                }
                if ($deviceInfo) {
                    $possession->device_info = $deviceInfo;
                }
                if ($rawStatus === 'GIVEN') {
                    $possession->possession_given_at = now();
                }
                $possession->save();

                // 2. Update ews_allotted_8 (Card 8 table)
                // 0 by default, 1 when possession given
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

                // 3. Create immutable audit log entry
                EwsPossessionAuditLog::create([
                    'possession_id' => $possession->id,
                    'beneficiary_id' => $beneficiary->id,
                    'application_number' => $beneficiary->application_number,
                    'action' => 'POSSESSION_' . $rawStatus,
                    'old_status' => $oldStatus,
                    'new_status' => $rawStatus,
                    'stp_user_id' => $user->id,
                    'stp_user_name' => $user->name,
                    'latitude' => $request->latitude ? (string)$request->latitude : null,
                    'longitude' => $request->longitude ? (string)$request->longitude : null,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'app_version' => $appVersion,
                    'device_info' => $deviceInfo,
                    'payload_snapshot' => [
                        'status' => $rawStatus,
                        'remarks' => $request->remarks,
                        'has_possession_letter' => (bool)$letterPath,
                        'has_flat_photo' => (bool)$photoPath,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'app_version' => $appVersion,
                        'device_info' => $deviceInfo,
                        'submitted_at' => now()->toIso8601String(),
                    ],
                ]);

                return $possession;
            });

            return response()->json([
                'success' => true,
                'message' => $rawStatus === 'GIVEN' 
                    ? 'Physical possession has been recorded as GIVEN successfully.' 
                    : 'Possession status has been recorded as PENDING.',
                'possession' => [
                    'id' => $possession->id,
                    'beneficiary_id' => $beneficiary->id,
                    'application_number' => $beneficiary->application_number,
                    'possession_status' => $possession->possession_status,
                    'is_possession_given' => ($possession->possession_status === 'GIVEN') ? 1 : 0,
                    'possession_letter_url' => $possession->possession_letter_url,
                    'possession_letter_name' => $possession->possession_letter_original_name,
                    'beneficiary_flat_photo_url' => $possession->beneficiary_flat_photo_url,
                    'latitude' => $possession->latitude,
                    'longitude' => $possession->longitude,
                    'remarks' => $possession->remarks,
                    'possession_given_at' => $possession->possession_given_at ? $possession->possession_given_at->toDateTimeString() : null,
                    'updated_at' => $possession->updated_at ? $possession->updated_at->toDateTimeString() : null,
                ]
            ], 200);

        } catch (\Throwable $e) {
            // Clean up uploaded files if transaction failed
            if ($letterPath && Storage::disk('public')->exists($letterPath)) {
                Storage::disk('public')->delete($letterPath);
            }
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to save possession record: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Possession Record & Audit History for a Beneficiary
     * GET /api/stp/beneficiaries/{id}/possession
     */
    public function getPossessionDetails(Request $request, $secureId): JsonResponse
    {
        $beneficiary = DB::table('ews_allotted_8')
            ->where('secure_id', $secureId)
            ->first();

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficiary record not found. Access is strictly restricted to valid 32-digit Secure ID.',
            ], 404);
        }

        $possession = EwsBeneficiaryPossession::where('beneficiary_id', $beneficiary->id)->first();
        $auditLogs = EwsPossessionAuditLog::where('beneficiary_id', $beneficiary->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'beneficiary_id' => $beneficiary->id,
            'secure_id' => $beneficiary->secure_id ?? null,
            'application_number' => $beneficiary->application_number,
            'flat_no' => $beneficiary->flat_no,
            'is_possession_given' => (int)($beneficiary->is_possession_given ?? 0),
            'possession_status' => !empty($beneficiary->possession_status)
                ? $beneficiary->possession_status
                : ((int)($beneficiary->is_possession_given ?? 0) === 1 ? 'GIVEN' : 'PENDING'),
            'possession' => $possession ? [
                'id' => $possession->id,
                'possession_status' => $possession->possession_status,
                'possession_letter_url' => $possession->possession_letter_url,
                'possession_letter_name' => $possession->possession_letter_original_name,
                'beneficiary_flat_photo_url' => $possession->beneficiary_flat_photo_url,
                'latitude' => $possession->latitude,
                'longitude' => $possession->longitude,
                'remarks' => $possession->remarks,
                'possession_given_at' => $possession->possession_given_at ? $possession->possession_given_at->toDateTimeString() : null,
                'updated_at' => $possession->updated_at ? $possession->updated_at->toDateTimeString() : null,
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
                    'timestamp' => $log->created_at ? $log->created_at->toDateTimeString() : null,
                ];
            }),
        ]);
    }
}
