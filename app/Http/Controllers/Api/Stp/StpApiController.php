<?php

namespace App\Http\Controllers\Api\Stp;

use App\Http\Controllers\Controller;
use App\Models\EwsDistrict;
use App\Models\EwsTown;
use App\Models\EwsProject;
use App\Models\EwsBlock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StpApiController extends Controller
{
    /**
     * Get STP Zone Details and Assigned Districts
     * GET /api/stp/districts or GET /api/stp/zone-districts
     */
    public function getZoneDistricts(Request $request): JsonResponse
    {
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
        $townId = $request->query('town_id');

        if (!$districtId) {
            return response()->json([
                'success' => false,
                'message' => 'district_id parameter is required.',
            ], 422);
        }

        $query = EwsProject::where('district_id', $districtId);
        if ($townId) {
            $query->where('town_id', $townId);
        }

        $projects = $query->orderBy('name', 'asc')->get(['id', 'district_id', 'town_id', 'name']);

        return response()->json([
            'success' => true,
            'district_id' => (int)$districtId,
            'town_id' => $townId ? (int)$townId : null,
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
}
