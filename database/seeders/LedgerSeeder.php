<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\ImportsCsvFromDocuments;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LedgerSeeder extends Seeder
{
    use ImportsCsvFromDocuments;

    private const CHUNK_SIZE = 500;

    public function run(): void
    {
        ini_set('memory_limit', '512M');

        $csvFile = $this->csvPath('Ledger.csv');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('ledger')->truncate();

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
                'Id' => (int) ($row[0] ?? 0),
                'InstallmentNumber' => (int) ($row[1] ?? 0),
                'DueDate' => $this->parseDate($row[2] ?? null),
                'PrincipalAmount' => (int) ($row[3] ?? 0),
                'InterestAmount' => (int) ($row[4] ?? 0),
                'GSTAmount' => (int) ($row[5] ?? 0),
                'InsuranceAmount' => (int) ($row[6] ?? 0),
                'EMIAmount' => (int) ($row[7] ?? 0),
                'CalculatedAmount' => (int) ($row[8] ?? 0),
                'PenaltyAmount' => (int) ($row[9] ?? 0),
                'PenaltyRate' => (int) ($row[10] ?? 0),
                'GSTonPenalty' => (int) ($row[11] ?? 0),
                'Payment' => (int) ($row[12] ?? 0),
                'CumulativePenalty' => (int) ($row[13] ?? 0),
                'CumulativeGST' => (int) ($row[14] ?? 0),
                'RemainingBalance' => (int) ($row[15] ?? 0),
                'ConsecutiveMissedPayments' => (int) ($row[16] ?? 0),
                'Payable_amount' => (int) ($row[17] ?? 0),
                'total_gst' => (int) ($row[18] ?? 0),
                'gst_running_bal' => (int) ($row[19] ?? 0),
                'int_on_gst' => (int) ($row[20] ?? 0),
                'int_running_bal' => (int) ($row[21] ?? 0),
                'total_gst_int_payable' => (int) ($row[22] ?? 0),
                'gst_payment' => (int) ($row[23] ?? 0),
                'balance_amount' => (int) ($row[24] ?? 0),
                'CompanyId' => (int) ($row[25] ?? 544),
                'Is_Active' => (int) ($row[26] ?? 1),
                'Is_Deleted' => (int) ($row[27] ?? 0),
                'CreatedBy' => $this->nullableInt($row[28] ?? null),
                'CreateDate' => $this->parseDateTime($row[29] ?? null),
                'AuthorizedBy' => $this->nullableInt($row[30] ?? null),
                'AuthorizedDate' => $this->parseDateTime($row[31] ?? null),
                'AssetId' => (int) ($row[32] ?? 0),
                'PaneltyOnAmount' => $this->nullableFloat($row[33] ?? null),
                'InstallmentPhase' => $this->nullableFloat($row[34] ?? null),
                'PrincipalBalance' => $this->nullableFloat($row[35] ?? null),
            ];

            if (count($buffer) >= self::CHUNK_SIZE) {
                DB::table('ledger')->insert($buffer);
                $imported += count($buffer);
                $buffer = [];
            }
        }

        fclose($file);

        if ($buffer !== []) {
            DB::table('ledger')->insert($buffer);
            $imported += count($buffer);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command?->info("Ledger imported: {$imported}");
    }
}
