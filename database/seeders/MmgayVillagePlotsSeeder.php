<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MmgayVillagePlotsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing data to start fresh
        Schema::disableForeignKeyConstraints();
        DB::table('villagemaster')->truncate();
        Schema::enableForeignKeyConstraints();

        $path = database_path('seeders/data/Villages wise list.xlsx');
        if (!file_exists($path)) {
            $this->command->error("Excel file not found at: {$path}");
            return;
        }

        $reader = IOFactory::createReaderForFile($path);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet(0);
        $rows = $sheet->toArray(null, true, true, true);

        $insertData = [];
        $totalRows = count($rows);

        // Row 1 is Title, Row 2 is Header, data starts at Row 3
        for ($i = 3; $i <= $totalRows; $i++) {
            $row = $rows[$i];

            $villageId = trim($row['A'] ?? '');
            $blockId = trim($row['B'] ?? '');
            $districtId = trim($row['C'] ?? '');
            $villageName = trim($row['D'] ?? '');
            $phase = trim($row['E'] ?? '');
            $plotsCount = trim($row['F'] ?? '');

            if ($villageId === '' || $villageName === '') {
                continue;
            }

            $insertData[] = [
                'VillageId' => (int)$villageId,
                'BlockId' => (int)$blockId,
                'DistrictId' => (int)$districtId,
                'VillageName' => $villageName,
                'plots' => ($plotsCount !== '') ? (int)$plotsCount : null,
                'phase' => ($phase !== '') ? (int)$phase : null,
            ];
        }

        // Chunk insert to be fast and safe
        foreach (array_chunk($insertData, 200) as $chunk) {
            DB::table('villagemaster')->insert($chunk);
        }

        $count = count($insertData);
        $this->command->info("Successfully seeded {$count} village plot rows from Excel.");
    }
}
