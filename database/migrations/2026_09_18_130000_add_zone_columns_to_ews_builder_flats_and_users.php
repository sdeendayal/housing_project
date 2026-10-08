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
        // 1. Ensure SONIPAT exists in ews_stp_districts master table for existing user compatibility
        if (Schema::hasTable('ews_stp_districts')) {
            $sonipatExists = DB::table('ews_stp_districts')->where('name', 'SONIPAT')->exists();
            if (!$sonipatExists) {
                DB::table('ews_stp_districts')->insert([
                    'name' => 'SONIPAT',
                    'code' => 'SNP',
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Add zone_id and zone_name to ews_builder_flats
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_builder_flats', 'zone_id')) {
                    $table->unsignedBigInteger('zone_id')->nullable()->after('district_id');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'zone_name')) {
                    $table->string('zone_name')->nullable()->after('zone_id');
                }
            });
        }

        // 3. Add zone_id and zone_name to users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'zone_id')) {
                    $table->unsignedBigInteger('zone_id')->nullable()->after('district_id');
                }
                if (!Schema::hasColumn('users', 'zone_name')) {
                    $table->string('zone_name')->nullable()->after('zone_id');
                }
            });
        }

        // 4. Backfill zone_id and zone_name for existing users and builder flats
        $stpDistricts = DB::table('ews_stp_districts')->get()->keyBy(function($item) {
            return strtoupper(trim($item->name));
        });

        // Update users
        $users = DB::table('users')->whereIn('role', ['ews_stp', 'stp', 'ews_developer'])->get();
        foreach ($users as $u) {
            $cleanName = strtoupper(trim(str_replace(' ZONE', '', $u->district_name ?? '')));
            if ($cleanName && isset($stpDistricts[$cleanName])) {
                DB::table('users')->where('id', $u->id)->update([
                    'zone_id' => $stpDistricts[$cleanName]->id,
                    'zone_name' => $cleanName . ' ZONE',
                ]);
            }
        }

        // Update builder flats
        $flats = DB::table('ews_builder_flats')->get();
        foreach ($flats as $f) {
            $cleanDist = strtoupper(trim(str_replace(' ZONE', '', $f->district_name ?? '')));
            if ($cleanDist && isset($stpDistricts[$cleanDist])) {
                DB::table('ews_builder_flats')->where('id', $f->id)->update([
                    'zone_id' => $stpDistricts[$cleanDist]->id,
                    'zone_name' => $cleanDist . ' ZONE',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                if (Schema::hasColumn('ews_builder_flats', 'zone_name')) {
                    $table->dropColumn('zone_name');
                }
                if (Schema::hasColumn('ews_builder_flats', 'zone_id')) {
                    $table->dropColumn('zone_id');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'zone_name')) {
                    $table->dropColumn('zone_name');
                }
                if (Schema::hasColumn('users', 'zone_id')) {
                    $table->dropColumn('zone_id');
                }
            });
        }
    }
};
