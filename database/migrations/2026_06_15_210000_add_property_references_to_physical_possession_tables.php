<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('physical_possession_applications', 'property_auction_id')) {
                $table->integer('property_auction_id')->nullable()->after('asset_id');
                $table->integer('mmsay_application_no')->nullable()->after('property_auction_id');
                $table->integer('branch_id')->nullable()->after('district_name');
                $table->integer('city_id')->nullable()->after('branch_id');
                $table->string('city_name')->nullable()->after('city_id');
                $table->integer('sector_id')->nullable()->after('city_name');
                $table->string('sector_name')->nullable()->after('sector_id');
                $table->integer('flat_id')->nullable()->after('sector_name');
                $table->string('asset_name')->nullable()->after('flat_id');
                $table->integer('asset_size')->nullable()->after('asset_name');
                $table->string('asset_unit', 50)->nullable()->after('asset_size');
                $table->decimal('flat_cost', 15, 2)->nullable()->after('asset_unit');
                $table->decimal('received_amount', 15, 2)->nullable()->after('flat_cost');
                $table->decimal('balance_amount', 15, 2)->nullable()->after('received_amount');
            }
        });

        Schema::table('physical_possession_documents', function (Blueprint $table) {
            if (! Schema::hasColumn('physical_possession_documents', 'private_purchaser_id')) {
                $table->integer('private_purchaser_id')->nullable()->after('user_id');
                $table->integer('property_auction_id')->nullable()->after('private_purchaser_id');
                $table->integer('mmsay_application_no')->nullable()->after('property_auction_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('physical_possession_documents', function (Blueprint $table) {
            if (Schema::hasColumn('physical_possession_documents', 'private_purchaser_id')) {
                $table->dropColumn(['private_purchaser_id', 'property_auction_id', 'mmsay_application_no']);
            }
        });

        Schema::table('physical_possession_applications', function (Blueprint $table) {
            if (Schema::hasColumn('physical_possession_applications', 'property_auction_id')) {
                $table->dropColumn([
                    'property_auction_id',
                    'mmsay_application_no',
                    'branch_id',
                    'city_id',
                    'city_name',
                    'sector_id',
                    'sector_name',
                    'flat_id',
                    'asset_name',
                    'asset_size',
                    'asset_unit',
                    'flat_cost',
                    'received_amount',
                    'balance_amount',
                ]);
            }
        });
    }
};
