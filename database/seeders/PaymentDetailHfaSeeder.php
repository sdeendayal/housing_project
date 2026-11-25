<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentDetailHfaSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    private const CHUNK_SIZE = 1000;

    public function run(): void
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        DB::connection()->disableQueryLog();

        $baseDir = database_path('seeders/data/payment_detail_hfa');
        $this->ensureDataReady($baseDir);

        $this->withoutForeignKeyChecks(function () use ($baseDir) {
            $this->seedCashReceiptDetails($baseDir . '/cash_receipt_details.csv');
            $this->seedInstallmentDue($baseDir . '/installment_due.csv');
            $this->seedLedger($baseDir . '/ledger.csv');
        });
    }

    private function ensureDataReady(string $baseDir): void
    {
        $zipCandidates = [
            $baseDir . '/payment detail HFA.zip',
            $baseDir . '/payment_detail_hfa.zip',
            database_path('seeders/data/payment detail HFA.zip'),
        ];

        $zipPath = null;
        foreach ($zipCandidates as $candidate) {
            if (file_exists($candidate)) {
                $zipPath = $candidate;
                break;
            }
        }

        $gzFiles = [
            $baseDir . '/cash_receipt_details.csv.gz',
            $baseDir . '/installment_due.csv.gz',
            $baseDir . '/ledger.csv.gz',
        ];

        $needsConversion = false;
        foreach ($gzFiles as $gz) {
            if (! file_exists($gz)) {
                $needsConversion = true;
                break;
            }
            if ($zipPath && filemtime($zipPath) > filemtime($gz)) {
                $needsConversion = true;
                break;
            }
        }

        if ($needsConversion && $zipPath) {
            $this->command?->info("Converting data from {$zipPath}...");
            $script = database_path('seeders/scripts/convert_payment_hfa.py');
            if (file_exists($script)) {
                $output = [];
                $returnVar = 0;
                exec("python3 " . escapeshellarg($script) . " " . escapeshellarg($baseDir) . " 2>&1", $output, $returnVar);
                if ($returnVar !== 0) {
                    $this->command?->warn("Python conversion returned code {$returnVar}: " . implode("\n", $output));
                } else {
                    $this->command?->info("Data converted successfully from zip.");
                }
            }
        }
    }

    private function openCsvStream(string $basePath)
    {
        if (file_exists($basePath . '.gz')) {
            return fopen('compress.zlib://' . $basePath . '.gz', 'r');
        }

        if (file_exists($basePath)) {
            return fopen($basePath, 'r');
        }

        return null;
    }

    private function seedCashReceiptDetails(string $csvFile): void
    {
        $file = $this->openCsvStream($csvFile);
        if (! $file) {
            $this->command?->error("File not found: {$csvFile} (or .gz)");
            return;
        }

        $this->command?->info("Truncating cash_receipt_details...");
        DB::table('cash_receipt_details')->truncate();

        $this->command?->info("Seeding cash_receipt_details...");
        fgetcsv($file); // skip header

        $buffer = [];
        $total = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $buffer[] = [
                'id' => (int) $row[0],
                'asset_number' => (int) ($row[1] ?? 0),
                'total_paid_amount' => (float) ($row[2] ?? 0),
                'receipt_number' => $this->cleanStr($row[3] ?? null),
                'BranchId' => (int) ($row[4] ?? 0),
                'DistrictId' => (int) ($row[5] ?? 0),
                'CityId' => (int) ($row[6] ?? 0),
                'SectorId' => (int) ($row[7] ?? 0),
                'IsActive' => isset($row[8]) && $row[8] !== '' ? (int) $row[8] : 1,
                'IsDeleted' => isset($row[9]) && $row[9] !== '' ? (int) $row[9] : 0,
                'created_date' => $this->cleanDate($row[10] ?? null),
                'CreatedBy' => !empty($row[11]) && $row[11] !== 'NULL' ? (int) $row[11] : null,
                'ModifiedDate' => $this->cleanDate($row[12] ?? null),
                'ModifiedBy' => !empty($row[13]) && $row[13] !== 'NULL' ? (int) $row[13] : null,
                'CompanyId' => !empty($row[14]) && $row[14] !== 'NULL' ? (int) $row[14] : 544,
            ];

            if (count($buffer) >= self::CHUNK_SIZE) {
                DB::table('cash_receipt_details')->insert($buffer);
                $total += count($buffer);
                $buffer = [];

                if ($total % 50000 === 0) {
                    $this->command?->info("  ... cash_receipt_details: {$total} rows imported");
                }
            }
        }

        if (!empty($buffer)) {
            DB::table('cash_receipt_details')->insert($buffer);
            $total += count($buffer);
        }

        fclose($file);
        $this->command?->info("Successfully imported {$total} rows into cash_receipt_details.");
    }

    private function seedInstallmentDue(string $csvFile): void
    {
        $file = $this->openCsvStream($csvFile);
        if (! $file) {
            $this->command?->error("File not found: {$csvFile} (or .gz)");
            return;
        }

        $this->command?->info("Truncating installment_due...");
        DB::table('installment_due')->truncate();

        $this->command?->info("Seeding installment_due...");
        fgetcsv($file); // skip header

        $buffer = [];
        $total = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $buffer[] = [
                'DueInstallmentId' => (int) $row[0],
                'PropertyAuctionId' => (int) ($row[1] ?? 0),
                'AssetId' => (int) ($row[2] ?? 0),
                'OfferOfPossessionDate' => $this->cleanDate($row[3] ?? null),
                'InstallmentNumber' => (int) ($row[4] ?? 0),
                'DueDate' => $this->cleanDate($row[5] ?? null) ?? '2000-01-01',
                'RunningBalance' => (int) ($row[6] ?? 0),
                'EMIAmount' => (int) ($row[7] ?? 0),
                'PrincipleAmount' => (int) ($row[8] ?? 0),
                'InterestAmount' => (int) ($row[9] ?? 0),
                'GSTAmount' => (int) ($row[10] ?? 0),
                'InsuranceAmout' => (int) ($row[11] ?? 0),
                'DueAmount' => (float) ($row[12] ?? 0),
                'RunningClosingBalance' => (int) ($row[13] ?? 0),
                'LastSettledDate' => $this->cleanDate($row[14] ?? null),
                'CompanyId' => !empty($row[15]) && $row[15] !== 'NULL' ? (int) $row[15] : 544,
                'CreatedDate' => $this->cleanDate($row[16] ?? null),
                'CreatedBy' => !empty($row[17]) && $row[17] !== 'NULL' ? (int) $row[17] : null,
                'ModifiedDate' => $this->cleanDate($row[18] ?? null),
                'ModifiedBy' => !empty($row[19]) && $row[19] !== 'NULL' ? (int) $row[19] : null,
                'IsDeleted' => isset($row[20]) && $row[20] !== '' ? (int) $row[20] : 0,
                'IsActive' => isset($row[21]) && $row[21] !== '' ? (int) $row[21] : 1,
                'InstallmentPhase' => !empty($row[22]) && $row[22] !== 'NULL' ? (float) $row[22] : null,
                'PrincipalBalance' => !empty($row[23]) && $row[23] !== 'NULL' ? (float) $row[23] : null,
            ];

            if (count($buffer) >= self::CHUNK_SIZE) {
                DB::table('installment_due')->insert($buffer);
                $total += count($buffer);
                $buffer = [];

                if ($total % 50000 === 0) {
                    $this->command?->info("  ... installment_due: {$total} rows imported");
                }
            }
        }

        if (!empty($buffer)) {
            DB::table('installment_due')->insert($buffer);
            $total += count($buffer);
        }

        fclose($file);
        $this->command?->info("Successfully imported {$total} rows into installment_due.");
    }

    private function seedLedger(string $csvFile): void
    {
        $file = $this->openCsvStream($csvFile);
        if (! $file) {
            $this->command?->error("File not found: {$csvFile} (or .gz)");
            return;
        }

        $this->command?->info("Truncating ledger...");
        DB::table('ledger')->truncate();

        $this->command?->info("Seeding ledger...");
        fgetcsv($file); // skip header

        $buffer = [];
        $total = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $buffer[] = [
                'Id' => (int) $row[0],
                'InstallmentNumber' => (int) ($row[1] ?? 0),
                'DueDate' => $this->cleanDate($row[2] ?? null) ?? '2000-01-01',
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
                'CompanyId' => !empty($row[25]) && $row[25] !== 'NULL' ? (int) $row[25] : 544,
                'Is_Active' => isset($row[26]) && $row[26] !== '' ? (int) $row[26] : 1,
                'Is_Deleted' => isset($row[27]) && $row[27] !== '' ? (int) $row[27] : 0,
                'CreatedBy' => !empty($row[28]) && $row[28] !== 'NULL' ? (int) $row[28] : null,
                'CreateDate' => $this->cleanDate($row[29] ?? null),
                'AuthorizedBy' => !empty($row[30]) && $row[30] !== 'NULL' ? (int) $row[30] : null,
                'AuthorizedDate' => $this->cleanDate($row[31] ?? null),
                'AssetId' => (int) ($row[32] ?? 0),
                'PaneltyOnAmount' => !empty($row[33]) && $row[33] !== 'NULL' ? (float) $row[33] : null,
                'InstallmentPhase' => !empty($row[34]) && $row[34] !== 'NULL' ? (float) $row[34] : null,
                'PrincipalBalance' => !empty($row[35]) && $row[35] !== 'NULL' ? (float) $row[35] : null,
            ];

            if (count($buffer) >= self::CHUNK_SIZE) {
                DB::table('ledger')->insert($buffer);
                $total += count($buffer);
                $buffer = [];

                if ($total % 50000 === 0) {
                    $this->command?->info("  ... ledger: {$total} rows imported");
                }
            }
        }

        if (!empty($buffer)) {
            DB::table('ledger')->insert($buffer);
            $total += count($buffer);
        }

        fclose($file);
        $this->command?->info("Successfully imported {$total} rows into ledger.");
    }

    private function cleanDate($val): ?string
    {
        if ($val === null) {
            return null;
        }
        $val = trim($val, "\"' \t\n\r\0\x0B");
        return (!empty($val) && strtoupper($val) !== 'NULL') ? $val : null;
    }

    private function cleanStr($val): ?string
    {
        if ($val === null) {
            return null;
        }
        $val = trim($val, "\"' \t\n\r\0\x0B");
        return (!empty($val) && strtoupper($val) !== 'NULL') ? $val : null;
    }
}
