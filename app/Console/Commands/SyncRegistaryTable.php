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
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereNotNull('o.RegistrationNo')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('registary as r')
                    ->whereColumn('r.SecondPartyMobile', 'o.MobileNo');
            })
            ->get(['o.RegistrationNo', 'o.MobileNo', 'o.OwnerName']);

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
                    ->get('https://api.revenueharyana.gov.in/api/LandRegistration/getRegistrationforHFAland', [
                        'RegistrationNo' => $regNo,
                    ]);

                if ($response->successful()) {
                    $registrations = $response->json();

                    if (!empty($registrations) && is_array($registrations)) {
                        foreach ($registrations as $reg) {
                            $token = $reg['Token'] ?? $reg['token'] ?? null;
                            if (!$token) {
                                continue;
                            }

                            // Check if this registry token already exists in our table to prevent duplicates
                            $exists = DB::table('registary')
                                ->where('Token', $token)
                                ->exists();

                            if (!$exists) {
                                DB::table('registary')->insert([
                                    'District' => $reg['District'] ?? $reg['district'] ?? null,
                                    'TehsilName' => $reg['TehsilName'] ?? $reg['tehsilName'] ?? null,
                                    'Village' => $reg['Village'] ?? $reg['village'] ?? null,
                                    'Token' => $token,
                                    'Khewat' => $reg['Khewat'] ?? $reg['khewat'] ?? null,
                                    'FirstParty' => $reg['FirstParty'] ?? $reg['firstParty'] ?? null,
                                    'TotalArea' => $reg['TotalArea'] ?? $reg['totalArea'] ?? null,
                                    'Bhag' => $reg['Bhag'] ?? $reg['bhag'] ?? null,
                                    'TransferArea' => $reg['TransferArea'] ?? $reg['transferArea'] ?? null,
                                    'SecondParty' => $reg['SecondParty'] ?? $reg['secondParty'] ?? null,
                                    'SecondPartyMobile' => $reg['SecondPartyMobile'] ?? $reg['secondPartyMobile'] ?? $reg['SecondPartyMobileNo'] ?? $owner->MobileNo,
                                    'RegistaryNumber' => $reg['RegistaryNumber'] ?? $reg['registaryNumber'] ?? null,
                                    'RegistaryDate' => $reg['RegistaryDate'] ?? $reg['registaryDate'] ?? null,
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
