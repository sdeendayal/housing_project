<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add secure_id to ownermaster table
        Schema::table('ownermaster', function (Blueprint $table) {
            $table->string('secure_id', 32)->nullable()->after('OwnerId')->index('om_secure_id_idx');
        });

        // 2. Generate random 32-character MD5 hashes for all existing records
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $owners = DB::table('ownermaster')->whereNull('secure_id')->orWhere('secure_id', '')->get();
            foreach ($owners as $owner) {
                DB::table('ownermaster')->where('OwnerId', $owner->OwnerId)->update([
                    'secure_id' => md5($owner->OwnerId . uniqid(rand(), true))
                ]);
            }
        } else {
            DB::statement("UPDATE ownermaster SET secure_id = MD5(CONCAT(OwnerId, RAND(), UUID())) WHERE secure_id IS NULL OR secure_id = ''");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ownermaster', function (Blueprint $table) {
            $table->dropIndex('om_secure_id_idx');
            $table->dropColumn('secure_id');
        });
    }
};
