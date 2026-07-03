<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FlatMasterSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    private const CHUNK_SIZE = 1000;

    public function run(): void
    {
        ini_set('memory_limit', '512M');

        $filePath = database_path('seeders/data/owners/Flats.xlsx');
        
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
        
        $buffer = [];
        $importedCount = 0;

        $this->withoutForeignKeyChecks(function () use ($rows, $highestRow, &$buffer, &$importedCount) {
            DB::table('flatmaster')->truncate();

            for ($i = 2; $i <= $highestRow; $i++) {
                $row = $rows[$i];
                if (empty(array_filter($row))) {
                    continue;
                }

                $isActiveValue = trim((string)($row['K'] ?? '1'));
                $isActive = $isActiveValue === '' || $isActiveValue === '1' || strtolower($isActiveValue) === 'true';

                $buffer[] = [
                    'FlatId' => (int) $row['A'],
                    'FlatNo' => trim($row['B'] ?? ''),
                    'VillageId' => (int) $row['C'],
                    'BlockId' => (int) $row['D'],
                    'DistrictId' => (int) $row['E'],
                    'CompanyId' => $row['F'] !== null && trim($row['F']) !== '' ? (int) $row['F'] : null,
                    'CreatedDate' => $this->parseExcelDateTime($row['G']),
                    'CreatedBy' => $row['H'] !== null && trim($row['H']) !== '' ? (int) $row['H'] : null,
                    'UpdatedDate' => $this->parseExcelDateTime($row['I']),
                    'UpdatedBy' => $row['J'] !== null && trim($row['J']) !== '' ? (int) $row['J'] : null,
                    'IsActive' => $isActive,
                ];

                if (count($buffer) >= self::CHUNK_SIZE) {
                    DB::table('flatmaster')->insert($buffer);
                    $importedCount += count($buffer);
                    $buffer = [];
                }
            }

            if ($buffer !== []) {
                DB::table('flatmaster')->insert($buffer);
                $importedCount += count($buffer);
            }
        });
        
        $this->command->info("flatmaster seeded: {$importedCount} rows");
    }

    private function parseExcelDateTime($value): ?string
    {
        if ($value === null || $value === '' || strtolower((string)$value) === 'null') {
            return null;
        }
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s');
            } catch (\Exception) {
                return null;
            }
        }
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return null;
        }
    }
}
