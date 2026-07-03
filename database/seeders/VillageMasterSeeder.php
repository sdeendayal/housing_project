<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class VillageMasterSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    public function run(): void
    {
        $filePath = database_path('seeders/data/owners/Villages.xlsx');
        
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        $rows = $sheet->rangeToArray('A1:' . $highestColumn . $highestRow, null, true, true, true);
        
        $data = [];
        for ($i = 2; $i <= $highestRow; $i++) {
            $row = $rows[$i];
            if (empty(array_filter($row))) {
                continue;
            }
            
            $data[] = [
                'VillageId' => (int) $row['A'],
                'BlockId' => (int) $row['B'],
                'DistrictId' => (int) $row['C'],
                'VillageName' => trim($row['D']),
            ];
        }

        $this->withoutForeignKeyChecks(function () use ($data) {
            DB::table('villagemaster')->truncate();
            DB::table('villagemaster')->insert($data);
        });
        
        $this->command->info("villagemaster seeded: " . count($data) . " rows");
    }
}
