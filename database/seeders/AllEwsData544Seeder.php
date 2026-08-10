<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AllEwsData544Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '4G');

        $excelPath = 'C:/Users/hp/Downloads/544_data_all_columns_v1.xlsx';
        if (!file_exists($excelPath)) {
            $excelPath = database_path('seeders/data/544_data_all_columns_v1.xlsx');
        }

        if (!file_exists($excelPath)) {
            $this->command->error("Excel file not found at: {$excelPath}");
            return;
        }

        $this->command->info("Extracting Excel file to temporary directory...");
        $tempDir = storage_path('app/excel_temp_' . time());
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($excelPath) === TRUE) {
            $zip->extractTo($tempDir, 'xl/sharedStrings.xml');
            $zip->extractTo($tempDir, 'xl/worksheets/sheet1.xml');
            $zip->close();
        } else {
            $this->command->error("Failed to unzip Excel file.");
            return;
        }

        $sharedStringsFile = $tempDir . '/xl/sharedStrings.xml';
        $sheetFile = $tempDir . '/xl/worksheets/sheet1.xml';

        if (!file_exists($sharedStringsFile) || !file_exists($sheetFile)) {
            $this->command->error("Extracted XML files not found inside temp dir.");
            $this->cleanup($tempDir);
            return;
        }

        // 1. Load shared strings
        $this->command->info("Loading shared strings...");
        $strings = [];
        $xml = new \XMLReader();
        $xml->open($sharedStringsFile);
        while ($xml->read()) {
            if ($xml->nodeType == \XMLReader::ELEMENT && $xml->name == 't') {
                $strings[] = $xml->readString();
            }
        }
        $xml->close();
        $this->command->info("Loaded " . count($strings) . " shared strings.");

        // 2. Fetch district mapping
        $this->command->info("Loading districts from ews_districts...");
        $districts = DB::table('ews_districts')->get()->keyBy(function($item) {
            return strtoupper(trim($item->name));
        })->toArray();

        // 3. Clear existing table data
        $this->command->info("Truncating all_ews_data_544 table...");
        DB::table('all_ews_data_544')->truncate();

        // 4. Stream sheet1.xml
        $this->command->info("Streaming sheet1.xml and importing data...");
        $xml = new \XMLReader();
        $xml->open($sheetFile);

        $rowCount = 0;
        $headers = [];
        $colMap = [];
        $batch = [];
        $batchSize = 150;
        $insertedCount = 0;

        while ($xml->read()) {
            if ($xml->nodeType == \XMLReader::ELEMENT && $xml->name == 'row') {
                $rowNum = $xml->getAttribute('r');
                $rowData = [];
                
                $rowXml = $xml->readOuterXml();
                $cellReader = new \XMLReader();
                $cellReader->XML($rowXml);
                
                while ($cellReader->read()) {
                    if ($cellReader->nodeType == \XMLReader::ELEMENT && $cellReader->name == 'c') {
                        $ref = $cellReader->getAttribute('r');
                        $type = $cellReader->getAttribute('t');
                        
                        preg_match('/^([A-Z]+)/', $ref, $matches);
                        $colLetter = $matches[1];
                        $colIdx = $this->colIndex($colLetter);
                        
                        $val = '';
                        $valReader = new \XMLReader();
                        $valReader->XML($cellReader->readOuterXml());
                        while ($valReader->read()) {
                            if ($valReader->nodeType == \XMLReader::ELEMENT && $valReader->name == 'v') {
                                $val = $valReader->readString();
                                break;
                            }
                        }
                        $valReader->close();
                        
                        if ($type === 's') {
                            $rowData[$colIdx] = isset($strings[(int)$val]) ? $strings[(int)$val] : $val;
                        } else {
                            $rowData[$colIdx] = $val;
                        }
                    }
                }
                $cellReader->close();
                
                // Sort row data by key
                ksort($rowData);

                if ($rowNum == 1) {
                    // This is the header row
                    $headers = $rowData;
                    
                    // Create unique columns map matching the migration cleaning logic
                    $seen = [
                        'id' => 1,
                        'secure_id' => 1,
                        'dist' => 1,
                        'dist_id' => 1,
                        'property_type' => 1,
                        'created_at' => 1,
                        'updated_at' => 1
                    ];
                    
                    foreach ($headers as $index => $header) {
                        $header = trim($header, "\xEF\xBB\xBF \t\n\r\0\x0B");
                        if (empty($header)) {
                            $header = "column_" . $index;
                        }
                        
                        $lowerHeader = strtolower($header);
                        if (isset($seen[$lowerHeader])) {
                            $seen[$lowerHeader]++;
                            $header = $header . "_" . $seen[$lowerHeader];
                        } else {
                            $seen[$lowerHeader] = 1;
                        }
                        $colMap[$index] = $header;
                    }
                    
                    $rowCount++;
                    continue;
                }

                // Process data row
                $rowInsert = [];
                
                // Initialize all fields from map with null
                foreach ($colMap as $dbCol) {
                    $rowInsert[$dbCol] = null;
                }

                // Map row fields
                foreach ($rowData as $colIdx => $val) {
                    if (isset($colMap[$colIdx])) {
                        $dbCol = $colMap[$colIdx];
                        if ($val === 'NULL' || $val === 'null' || $val === '') {
                            $val = null;
                        }
                        $rowInsert[$dbCol] = $val;
                    }
                }

                // Match location to district from ews_districts (referred to as ews_dist_master)
                // Use LocationnName from index 0
                $locationnName = $rowInsert['LocationnName'] ?? '';
                $matchedDist = null;
                $matchedDistId = null;

                if (!empty($locationnName)) {
                    $searchKey = strtoupper(trim($locationnName));
                    if (isset($districts[$searchKey])) {
                        $matchedDist = $districts[$searchKey]->name;
                        $matchedDistId = $districts[$searchKey]->id;
                    } else {
                        // Fallback partial matching
                        foreach ($districts as $name => $distObj) {
                            if (strpos($searchKey, $name) !== false || strpos($name, $searchKey) !== false) {
                                $matchedDist = $distObj->name;
                                $matchedDistId = $distObj->id;
                                break;
                            }
                        }
                    }
                }

                $rowInsert['secure_id'] = Str::random(32);
                $rowInsert['dist'] = $matchedDist;
                $rowInsert['dist_id'] = $matchedDistId;
                $rowInsert['property_type'] = 'flat';
                $rowInsert['created_at'] = now();
                $rowInsert['updated_at'] = now();

                $batch[] = $rowInsert;
                $rowCount++;

                if (count($batch) >= $batchSize) {
                    DB::table('all_ews_data_544')->insert($batch);
                    $insertedCount += count($batch);
                    $batch = [];
                }
            }
        }
        $xml->close();

        if (count($batch) > 0) {
            DB::table('all_ews_data_544')->insert($batch);
            $insertedCount += count($batch);
        }

        $this->command->info("Seeding completed successfully! Total rows parsed: {$rowCount}, Total records seeded: {$insertedCount}.");

        // 5. Clean up temp files
        $this->cleanup($tempDir);
    }

    private function colIndex($col) {
        $len = strlen($col);
        $idx = 0;
        for ($i = 0; $i < $len; $i++) {
            $idx = $idx * 26 + (ord($col[$i]) - 64);
        }
        return $idx - 1;
    }

    private function cleanup($dir) {
        $this->command->info("Cleaning up temporary directory...");
        if (is_dir($dir)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }

            rmdir($dir);
        }
    }
}
