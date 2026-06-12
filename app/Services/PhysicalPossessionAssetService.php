<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PhysicalPossessionAssetService
{
    public function resolveFromPurchaserId(?int $privatePurchaserId): ?int
    {
        if (! $privatePurchaserId) {
            return null;
        }

        $assetId = DB::table('property_auction_detail')
            ->where('PurchaserID', $privatePurchaserId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderByDesc('CreatedDate')
            ->value('AssetId');

        return $assetId ? (int) $assetId : null;
    }
}
