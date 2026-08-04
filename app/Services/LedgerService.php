<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LedgerService
{
    /**
     * Generate ledger entries for active purchasers on the 1st of the month (or check daily to self-correct).
     * This runs via the daily Cron Job scheduler at 12:00 AM.
     *
     * @return int Number of new ledger entries created.
     */
    public static function generateDueEntries(): int
    {
        Log::info('Starting Daily Ledger entries generation check');

        // Fetch all active, non-deleted properties/purchasers from property_auction_detail
        $assets = DB::table('property_auction_detail')
            ->where('IsActive', 1)
            ->where('IsDeleted', 0)
            ->select('AssetId', 'CompanyId', 'FlatCost', 'ReceivedAmount')
            ->distinct()
            ->get();

        $createdCount = 0;
        $currentMonthStart = Carbon::now()->startOfMonth()->toDateString();

        foreach ($assets as $asset) {
            try {
                // Find the latest installment number created in the ledger for this AssetId
                $maxInstallmentNum = DB::table('ledger')
                    ->where('AssetId', $asset->AssetId)
                    ->max('InstallmentNumber') ?? 0;

                $nextInstallment = $maxInstallmentNum + 1;

                // 1. Terminate condition: If we are beyond the 36-month tenure (37+),
                // check if the user has fully paid the FlatCost (1 Lakh)
                if ($nextInstallment > 36) {
                    $ledgerPayments = (float) DB::table('ledger')
                        ->where('AssetId', $asset->AssetId)
                        ->sum('Payment');
                    
                    $totalPaid = (float) $asset->ReceivedAmount + $ledgerPayments;
                    $targetCost = (float) $asset->FlatCost;

                    if ($totalPaid >= $targetCost) {
                        // Fully paid off, stop generating installments after 36 months
                        continue;
                    }
                }

                // 2. Daily Check: Verify if a ledger entry already exists for the current month's start
                $exists = DB::table('ledger')
                    ->where('AssetId', $asset->AssetId)
                    ->where('DueDate', $currentMonthStart)
                    ->exists();

                if (!$exists) {
                    // Fetch this installment's details from installment_due schedule
                    $due = DB::table('installment_due')
                        ->where('AssetId', $asset->AssetId)
                        ->where('InstallmentNumber', $nextInstallment)
                        ->where('IsActive', 1)
                        ->where('IsDeleted', 0)
                        ->first();

                    // Fallback: If we are beyond the defined schedule (e.g. Installment 37+) and no specific schedule row exists,
                    // fetch the last defined installment details for this asset to use as a template.
                    if (!$due && $nextInstallment > 36) {
                        $due = DB::table('installment_due')
                            ->where('AssetId', $asset->AssetId)
                            ->where('IsActive', 1)
                            ->where('IsDeleted', 0)
                            ->orderBy('InstallmentNumber', 'desc')
                            ->first();
                    }

                    if ($due) {
                        // Fetch the previous ledger entry to carry forward the cumulative running balance
                        $previousLedger = DB::table('ledger')
                            ->where('AssetId', $asset->AssetId)
                            ->orderBy('InstallmentNumber', 'desc')
                            ->first();

                        $previousRemaining = $previousLedger ? (int) $previousLedger->RemainingBalance : 0;
                        $newRemaining = $previousRemaining + (int) $due->DueAmount;

                        $maxId = DB::table('ledger')->max('Id') ?? 0;
                        $newId = $maxId + 1;

                        DB::table('ledger')->insert([
                            'Id' => $newId,
                            'InstallmentNumber' => $nextInstallment,
                            'DueDate' => $currentMonthStart,
                            'PrincipalAmount' => (int) $due->PrincipleAmount,
                            'InterestAmount' => (int) $due->InterestAmount,
                            'GSTAmount' => (int) $due->GSTAmount,
                            'InsuranceAmount' => (int) $due->InsuranceAmout, // Note spelling in installment_due table
                            'EMIAmount' => (int) $due->EMIAmount,
                            'CalculatedAmount' => (int) $due->DueAmount,
                            'RemainingBalance' => $newRemaining,
                            'Payable_amount' => $newRemaining,
                            'balance_amount' => $newRemaining, // Keep balance fields in sync
                            'Payment' => 0,
                            'AssetId' => $asset->AssetId,
                            'CompanyId' => $asset->CompanyId,
                            'Is_Active' => 1,
                            'Is_Deleted' => 0,
                            'CreatedBy' => 1, // Default to 1 to match existing rows
                            'CreateDate' => Carbon::now(),
                        ]);

                        $createdCount++;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error in daily ledger generation check', [
                    'asset_id' => $asset->AssetId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Finished Daily Ledger entries generation check', ['created_count' => $createdCount]);
        return $createdCount;
    }

    /**
     * Record a payment against the active/latest ledger entry for the asset.
     * It performs the addition/subtraction ("jod-ghata") on the latest ledger row.
     *
     * @param int $assetId
     * @param float $paymentAmount
     * @param string|null $paymentDate
     * @return bool
     */
    public static function recordPayment(int $assetId, float $paymentAmount, string $paymentDate = null): bool
    {
        Log::info('Recording payment in ledger (latest row)', [
            'asset_id' => $assetId,
            'amount' => $paymentAmount
        ]);

        // Find the latest ledger row by InstallmentNumber
        $ledgerRow = DB::table('ledger')
            ->where('AssetId', $assetId)
            ->orderBy('InstallmentNumber', 'desc')
            ->first();

        // Self-Healing: If there are absolutely no rows in the ledger table for this asset yet,
        // we check the installment_due table for InstallmentNumber = 1 and generate it on the fly.
        if (!$ledgerRow) {
            $earliestDue = DB::table('installment_due')
                ->where('AssetId', $assetId)
                ->where('InstallmentNumber', 1)
                ->where('IsActive', 1)
                ->where('IsDeleted', 0)
                ->first();

            if ($earliestDue) {
                $maxId = DB::table('ledger')->max('Id') ?? 0;
                $newId = $maxId + 1;
                $currentMonthStart = Carbon::now()->startOfMonth()->toDateString();

                DB::table('ledger')->insert([
                    'Id' => $newId,
                    'InstallmentNumber' => 1,
                    'DueDate' => $currentMonthStart,
                    'PrincipalAmount' => (int) $earliestDue->PrincipleAmount,
                    'InterestAmount' => (int) $earliestDue->InterestAmount,
                    'GSTAmount' => (int) $earliestDue->GSTAmount,
                    'InsuranceAmount' => (int) $earliestDue->InsuranceAmout,
                    'EMIAmount' => (int) $earliestDue->EMIAmount,
                    'CalculatedAmount' => (int) $earliestDue->DueAmount,
                    'RemainingBalance' => (int) $earliestDue->DueAmount,
                    'Payable_amount' => (int) $earliestDue->DueAmount,
                    'balance_amount' => (int) $earliestDue->DueAmount,
                    'Payment' => 0,
                    'AssetId' => $earliestDue->AssetId,
                    'CompanyId' => $earliestDue->CompanyId,
                    'Is_Active' => 1,
                    'Is_Deleted' => 0,
                    'CreatedBy' => 1,
                    'CreateDate' => Carbon::now(),
                ]);

                $ledgerRow = DB::table('ledger')->where('Id', $newId)->first();
            }
        }

        if ($ledgerRow) {
            // Find the previous ledger entry for the asset to preserve cumulative remaining balance
            $previousLedger = DB::table('ledger')
                ->where('AssetId', $assetId)
                ->where('InstallmentNumber', '<', $ledgerRow->InstallmentNumber)
                ->orderBy('InstallmentNumber', 'desc')
                ->first();

            $previousRemaining = $previousLedger ? (int) $previousLedger->RemainingBalance : 0;

            $newPayment = (int) ($ledgerRow->Payment + $paymentAmount);
            // Remaining balance = (Previous Cumulative Balance) + (Current Due Amount) - (Current Accumulated Payment)
            $newRemaining = $previousRemaining + (int) $ledgerRow->CalculatedAmount - $newPayment;

            DB::table('ledger')
                ->where('Id', $ledgerRow->Id)
                ->update([
                    'Payment' => $newPayment,
                    'RemainingBalance' => $newRemaining,
                    'Payable_amount' => $newRemaining,
                    'balance_amount' => $newRemaining,
                ]);

            Log::info('Ledger updated successfully on latest row', [
                'ledger_id' => $ledgerRow->Id,
                'new_payment' => $newPayment,
                'new_remaining' => $newRemaining
            ]);

            return true;
        }

        Log::warning('No ledger entry found to record payment', [
            'asset_id' => $assetId,
            'amount' => $paymentAmount
        ]);

        return false;
    }
}
