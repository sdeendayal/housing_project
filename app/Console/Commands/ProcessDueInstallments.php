<?php

namespace App\Console\Commands;

use App\Services\LedgerService;
use Illuminate\Console\Command;

class ProcessDueInstallments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-due-installments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process installment dues whose due dates have passed and generate ledger entries.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Starting processing of due installments...');
        
        $createdCount = LedgerService::generateDueEntries();
        
        $this->info("Successfully created {$createdCount} new ledger entries.");
        
        return self::SUCCESS;
    }
}
