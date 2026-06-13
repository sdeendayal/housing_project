<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_private_purchasers', function (Blueprint $table) {
            $table->index('MobileNo', 'ppp_mobile_no_index');
            $table->index(
                ['IsActive', 'IsDeleted', 'Is_UserLogin_Deleted', 'PrivatePurchaserId'],
                'ppp_active_sync_index'
            );
        });

        Schema::table('role_types', function (Blueprint $table) {
            $table->index('role_id', 'role_types_role_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('property_private_purchasers', function (Blueprint $table) {
            $table->dropIndex('ppp_mobile_no_index');
            $table->dropIndex('ppp_active_sync_index');
        });

        Schema::table('role_types', function (Blueprint $table) {
            $table->dropIndex('role_types_role_id_index');
        });
    }
};
