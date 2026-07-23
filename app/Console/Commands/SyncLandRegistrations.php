<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\MmgayPossessionApplication;
use App\Models\User;
use Carbon\Carbon;

class SyncLandRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mmgay:sync-land-registrations {--from-date=} {--to-date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch land registrations from Revenue HFA API and sync eligible beneficiaries for physical possession';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fromDate = $this->option('from-date') ?: Carbon::now()->subDays(3)->format('Y-m-d');
        $toDate = $this->option('to-date') ?: Carbon::now()->format('Y-m-d');

        $this->info("Starting HFA Land Registration Sync for date range: $fromDate to $toDate");

        // Call the HFA Land Registration API
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-KEY' => 'HFA26@hry#',
                ])
                ->get('https://api.revenueharyana.gov.in/api/LandRegistration/getRegistrationforHFAland', [
                    'RegFromDate' => $fromDate,
                    'RegToDate' => $toDate,
                ]);

            $registrations = [];
            $status = $response->status();

            if ($response->successful()) {
                $registrations = $response->json();
            } else {
                $this->warn("API request failed with status: " . $status);
            }

            // Local fallback mock response for testing purposes if API yields unauthorized or empty in local sandbox
            if (app()->environment('local') && (empty($registrations) || $status == 401)) {
                $this->warn("Local sandbox mode: Mocking API response with sample registration numbers for testing.");
                $registrations = [
                    [
                        'RegistrationNo' => 'MMGAYE/GP/266709',
                        'OwnerName' => 'Test Sandbox Owner 1',
                        'RegistrationDate' => Carbon::now()->format('Y-m-d'),
                    ],
                    [
                        'RegistrationNo' => 'MMGAYE/GP/77',
                        'OwnerName' => 'Test Sandbox Owner 2',
                        'RegistrationDate' => Carbon::now()->format('Y-m-d'),
                    ]
                ];
            } elseif (!$response->successful()) {
                $this->error("Sync aborted due to API failure.");
                return 1;
            }

            if (empty($registrations) || !is_array($registrations)) {
                $this->info("No land registrations found for the specified range.");
                return 0;
            }

            $this->info("Found " . count($registrations) . " registrations in API response. Processing...");

            $addedCount = 0;
            $existingCount = 0;

            foreach ($registrations as $reg) {
                $regNo = $reg['RegistrationNo'] ?? $reg['registrationNo'] ?? $reg['registration_no'] ?? null;
                if (!$regNo) {
                    continue;
                }

                // Look for matching owner in ownermaster
                $owner = DB::table('ownermaster as o')
                    ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
                    ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
                    ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
                    ->select('o.*', 'b.BlockName', 'v.VillageName', 'd.DistrictName')
                    ->where('o.RegistrationNo', $regNo)
                    ->first();

                if (!$owner) {
                    $this->warn("No owner matching RegistrationNo: $regNo in ownermaster database. Skipping.");
                    continue;
                }

                // Check if application already exists for this owner
                $existingApp = MmgayPossessionApplication::where('owner_id', $owner->OwnerId)->first();
                if ($existingApp) {
                    $existingCount++;
                    continue;
                }

                // Ensure user account exists for this mobile number under MMGAY scheme
                $user = User::where('mobile', $owner->MobileNo)
                    ->where('scheme', 'MMGAY')
                    ->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $owner->OwnerName,
                        'mobile' => $owner->MobileNo,
                        'scheme' => 'MMGAY',
                        'password' => bcrypt('123456'), // Default password placeholder
                    ]);

                    // Seed role type for this new user
                    $villagerRole = DB::table('roles')->where('slug', 'villager')->first();
                    if ($villagerRole) {
                        DB::table('role_types')->insert([
                            'user_id' => $user->id,
                            'role_id' => $villagerRole->id,
                            'Is_Active' => '1',
                            'Is_Deleted' => '0',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Create possession application auto-whitelisted
                MmgayPossessionApplication::create([
                    'user_id' => $user->id,
                    'owner_id' => $owner->OwnerId,
                    'ppp_id' => $owner->PPPId ?? null,
                    'member_id' => $owner->MemberId ?? null,
                    'flat_id' => $owner->FlatId ?? null,
                    'scheme' => 'MMGAY',
                    'application_number' => 'PP-MMGAY-' . now()->format('Y') . '-' . ($owner->RegistrationNo ?? rand(1000, 9999)),
                    'secure_id' => $owner->secure_id ?: md5(uniqid(rand(), true)),
                    'slip_id' => 'SLIP-MMGAY-' . uniqid(),
                    'district_id' => $owner->DistrictId,
                    'district_name' => $owner->DistrictName,
                    'block_id' => $owner->BlockId,
                    'block_name' => $owner->BlockName,
                    'mobile' => $owner->MobileNo,
                    'applicant_name' => $owner->OwnerName,
                    'father_name' => $owner->FatherHusbandName ?? '',
                    'address' => $owner->OwnerAddress ?? '',
                    'flat_cost' => 0,
                    'received_amount' => 0,
                    'balance_amount' => 0,
                    'physical_possession_status' => 'Eligible for Physical Possession',
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $addedCount++;
                $this->info("Successfully auto-registered Physical Possession for Owner: {$owner->OwnerName} (Reg: {$regNo})");
            }

            $this->info("Sync completed: {$addedCount} new applications created, {$existingCount} already existed.");
            return 0;

        } catch (\Exception $e) {
            $this->error("Sync execution encountered an exception: " . $e->getMessage());
            return 1;
        }
    }
}
