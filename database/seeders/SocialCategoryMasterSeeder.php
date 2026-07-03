<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SocialCategoryMasterSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    public function run(): void
    {
        $filePath = database_path('seeders/data/owners/SocialCategories.xlsx');
        
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
                'CategoryId' => (int) $row['A'],
                'CategoryName' => trim($row['B']),
                'CompanyId' => $row['C'] !== null && trim($row['C']) !== '' ? (int) $row['C'] : null,
                'CreatedDate' => $this->parseExcelDateTime($row['D']),
                'CreatedBy' => $row['E'] !== null && trim($row['E']) !== '' ? (int) $row['E'] : null,
                'UpdatedDate' => $this->parseExcelDateTime($row['F']),
                'UpdatedBy' => $row['G'] !== null && trim($row['G']) !== '' ? (int) $row['G'] : null,
                'IsActive' => $row['H'] !== null && trim($row['H']) !== '' ? (bool) $row['H'] : true,
            ];
        }

        $this->withoutForeignKeyChecks(function () use ($data) {
            DB::table('socialcategorymaster')->truncate();
            DB::table('socialcategorymaster')->insert($data);
        });
        
        $this->command->info("socialcategorymaster seeded: " . count($data) . " rows");
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
