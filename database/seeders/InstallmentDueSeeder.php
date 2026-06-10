<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Database\Seeders\Concerns\ImportsCsvFromDocuments;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstallmentDueSeeder extends Seeder
{
    use DisablesForeignKeyChecks;
    use ImportsCsvFromDocuments;

    private const CHUNK_SIZE = 500;

    public function run(): void
    {
        ini_set('memory_limit', '512M');

        $csvFile = $this->csvPath('Installment_Due.csv');

        $imported = $this->withoutForeignKeyChecks(function () use ($csvFile) {
        DB::table('installment_due')->truncate();

        $file = fopen($csvFile, 'r');
        stream_filter_append($file, 'convert.iconv.ISO-8859-1/UTF-8');
        fgetcsv($file);

        $buffer = [];
        $imported = 0;

        while (($row = fgetcsv($file, 0, ',')) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $buffer[] = [
                'DueInstallmentId' => (int) ($row[0] ?? 0),
                'PropertyAuctionId' => (int) ($row[1] ?? 0),
                'AssetId' => (int) ($row[2] ?? 0),
                'OfferOfPossessionDate' => $this->parseDate($row[3] ?? null),
                'InstallmentNumber' => (int) ($row[4] ?? 0),
                'DueDate' => $this->parseDate($row[5] ?? null),
                'RunningBalance' => (int) ($row[6] ?? 0),
                'EMIAmount' => (int) ($row[7] ?? 0),
                'PrincipleAmount' => (int) ($row[8] ?? 0),
                'InterestAmount' => (int) ($row[9] ?? 0),
                'GSTAmount' => (int) ($row[10] ?? 0),
                'InsuranceAmout' => (int) ($row[11] ?? 0),
                'DueAmount' => (float) ($row[12] ?? 0),
                'RunningClosingBalance' => (int) ($row[13] ?? 0),
                'LastSettledDate' => $this->parseDate($row[14] ?? null),
                'CompanyId' => (int) ($row[15] ?? 544),
                'CreatedDate' => $this->parseDateTime($row[16] ?? null),
                'CreatedBy' => $this->nullableInt($row[17] ?? null),
                'ModifiedDate' => $this->parseDateTime($row[18] ?? null),
                'ModifiedBy' => $this->nullableInt($row[19] ?? null),
                'IsDeleted' => (int) ($row[20] ?? 0),
                'IsActive' => (int) ($row[21] ?? 1),
                'InstallmentPhase' => $this->nullableFloat($row[22] ?? null),
                'PrincipalBalance' => $this->nullableFloat($row[23] ?? null),
            ];

            if (count($buffer) >= self::CHUNK_SIZE) {
                DB::table('installment_due')->insert($buffer);
                $imported += count($buffer);
                $buffer = [];
            }
        }

        fclose($file);

        if ($buffer !== []) {
            DB::table('installment_due')->insert($buffer);
            $imported += count($buffer);
        }

        return $imported;
        });

        $this->command?->info("Installment Due imported: {$imported}");
    }
}
