<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VillageMasterSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    public function run(): void
    {
        $filePath = database_path('seeders/data/owners/villagemaster.csv');
        
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }

        $file = fopen($filePath, 'r');
        
        // Read header
        $header = fgetcsv($file);
        if (!$header) {
            fclose($file);
            throw new \Exception("CSV file is empty: " . $filePath);
        }

        // Clean headers (remove BOM or spaces if any)
        $header = array_map(function($h) {
            return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $header);

        $data = [];
        
        while (($row = fgetcsv($file)) !== false) {
            if (count($header) !== count($row)) {
                continue;
            }

            $rowData = array_combine($header, $row);

            $cleanVal = function($val, $asInt = false) {
                $trimmed = trim($val);
                if ($trimmed === '' || strtoupper($trimmed) === 'NULL') {
                    return null;
                }
                return $asInt ? (int) $trimmed : $trimmed;
            };

            $data[] = [
                'VillageId' => $cleanVal($rowData['VillageId'], true),
                'BlockId' => $cleanVal($rowData['BlockId'], true),
                'DistrictId' => $cleanVal($rowData['DistrictId'], true),
                'VillageName' => trim($rowData['VillageName'] ?? ''),
                'TotalPlots' => $cleanVal($rowData['TotalPlots'] ?? null, true),
                'Phase1' => $cleanVal($rowData['Phase1'] ?? null),
                'totalPlotsPhase2' => $cleanVal($rowData['totalPlotsPhase2'] ?? null, true),
                'Phase2' => $cleanVal($rowData['Phase2'] ?? null),
                'totalPlotsPhase3' => $cleanVal($rowData['totalPlotsPhase3'] ?? null, true),
                'Phase3' => $cleanVal($rowData['Phase3'] ?? null),
            ];
        }

        fclose($file);

        $this->withoutForeignKeyChecks(function () use ($data) {
            DB::table('villagemaster')->truncate();
            
            // Insert in chunks of 500
            $chunks = array_chunk($data, 500);
            foreach ($chunks as $chunk) {
                DB::table('villagemaster')->insert($chunk);
            }
        });
        
        $this->command->info("villagemaster seeded: " . count($data) . " rows");
    }
}
