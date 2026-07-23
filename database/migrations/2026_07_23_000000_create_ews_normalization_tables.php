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
        // 1. Create ews_towns table
        if (!Schema::hasTable('ews_towns')) {
            Schema::create('ews_towns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('district_id');
                $table->string('name');
                $table->timestamps();

                $table->index(['district_id', 'name']);
            });
        }

        // 2. Create ews_projects table
        if (!Schema::hasTable('ews_projects')) {
            Schema::create('ews_projects', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('district_id');
                $table->string('name');
                $table->timestamps();

                $table->index(['district_id', 'name']);
            });
        }

        // 3. Create ews_blocks table
        if (!Schema::hasTable('ews_blocks')) {
            Schema::create('ews_blocks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id');
                $table->string('name');
                $table->timestamps();

                $table->index(['project_id', 'name']);
            });
        }

        // 4. Add foreign keys / reference columns to ews_builder_flats
        Schema::table('ews_builder_flats', function (Blueprint $table) {
            if (!Schema::hasColumn('ews_builder_flats', 'town_id')) {
                $table->unsignedBigInteger('town_id')->nullable()->after('town_name');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('project_name');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'block_id')) {
                $table->unsignedBigInteger('block_id')->nullable()->after('block_tower_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ews_builder_flats', function (Blueprint $table) {
            $table->dropColumn(['town_id', 'project_id', 'block_id']);
        });

        Schema::dropIfExists('ews_blocks');
        Schema::dropIfExists('ews_projects');
        Schema::dropIfExists('ews_towns');
    }
};
