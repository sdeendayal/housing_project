<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncAllRegistries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mmgay:sync-all-registries {--from-date=} {--to-date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch land registry records from HFA API and sync them into the registary table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fromDate = $this->option('from-date') ?: Carbon::now()->subDays(3)->format('Y-m-d');
        $toDate = $this->option('to-date') ?: Carbon::now()->format('Y-m-d');

        $this->info("Starting full HFA Land Registry Sync from $fromDate to $toDate...");
        Log::info("MMGAY Registry Sync: Full sync started from $fromDate to $toDate");

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'X-API-KEY' => 'HFA26@hry#',
                    'Accept' => 'application/json',
                ])
                ->get('https://api.revenueharyana.gov.in/api/LandRegistration/getRegistrationforHFALand', [
                    'RegFromDate' => $fromDate,
                    'RegToDate' => $toDate,
                ]);

            if (!$response->successful()) {
                $this->error("API request failed with status: " . $response->status());
                return 1;
            }

            $data = $response->json();
            $registrations = $data['payload'] ?? [];

            if (empty($registrations) || !is_array($registrations)) {
                $this->warn("No land registrations found for the specified range.");
                return 0;
            }

            $totalCount = count($registrations);
            $this->info("Found {$totalCount} registry records in API response. Processing...");

            $insertedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;

            foreach ($registrations as $reg) {
                $token = $reg['uniqueToken'] ?? $reg['flatnumber'] ?? $reg['flatid'] ?? $reg['ppId'] ?? null;
                if (!$token) {
                    $skippedCount++;
                    continue;
                }

                // Try to parse total area from Bhag denominator, else fallback to area
                $bhag = $reg['bhag'] ?? null;
                $totalArea = null;
                if ($bhag && strpos($bhag, '/') !== false) {
                    $parts = explode('/', $bhag);
                    $totalArea = trim($parts[1] ?? '');
                }
                if (empty($totalArea)) {
                    $totalArea = $reg['area'] ?? null;
                }

                $regDate = null;
                if (!empty($reg['registryDate'])) {
                    try {
                        $regDate = Carbon::parse($reg['registryDate'])->format('Y-m-d H:i:s');
                    } catch (\Exception $ex) {
                        $regDate = null;
                    }
                }

                $dbData = [
                    'District' => $reg['districtName'] ?? null,
                    'TehsilName' => $reg['tehsilName'] ?? null,
                    'Village' => $reg['villageName'] ?? null,
                    'Khewat' => $reg['khewat'] ?? null,
                    'FirstParty' => $reg['firstPartyName'] ?? 'Government/HFA',
                    'TotalArea' => $totalArea,
                    'Bhag' => $bhag,
                    'TransferArea' => $reg['transferHissaInMarla'] ?? $reg['area'] ?? null,
                    'SecondParty' => $reg['secondPartyName'] ?? $reg['fullname'] ?? null,
                    'SecondPartyMobile' => $reg['secondPartyMobile'] ?? null,
                    'RegistaryNumber' => $reg['registryNumber'] ?? $reg['registrationNo'] ?? null,
                    'RegistaryDate' => $regDate,
                    
                    // Store all extra HFA API response parameters
                    'flatid' => $reg['flatid'] ?? null,
                    'flatnumber' => $reg['flatnumber'] ?? null,
                    'registrationNo' => $reg['registrationNo'] ?? null,
                    'pppId' => $reg['pppId'] ?? null,
                    'area' => $reg['area'] ?? null,
                    'unit' => $reg['unit'] ?? null,
                    'ownerid' => $reg['ownerid'] ?? null,
                    'fullname' => $reg['fullname'] ?? null,
                    'fatherName' => $reg['fatherName'] ?? null,
                    'dues' => $reg['dues'] ?? null,
                    'acceptFlag' => $reg['acceptFlag'] ?? null,
                    'propertyCategory' => $reg['propertyCategory'] ?? null,
                    'transferHissaInMarla' => $reg['transferHissaInMarla'] ?? null,
                    
                    'updated_at' => now(),
                ];

                // Check if this registry already exists (by Token OR by RegistaryNumber + RegistaryDate OR by flatid)
                $exists = DB::table('registary')
                    ->where(function($q) use ($token, $reg, $regDate) {
                        $q->where('Token', $token);
                        
                        $regNum = $reg['registryNumber'] ?? $reg['registrationNo'] ?? null;
                        if (!empty($regNum) && !empty($regDate)) {
                            $q->orWhere(function($sub) use ($regNum, $regDate) {
                                $sub->where('RegistaryNumber', $regNum)
                                    ->where('RegistaryDate', $regDate);
                            });
                        }
                        
                        if (!empty($reg['flatid'])) {
                            $q->orWhere('flatid', $reg['flatid']);
                        }
                    })
                    ->first();

                if (!$exists) {
                    $dbData['Token'] = $token;
                    $dbData['created_at'] = now();
                    DB::table('registary')->insert($dbData);
                    $insertedCount++;
                } else {
                    $skippedCount++;
                }
            }

            $this->info("Registry Sync completed successfully!");
            $this->info("Total Processed: {$totalCount}");
            $this->info("Newly Inserted: {$insertedCount}");
            $this->info("Updated Existing: {$updatedCount}");
            $this->info("Skipped: {$skippedCount}");

            Log::info("MMGAY Registry Sync completed: Inserted {$insertedCount}, Updated {$updatedCount}, Skipped {$skippedCount}");

            return 0;

        } catch (\Exception $e) {
            $this->error("Sync execution encountered an exception: " . $e->getMessage());
            Log::error("MMGAY Registry Sync Exception: " . $e->getMessage());
            return 1;
        }
    }
}
