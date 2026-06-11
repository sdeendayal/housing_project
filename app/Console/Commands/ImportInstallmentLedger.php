<?php

namespace App\Console\Commands;

use Database\Seeders\InstallmentDueSeeder;
use Database\Seeders\LedgerSeeder;
use Illuminate\Console\Command;

class ImportInstallmentLedger extends Command
{
    protected $signature = 'import:installment-ledger
                            {--only= : Import only ledger or installment_due}';

    protected $description = 'Import ledger and installment_due tables from CSV (~8.5 lakh rows)';

    public function handle(): int
    {
        ini_set('memory_limit', '512M');

        $only = $this->option('only');

        if ($only && ! in_array($only, ['ledger', 'installment_due'], true)) {
            $this->error('Invalid --only value. Use: ledger or installment_due');

            return self::FAILURE;
        }

        $this->warn('Large import — this may take 1–2 minutes.');

        if ($only === null || $only === 'ledger') {
            $this->info('Importing ledger...');
            $this->call('db:seed', ['--class' => LedgerSeeder::class, '--force' => true]);
        }

        if ($only === null || $only === 'installment_due') {
            $this->info('Importing installment_due...');
            $this->call('db:seed', ['--class' => InstallmentDueSeeder::class, '--force' => true]);
        }

        $this->info('Installment ledger import complete.');

        return self::SUCCESS;
    }
}
