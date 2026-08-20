<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class MmsayEligibleBeneficiariesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '-1');
        $filePath = database_path('seeders/data/eligible.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath}...");
        
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheetNames = $spreadsheet->getSheetNames();
        
        $this->command->info("Found sheets: " . implode(', ', $sheetNames));

        // Truncate existing table
        $this->command->info("Truncating mmsay_eligible_beneficiaries table...");
        DB::table('mmsay_eligible_beneficiaries')->truncate();

        $totalSeeded = 0;
        $batch = [];
        $batchSize = 250;

        foreach ($sheetNames as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (!$sheet) {
                $this->command->error("Sheet '{$sheetName}' not found.");
                continue;
            }

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            
            $headerRow = $this->getHeaderRow($sheetName);
            $this->command->info("Processing sheet: {$sheetName} (Rows: {$highestRow}, Cols: {$highestColumnIndex}, HeaderRow: {$headerRow})...");

            // Read headers
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $headers[$col] = trim($sheet->getCell([$col, $headerRow])->getValue() ?? '');
            }

            // Get mapping for this sheet
            $colMapping = $this->getColumnMapping($sheetName, $headers);
            $this->command->info("Mapped columns for '{$sheetName}': " . json_encode($colMapping));

            $sheetSeeded = 0;
            $dataStartRow = $headerRow + 1;

            for ($row = $dataStartRow; $row <= $highestRow; $row++) {
                // If the row is empty (like a spacer), skip it. We'll check if name or application_number exists.
                $hasData = false;
                $rowInsert = [
                    'sheet_name' => $sheetName,
                    'application_number' => null,
                    'registration_number' => null,
                    'pmay_id' => null,
                    'family_id' => null,
                    'full_name' => null,
                    'father_husband_name' => null,
                    'spouse_name' => null,
                    'mobile_number' => null,
                    'marital_status' => null,
                    'caste' => null,
                    'category' => null,
                    'sector' => null,
                    'plot_number' => null,
                    'ward_no' => null,
                    'town_city' => null,
                    'district_name' => null,
                    'branch_name' => null,
                    'address' => null,
                    'pmay_benefit' => null,
                    'own_residence' => null,
                    'physical_verification' => null,
                    'status_reason' => null,
                    'remarks' => null,
                    'secure_id' => Str::random(32),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($colMapping as $colIdx => $dbCol) {
                    $cell = $sheet->getCell([$colIdx, $row]);
                    $val = $this->getCellValue($cell);

                    // Normalize NULL/empty strings
                    if ($val === 'NULL' || $val === 'null' || trim($val ?? '') === '') {
                        $val = null;
                    }

                    if ($val !== null) {
                        $hasData = true;
                    }

                    $rowInsert[$dbCol] = $val;
                }

                if (!$hasData) {
                    continue;
                }

                $batch[] = $rowInsert;
                $sheetSeeded++;
                $totalSeeded++;

                if (count($batch) >= $batchSize) {
                    DB::table('mmsay_eligible_beneficiaries')->insert($batch);
                    $batch = [];
                }
            }

            $this->command->info("Completed sheet '{$sheetName}': Seeded {$sheetSeeded} records.");
        }

        if (count($batch) > 0) {
            DB::table('mmsay_eligible_beneficiaries')->insert($batch);
        }

        $this->command->info("Seeding finished! Successfully seeded total {$totalSeeded} records into mmsay_eligible_beneficiaries.");
        
        if (isset($spreadsheet)) {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }
        gc_collect_cycles();
    }

    /**
     * Get the header row for a specific sheet.
     */
    private function getHeaderRow(string $sheetName): int
    {
        $headerRows = [
            'GOHANA' => 3,
            'safidon' => 2,
            'charkhi dadri' => 3,
            'fatehabad' => 3,
            'jhajjar' => 4,
            'sirsa' => 3,
            'palwal' => 3,
            'julana' => 4,
            'karnal' => 4,
            'kalka' => 3,
            'rewari' => 3,
            'mahendergarh' => 3,
            'rohtak' => 2,
            'YAMUNANAGAR' => 3,
        ];
        
        return $headerRows[$sheetName] ?? 1;
    }

    /**
     * Build the columns mapping based on Excel sheet headers.
     */
    private function getColumnMapping(string $sheetName, array $headers): array
    {
        $mapping = [];
        $mappedCols = [];
        
        foreach ($headers as $colIndex => $header) {
            $clean = strtolower(trim(str_replace(["\n", "\r", "_", " ", "-", ".", "(", ")", "/"], '', $header)));
            $dbCol = null;
            
            switch ($clean) {
                case 'applicationnumber':
                case 'applicationno':
                case 'appno':
                case 'registrationnumber':
                    $dbCol = 'application_number';
                    break;
                case 'applicationnoofpmayu20':
                case 'applicationid':
                case 'pmayid':
                    $dbCol = 'pmay_id';
                      break;
                case 'familyid':
                case 'pppid':
                    $dbCol = 'family_id';
                    break;
                case 'name':
                case 'fullname':
                case 'nameoftheapplicant':
                case 'membername':
                    $dbCol = 'full_name';
                    break;
                case 'fathersorhusbandname':
                case 'fatherhusbandname':
                case 'fathersname':
                case 'fathername':
                    $dbCol = 'father_husband_name';
                    break;
                case 'spoucename':
                case 'spousename':
                    $dbCol = 'spouse_name';
                    break;
                case 'mobilen0':
                case 'mobileno':
                case 'phonenumber':
                case 'contact':
                    $dbCol = 'mobile_number';
                    break;
                case 'maritalstatus':
                    $dbCol = 'marital_status';
                    break;
                case 'caste':
                    $dbCol = 'caste';
                    break;
                case 'category':
                    $dbCol = 'category';
                    break;
                case 'sector':
                case 'sectorname':
                case 'sectorplotaddress':
                    $dbCol = 'sector';
                    break;
                case 'plotnumber':
                    $dbCol = 'plot_number';
                    break;
                case 'wardno':
                    $dbCol = 'ward_no';
                    break;
                case 'town':
                case 'cityname':
                    $dbCol = 'town_city';
                    break;
                case 'districtname':
                    $dbCol = 'district_name';
                    break;
                case 'branchname':
                    if (in_array('town_city', $mappedCols)) {
                        $dbCol = 'branch_name';
                    } else {
                        $dbCol = 'town_city';
                    }
                    break;
                case 'addressandlandmark':
                case 'addresslandmark':
                case 'address':
                    $dbCol = 'address';
                    break;
                case 'whetherapplicanttakebenefitinpmayuyesno':
                    $dbCol = 'pmay_benefit';
                    break;
                case 'whetherapplicantandapplicantfamilymemberhavingownresidancehouseinthestateyesno':
                    $dbCol = 'own_residence';
                    break;
                case 'physicalverification':
                case 'physicalverificationyesno':
                case 'physicalverificationstatus':
                case 'phyverf':
                case 'physicalsurvey':
                    $dbCol = 'physical_verification';
                    break;
                case 'statusreason':
                case 'status':
                case 'eligiblenoteligible':
                case 'eligible':
                case 'actualremarks':
                    $dbCol = 'status_reason';
                    break;
                case 'remarks':
                    $dbCol = 'remarks';
                    break;
            }
            
            // Handle sheet-specific index overrides to resolve overlaps precisely
            if ($sheetName === 'GOHANA') {
                if ($colIndex == 7) $dbCol = 'town_city'; // Branch Name (SONIPAT)
                if ($colIndex == 8) $dbCol = 'district_name'; // District Name (SONIPAT)
                if ($colIndex == 9) $dbCol = 'branch_name'; // Branch Name (GOHANA MC)
            }
            if ($sheetName === 'fatehabad') {
                if ($colIndex == 7) $dbCol = 'branch_name'; // BranchName (FATEHABAD)
                if ($colIndex == 8) $dbCol = 'district_name'; // DistrictName (FATEHABAD)
                if ($colIndex == 9) $dbCol = 'town_city'; // CityName (FATEHABAD MC)
                if ($colIndex == 10) $dbCol = 'ward_no'; // SectorName (Ward 5)
            }
            if ($sheetName === 'karnal') {
                if ($colIndex == 10) $dbCol = 'ward_no'; // Sector Name (Ward 16)
            }
            
            if ($dbCol && !in_array($dbCol, $mappedCols)) {
                $mapping[$colIndex] = $dbCol;
                $mappedCols[] = $dbCol;
            }
        }
        
        return $mapping;
    }

    /**
     * Helper to get cell value or calculate it if it is a formula.
     */
    private function getCellValue($cell)
    {
        if (!$cell) {
            return null;
        }
        
        $val = $cell->getValue();
        
        // If it's a formula, calculate it
        if (is_string($val) && strpos($val, '=') === 0) {
            try {
                $calcVal = $cell->getCalculatedValue();
                
                // Check if the calculated value is an Excel error string (e.g. #N/A, #REF!, etc.)
                if (is_string($calcVal) && strpos($calcVal, '#') === 0) {
                    return null;
                }
                return $calcVal;
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return $val;
    }
}
