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
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_builder_flats', 'register_flat')) {
                    $table->string('register_flat', 255)->nullable()->after('secure_id');
                }
            });
        }

        if (Schema::hasTable('ews_projects')) {
            Schema::table('ews_projects', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_projects', 'register_flat')) {
                    $table->string('register_flat', 255)->nullable()->after('name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                if (Schema::hasColumn('ews_builder_flats', 'register_flat')) {
                    $table->dropColumn('register_flat');
                }
            });
        }

        if (Schema::hasTable('ews_projects')) {
            Schema::table('ews_projects', function (Blueprint $table) {
                if (Schema::hasColumn('ews_projects', 'register_flat')) {
                    $table->dropColumn('register_flat');
                }
            });
        }
    }
};
