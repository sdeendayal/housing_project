<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BlockMasterSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    public function run(): void
    {
        $filePath = database_path('seeders/data/owners/Blocks.xlsx');
        
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
                'BlockId' => (int) $row['A'],
                'DistrictId' => (int) $row['B'],
                'BlockName' => trim($row['C']),
            ];
        }

        $this->withoutForeignKeyChecks(function () use ($data) {
            DB::table('blockmaster')->truncate();
            DB::table('blockmaster')->insert($data);
        });
        
        $this->command->info("blockmaster seeded: " . count($data) . " rows");
    }
}
