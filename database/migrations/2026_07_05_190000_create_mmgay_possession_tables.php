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
        // 1. Create mmgay_possession_applications table
        Schema::create('mmgay_possession_applications', function (Blueprint $table) {
            $table->id();
            $table->char('secure_id', 32)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('owner_id')->nullable();
            
            $table->string('slip_id')->nullable();
            $table->string('application_number')->unique();
            $table->integer('district_id')->nullable();
            $table->string('district_name')->nullable();
            
            $table->string('mobile', 15);
            $table->string('applicant_name');
            $table->string('father_name')->nullable();
            $table->text('address')->nullable();
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            
            $table->string('physical_possession_status')->default('Eligible for Physical Possession');
            $table->date('possession_date')->nullable();
            $table->string('meeting_slot')->nullable();
            
            $table->datetime('citizen_visit_date')->nullable();
            $table->datetime('visit_slot_1')->nullable();
            $table->datetime('visit_slot_2')->nullable();
            $table->datetime('visit_slot_3')->nullable();
            $table->text('visit_instructions')->nullable();
            
            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
            $table->string('plot_image')->nullable();
            $table->datetime('image_capture_datetime')->nullable();
            $table->string('possession_certificate')->nullable();
            $table->string('site_engineer_file')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
        });

        // 2. Create mmgay_possession_status_logs table
        Schema::create('mmgay_possession_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('mmgay_possession_applications')->cascadeOnDelete();
            $table->unsignedBigInteger('asset_id')->default(0);
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('remarks')->nullable();
            $table->string('changed_by_type'); // 'officer' or 'citizen'
            $table->unsignedBigInteger('changed_by_id');
            $table->timestamps();
        });

        // 3. Data migration: copy existing MMGAY applications and logs to separate tables
        if (Schema::hasTable('physical_possession_applications')) {
            $existingApps = DB::table('physical_possession_applications')
                ->where('scheme', 'MMGAY')
                ->get();

            foreach ($existingApps as $app) {
                // Remove keys not present in new table or handle directly
                $appData = (array)$app;
                
                // Insert into new table preserving IDs
                DB::table('mmgay_possession_applications')->insert($appData);
                
                // Get related logs
                if (Schema::hasTable('application_status_logs')) {
                    $logs = DB::table('application_status_logs')
                        ->where('application_id', $app->id)
                        ->get();
                    
                    foreach ($logs as $log) {
                        DB::table('mmgay_possession_status_logs')->insert((array)$log);
                    }
                }
            }

            // 4. Delete MMGAY records from the shared tables to complete separation
            if ($existingApps->isNotEmpty()) {
                $appIds = $existingApps->pluck('id')->toArray();
                
                if (Schema::hasTable('application_status_logs')) {
                    DB::table('application_status_logs')
                        ->whereIn('application_id', $appIds)
                        ->delete();
                }
                
                DB::table('physical_possession_applications')
                    ->where('scheme', 'MMGAY')
                    ->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mmgay_possession_status_logs');
        Schema::dropIfExists('mmgay_possession_applications');
    }
};
