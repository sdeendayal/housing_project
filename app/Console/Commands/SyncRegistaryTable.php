<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncRegistaryTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mmgay:sync-registary-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync land registry details for approved and paid owners from HFA API into the local registary table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting HFA Land Registry Sync for approved and paid owners...");
        Log::info("MMGAY Registry Sync: Command execution started.");

        // 1. Fetch approved and paid owners who do not have a registry entry in the database yet
        $owners = DB::table('ownermaster as o')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereNotNull('o.RegistrationNo')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->whereColumn('r.SecondPartyMobile', 'o.MobileNo');
            })
            ->get([
                'o.RegistrationNo',
                'o.MobileNo',
                'o.OwnerName',
                'd.DistrictName',
                'v.VillageName'
            ]);

        $totalOwners = $owners->count();
        $this->info("Found {$totalOwners} approved & paid owners lacking registry matches in DB.");
        Log::info("MMGAY Registry Sync: Found {$totalOwners} owners to check.");

        if ($totalOwners === 0) {
            $this->info("All approved and paid owners already have registry data synced. Exiting.");
            return 0;
        }

        $syncedCount = 0;
        $failedCount = 0;
        $processedCount = 0;

        foreach ($owners as $owner) {
            $regNo = trim($owner->RegistrationNo);
            $processedCount++;

            $this->info("[$processedCount/$totalOwners] Querying API for: {$owner->OwnerName} (Reg: {$regNo})");

            try {
                // Hit the HFA registry API for this specific owner's RegistrationNo
                $response = Http::timeout(15)
                    ->withHeaders([
                        'X-API-KEY' => 'HFA26@hry#',
                    ])
                    ->get('https://api.revenueharyana.gov.in/api/LandRegistration/getRegistrationforHFALand', [
                        'RegistrationNo' => $regNo,
                    ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $registrations = $responseData['payload'] ?? [];

                    if (!empty($registrations) && is_array($registrations)) {
                        foreach ($registrations as $reg) {
                            $token = $reg['flatnumber'] ?? $reg['flatid'] ?? $reg['ppId'] ?? null;
                            if (!$token) {
                                continue;
                            }

                            // Check if this registry token already exists in our table to prevent duplicates
                            $exists = DB::table('registary')
                                ->where('Token', $token)
                                ->exists();

                            if (!$exists) {
                                DB::table('registary')->insert([
                                    'District' => $owner->DistrictName ?? null,
                                    'TehsilName' => null,
                                    'Village' => $owner->VillageName ?? null,
                                    'Token' => $token,
                                    'Khewat' => null,
                                    'FirstParty' => 'Government/HFA',
                                    'TotalArea' => $reg['area'] ?? null,
                                    'Bhag' => null,
                                    'TransferArea' => $reg['area'] ?? null,
                                    'SecondParty' => $reg['fullname'] ?? $owner->OwnerName,
                                    'SecondPartyMobile' => $owner->MobileNo,
                                    'RegistaryNumber' => $reg['registrationNo'] ?? $regNo,
                                    'RegistaryDate' => now(),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $syncedCount++;
                                $this->info("-> Synced & saved registry for Token: {$token}");
                            }
                        }
                    } else {
                        $this->line("-> No registry data returned from API for this registration.");
                    }
                } else {
                    $failedCount++;
                    $this->warn("-> API error for {$regNo}: HTTP " . $response->status());
                }

            } catch (\Exception $e) {
                $failedCount++;
                $this->error("-> Exception for {$regNo}: " . $e->getMessage());
                Log::error("MMGAY Registry Sync Exception for {$regNo}: " . $e->getMessage());
            }

            // Subtle sleep of 100ms between API calls to prevent overloading the endpoint
            usleep(100000);
        }

        $this->info("Sync Completed. Successfully synced {$syncedCount} new records. Failed calls: {$failedCount}.");
        Log::info("MMGAY Registry Sync Completed. Synced {$syncedCount} records. Failed calls: {$failedCount}.");
        
        return 0;
    }
}
