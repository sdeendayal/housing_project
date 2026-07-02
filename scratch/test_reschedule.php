<?php

use App\Models\User;
use App\Models\PhysicalPossessionApplication;
use App\Models\ApplicationStatusLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::beginTransaction();

try {
    echo "Starting Citizen Absent & Reschedule Flow Test...\n";

    // 1. Get Panchkula Site Engineer and authenticate
    $officer = User::where('mobile', '9999900278')->first();
    if (!$officer) {
        throw new Exception("Officer 9999900278 not found!");
    }
    Auth::login($officer);
    echo "Logged in as: " . $officer->name . "\n";

    // 2. Find the application in 'Slot Selected' status
    $application = PhysicalPossessionApplication::where('secure_id', 'e61135c519a6f6e311a4ae655d49fd92')->first();
    if (!$application) {
        throw new Exception("Application e61135c519a6f6e311a4ae655d49fd92 not found!");
    }
    
    // Set mock scheduled dates
    $application->physical_possession_status = 'Slot Selected';
    $application->possession_date = '2026-07-10';
    $application->meeting_slot = '10:00 AM';
    $application->visit_slot_1 = '2026-07-10 10:00:00';
    $application->visit_slot_2 = '2026-07-11 11:00:00';
    $application->visit_slot_3 = '2026-07-12 12:00:00';
    $application->visit_instructions = 'Test instructions';
    $application->save();

    echo "Initial Status set for test: " . $application->physical_possession_status . "\n";
    echo "Initial Slot Date: " . $application->possession_date . " Slot: " . $application->meeting_slot . "\n";

    // 3. Simulate Reschedule Action in Controller
    echo "\n--- Simulating Reschedule Action ---\n";
    $oldStatus = $application->physical_possession_status;

    // Capture the previous slot time before resetting
    $prevSlotInfo = "N/A";
    if ($application->possession_date) {
        $dateFormatted = date('d M Y', strtotime($application->possession_date));
        $prevSlotInfo = $dateFormatted . " (" . ($application->meeting_slot ?? 'N/A') . ")";
    }

    // Reset status and all slots data
    $application->physical_possession_status = 'Eligible for Physical Possession';
    $application->possession_date = null;
    $application->meeting_slot = null;
    $application->visit_slot_1 = null;
    $application->visit_slot_2 = null;
    $application->visit_slot_3 = null;
    $application->visit_instructions = null;
    $application->save();

    ApplicationStatusLog::create([
        'application_id' => $application->id,
        'asset_id' => $application->asset_id,
        'old_status' => $oldStatus,
        'new_status' => 'Eligible for Physical Possession',
        'remarks' => "Citizen was absent / did not attend the scheduled visit slot: {$prevSlotInfo}. Visit slot has been reset for rescheduling by Site Engineer.",
        'changed_by_type' => 'officer',
        'changed_by_id' => $officer->id,
    ]);

    // Refresh model from DB
    $application->refresh();

    echo "New Status: " . $application->physical_possession_status . "\n";
    echo "Possession Date after reset: " . ($application->possession_date ?? 'NULL') . "\n";
    echo "Meeting Slot after reset: " . ($application->meeting_slot ?? 'NULL') . "\n";
    echo "Visit Slot 1 after reset: " . ($application->visit_slot_1 ?? 'NULL') . "\n";
    echo "Visit Instructions after reset: " . ($application->visit_instructions ?? 'NULL') . "\n";

    // 4. Verify the logs
    echo "\n--- Verifying Reschedule Status Logs ---\n";
    $logs = ApplicationStatusLog::where('application_id', $application->id)->latest()->take(2)->get();
    foreach ($logs as $log) {
        echo "Log: " . $log->old_status . " -> " . $log->new_status . " | Remarks: " . $log->remarks . "\n";
    }

    echo "\nReschedule test completed successfully! Rolling back database transaction...\n";
} catch (Exception $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "Database rolled back successfully.\n";
}
