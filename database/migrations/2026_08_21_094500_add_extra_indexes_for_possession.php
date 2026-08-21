<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $padIndexes = collect(Schema::getIndexes('property_auction_detail'))->pluck('name')->toArray();
        Schema::table('property_auction_detail', function (Blueprint $table) use ($padIndexes) {
            if (!in_array('pad_purchaser_id_idx', $padIndexes)) {
                $table->index('PurchaserID', 'pad_purchaser_id_idx');
            }
            if (!in_array('pad_asset_id_idx', $padIndexes)) {
                $table->index('AssetId', 'pad_asset_id_idx');
            }
        });

        $ppaIndexes = collect(Schema::getIndexes('physical_possession_applications'))->pluck('name')->toArray();
        Schema::table('physical_possession_applications', function (Blueprint $table) use ($ppaIndexes) {
            if (!in_array('ppa_private_purchaser_id_idx', $ppaIndexes)) {
                $table->index('private_purchaser_id', 'ppa_private_purchaser_id_idx');
            }
            if (!in_array('ppa_asset_id_idx', $ppaIndexes)) {
                $table->index('asset_id', 'ppa_asset_id_idx');
            }
            if (!in_array('ppa_district_id_idx', $ppaIndexes)) {
                $table->index('district_id', 'ppa_district_id_idx');
            }
        });

        $crdIndexes = collect(Schema::getIndexes('cash_receipt_details'))->pluck('name')->toArray();
        Schema::table('cash_receipt_details', function (Blueprint $table) use ($crdIndexes) {
            if (!in_array('crd_asset_number_idx', $crdIndexes)) {
                $table->index('asset_number', 'crd_asset_number_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_auction_detail', function (Blueprint $table) {
            $table->dropIndex('pad_purchaser_id_idx');
            $table->dropIndex('pad_asset_id_idx');
        });

        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dropIndex('ppa_private_purchaser_id_idx');
            $table->dropIndex('ppa_asset_id_idx');
            $table->dropIndex('ppa_district_id_idx');
        });

        Schema::table('cash_receipt_details', function (Blueprint $table) {
            $table->dropIndex('crd_asset_number_idx');
        });
    }
};
