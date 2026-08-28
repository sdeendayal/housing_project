<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            if (Schema::hasColumn('physical_possession_applications', 'created_by')) {
                try {
                    $table->dropForeign(['created_by']);
                } catch (\Exception $e) {}
            }
            if (Schema::hasColumn('physical_possession_applications', 'approved_by')) {
                try {
                    $table->dropForeign(['approved_by']);
                } catch (\Exception $e) {}
            }

            $columns = [
                'owner_id',
                'scheme',
                'property_auction_id',
                'mmsay_application_no',
                'ppp_id',
                'member_id',
                'branch_id',
                'city_id',
                'city_name',
                'sector_id',
                'sector_name',
                'flat_id',
                'asset_name',
                'asset_size',
                'asset_unit',
                'registration_details',
                'created_by',
                'approved_by',
                'approved_at'
            ];
            
            foreach ($columns as $col) {
                if (Schema::hasColumn('physical_possession_applications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('user_id');
            $table->string('scheme')->nullable()->default('MMSAY')->after('owner_id');
            $table->integer('property_auction_id')->nullable()->after('asset_id');
            $table->integer('mmsay_application_no')->nullable()->after('property_auction_id');
            $table->string('ppp_id', 50)->nullable()->after('mmsay_application_no');
            $table->string('member_id', 50)->nullable()->after('ppp_id');
            $table->integer('branch_id')->nullable()->after('district_name');
            $table->integer('city_id')->nullable()->after('branch_id');
            $table->string('city_name')->nullable()->after('city_id');
            $table->integer('sector_id')->nullable()->after('city_name');
            $table->string('sector_name')->nullable()->after('sector_id');
            $table->integer('flat_id')->nullable()->after('sector_name');
            $table->string('asset_name')->nullable()->after('flat_id');
            $table->integer('asset_size')->nullable()->after('asset_name');
            $table->string('asset_unit', 50)->nullable()->after('asset_size');
            $table->text('registration_details')->nullable()->after('address');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('visit_instructions');
            $table->unsignedBigInteger('approved_by')->nullable()->after('remarks');
            $table->dateTime('approved_at')->nullable()->after('approved_by');
            
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }
};
