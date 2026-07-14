<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AwsFlatsCridSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('seeders/data/aws_flats_crid.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath}...");
        
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $this->command->info("Excel sheet loaded. Total rows: {$highestRow}, Total columns: {$highestColumnIndex}.");

        // Read header row (row 1)
        $header = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $header[] = trim($sheet->getCell([$col, 1])->getValue());
        }

        // Clean headers to avoid any hidden characters
        $header = array_map(function ($h) {
            return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $header);

        $batch = [];
        $batchSize = 250; // Batch size to optimize database inserts
        $count = 0;

        $this->command->info("Truncating existing aws_flats_crid table...");
        DB::table('aws_flats_crid')->truncate();

        $this->command->info("Seeding data...");

        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $rowData[] = $sheet->getCell([$col, $row])->getValue();
            }

            // Combine header and row data
            $data = array_combine($header, $rowData);

            // Handle NULL strings in Excel sheets if they are literally written as "NULL"
            foreach ($data as $key => $val) {
                if ($val === 'NULL' || $val === 'null' || $val === '') {
                    $data[$key] = null;
                }
            }

            $batch[] = [
                'Id' => $data['Id'] ?? null,
                'FLAG' => $data['FLAG'] ?? null,
                'ApplicationID' => $data['ApplicationID'] ?? null,
                'DateOfBenefit' => $data['DateOfBenefit'] ?? null,
                'DepartmentName' => $data['DepartmentName'] ?? null,
                'District' => $data['District'] ?? null,
                'JobLoanName' => $data['JobLoanName'] ?? null,
                'Member_ID' => $data['Member_ID'] ?? null,
                'membername' => $data['membername'] ?? null,
                'PPP_ID' => $data['PPP_ID'] ?? null,
                'ProjectName' => $data['ProjectName'] ?? null,
                'AmountOfBenefit' => $data['AmountOfBenefit'] ?? null,
                'BenefitDetailsInCash' => $data['BenefitDetailsInCash'] ?? null,
                'ServiceSchemeName' => $data['ServiceSchemeName'] ?? null,
                'CenterState' => $data['CenterState'] ?? null,
                'EligibilityStatus' => $data['EligibilityStatus'] ?? null,
                'NatureOfBenefit' => $data['NatureOfBenefit'] ?? null,
                'ServiceScheme' => $data['ServiceScheme'] ?? null,
                'ServiceSchemeCode' => $data['ServiceSchemeCode'] ?? null,
                'Status' => $data['Status'] ?? null,
                'Flat_plotno' => $data['Flat_plotno'] ?? null,
                'Builder_Name' => $data['Builder_Name'] ?? null,
                'Builder_Addres' => $data['Builder_Addres'] ?? null,
                'DateOfApproval' => $data['DateOfApproval'] ?? null,
                'AllocationMonth' => $data['AllocationMonth'] ?? null,
                'AllocationYear' => $data['AllocationYear'] ?? null,
                'BenefitDetailsInKind' => $data['BenefitDetailsInKind'] ?? null,
                'UnitOfBenefit' => $data['UnitOfBenefit'] ?? null,
                'AmountOfBenefitInKind' => $data['AmountOfBenefitInKind'] ?? null,
                'commcode' => $data['commcode'] ?? null,
                'SrnNo' => $data['SrnNo'] ?? null,
                'SessionYear' => $data['SessionYear'] ?? null,
                'companyid' => $data['companyid'] ?? null,
                'createddate' => $data['createddate'] ?? null,
                'createdby' => $data['createdby'] ?? null,
                'new_status' => $data['new_status'] ?? null,
                'IsPushed' => $data['IsPushed'] ?? null,
                'PushedDate' => $data['PushedDate'] ?? null,
                'IsActive' => $data['IsActive'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('aws_flats_crid')->insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('aws_flats_crid')->insert($batch);
            $count += count($batch);
        }

        $this->command->info("Successfully seeded {$count} records into the aws_flats_crid table.");
    }
}
