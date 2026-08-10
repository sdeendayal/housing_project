<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyDrawDocumentsSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    public function run(): void
    {
        $filePath = database_path('seeders/data/property_draw_documents.csv');
        
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }

        $file = fopen($filePath, 'r');
        
        // Read header with comma delimiter
        $header = fgetcsv($file, 0, ',');
        if (!$header) {
            fclose($file);
            throw new \Exception("CSV file is empty: " . $filePath);
        }

        // Clean headers (remove BOM or spaces if any)
        $header = array_map(function($h) {
            return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $header);

        $data = [];
        
        while (($row = fgetcsv($file, 0, ',')) !== false) {
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
                'id' => $cleanVal($rowData['id'], true),
                'document_code' => $cleanVal($rowData['document_code']),
                'scheme' => $cleanVal($rowData['scheme']),
                'title' => $cleanVal($rowData['title']),
                'district_id' => $cleanVal($rowData['district_id'], true),
                'district_name' => $cleanVal($rowData['district_name']),
                'location_label' => $cleanVal($rowData['location_label']),
                'sector_label' => $cleanVal($rowData['sector_label']),
                'total_plots' => $cleanVal($rowData['total_plots'] ?? null, true),
                'original_file_name' => $cleanVal($rowData['original_file_name']),
                'file_path' => $cleanVal($rowData['file_path']),
                'published_date' => $cleanVal($rowData['published_date']) ? date('Y-m-d H:i:s', strtotime($cleanVal($rowData['published_date']))) : null,
                'sort_order' => $cleanVal($rowData['sort_order'] ?? 0, true),
                'IsActive' => $cleanVal($rowData['IsActive'] ?? 1, true),
                'IsDeleted' => $cleanVal($rowData['IsDeleted'] ?? 0, true),
                'CreatedDate' => $cleanVal($rowData['CreatedDate']) ? date('Y-m-d H:i:s', strtotime($cleanVal($rowData['CreatedDate']))) : null,
                'CreatedBy' => $cleanVal($rowData['CreatedBy'] ?? null, true),
                'ModifiedDate' => $cleanVal($rowData['ModifiedDate']) ? date('Y-m-d H:i:s', strtotime($cleanVal($rowData['ModifiedDate']))) : null,
                'ModifiedBy' => $cleanVal($rowData['ModifiedBy'] ?? null, true),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($file);

        $this->withoutForeignKeyChecks(function () use ($data) {
            DB::table('property_draw_documents')->truncate();
            
            // Insert in chunks of 500
            $chunks = array_chunk($data, 500);
            foreach ($chunks as $chunk) {
                DB::table('property_draw_documents')->insert($chunk);
            }
        });
        
        $this->command->info("property_draw_documents seeded: " . count($data) . " rows");
    }
}
