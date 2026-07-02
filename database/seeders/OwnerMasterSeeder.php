<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Database\Seeders\Concerns\ImportsCsvFromDocuments;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OwnerMasterSeeder extends Seeder
{
    use DisablesForeignKeyChecks;
    use ImportsCsvFromDocuments;

    private const CHUNK_SIZE = 1000;

    public function run(): void
    {
        ini_set('memory_limit', '1024M');

        $csvFile = $this->csvPath('owners.csv');

        $imported = $this->withoutForeignKeyChecks(function () use ($csvFile) {
            DB::table('ownermaster')->truncate();

            $file = fopen($csvFile, 'r');
            stream_filter_append($file, 'convert.iconv.ISO-8859-1/UTF-8');
            
            // Get headers and map them to indices
            $header = fgetcsv($file);
            if (!$header) {
                fclose($file);
                return 0;
            }
            
            $headerMap = array_flip($header);

            $buffer = [];
            $importedCount = 0;

            while (($row = fgetcsv($file, 0, ',')) !== false) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $buffer[] = [
                    'OwnerId' => $this->nullableInt($this->getValue($row, $headerMap, 'OwnerId')),
                    'OwnerName' => $this->getValue($row, $headerMap, 'OwnerName', ''),
                    'Relation' => $this->getValue($row, $headerMap, 'Relation'),
                    'FatherHusbandName' => $this->getValue($row, $headerMap, 'FatherHusbandName'),
                    'Gender' => $this->getValue($row, $headerMap, 'Gender'),
                    'FlatId' => $this->nullableInt($this->getValue($row, $headerMap, 'FlatId')) ?? 0,
                    'DistrictId' => $this->nullableInt($this->getValue($row, $headerMap, 'DistrictId')),
                    'BlockId' => $this->nullableInt($this->getValue($row, $headerMap, 'BlockId')),
                    'VillageId' => $this->nullableInt($this->getValue($row, $headerMap, 'VillageId')),
                    'OwnerAddress' => $this->getValue($row, $headerMap, 'OwnerAddress'),
                    'RegistrationNo' => $this->getValue($row, $headerMap, 'RegistrationNo'),
                    'PPPId' => $this->getValue($row, $headerMap, 'PPPId'),
                    'MemberId' => $this->getValue($row, $headerMap, 'MemberId'),
                    'Caste' => $this->getValue($row, $headerMap, 'Caste'),
                    'MobileNo' => $this->getValue($row, $headerMap, 'MobileNo'),
                    'CompanyId' => $this->nullableInt($this->getValue($row, $headerMap, 'CompanyId')),
                    'Phase' => $this->nullableInt($this->getValue($row, $headerMap, 'Phase')),
                    'IsApproved' => $this->parseBool($this->getValue($row, $headerMap, 'IsApproved')),
                    'IsRejected' => $this->parseBool($this->getValue($row, $headerMap, 'IsRejected')),
                    'IsDcReconsidered' => $this->parseBool($this->getValue($row, $headerMap, 'IsDcReconsidered')),
                    'DCReOpenedCount' => $this->nullableInt($this->getValue($row, $headerMap, 'DCReOpenedCount')) ?? 0,
                    'IsPaid' => $this->parseBool($this->getValue($row, $headerMap, 'IsPaid')),
                    'IsPaymentApproved' => $this->parseBool($this->getValue($row, $headerMap, 'IsPaymentApproved')),
                    'IsAllotmentCancelled' => $this->parseBool($this->getValue($row, $headerMap, 'IsAllotmentCancelled')),
                    'Remarks' => $this->getValue($row, $headerMap, 'Remarks'),
                    'DCRemarks' => $this->getValue($row, $headerMap, 'DCRemarks'),
                    'CreatedBy' => $this->nullableInt($this->getValue($row, $headerMap, 'CreatedBy')),
                    'CreatedDate' => $this->parseDateTime($this->getValue($row, $headerMap, 'CreatedDate')),
                    'UpdatedBy' => $this->nullableInt($this->getValue($row, $headerMap, 'UpdatedBy')),
                    'UpdatedDate' => $this->parseDateTime($this->getValue($row, $headerMap, 'UpdatedDate')),
                ];

                if (count($buffer) >= self::CHUNK_SIZE) {
                    DB::table('ownermaster')->insert($buffer);
                    $importedCount += count($buffer);
                    $buffer = [];
                }
            }

            fclose($file);

            if ($buffer !== []) {
                DB::table('ownermaster')->insert($buffer);
                $importedCount += count($buffer);
            }

            return $importedCount;
        });

        $this->command?->info("Owners imported: {$imported}");
    }

    private function getValue(array $row, array $headerMap, string $column, ?string $default = null): ?string
    {
        if (!isset($headerMap[$column])) {
            return $default;
        }

        $index = $headerMap[$column];
        if (!isset($row[$index])) {
            return $default;
        }

        $val = trim($row[$index]);
        if ($val === '' || strtolower($val) === 'null') {
            return $default;
        }

        return $val;
    }

    private function parseBool(?string $val, bool $default = false): bool
    {
        if ($val === null || $val === '' || strtolower($val) === 'null') {
            return $default;
        }
        $trimmed = strtolower($val);
        if ($trimmed === '1' || $trimmed === 'true' || $trimmed === 'yes') {
            return true;
        }
        return false;
    }
}
