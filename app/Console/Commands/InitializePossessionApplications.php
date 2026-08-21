<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PhysicalPossessionApplication;
use Illuminate\Support\Facades\DB;

class InitializePossessionApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:initialize-possession';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk initialize missing Physical Possession applications for eligible MMGAY beneficiaries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting bulk initialization of Physical Possession applications...");

        $receiptsQuery = DB::table('cash_receipt_details')
            ->select('asset_number')
            ->selectRaw('SUM(total_paid_amount) as receipt_total')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->groupBy('asset_number');

        $query = DB::table('property_auction_detail as pad')
            ->join('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->join('mmsay_eligible_beneficiaries as meb', 'ppp.ApplicationNo', '=', 'meb.application_number')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('physical_possession_applications as ppa', function ($join) {
                $join->on('pad.PurchaserID', '=', 'ppa.private_purchaser_id')
                     ->on('pad.AssetId', '=', 'ppa.asset_id');
            })
            ->leftJoinSub($receiptsQuery, 'crd', 'pad.AssetId', '=', 'crd.asset_number')
            ->where('pad.IsActive', 1)
            ->where('pad.IsDeleted', 0)
            ->whereNull('ppa.id')
            ->select([
                'pad.PropertyAuctionId as PropertyAuctionId',
                'pad.AssetId',
                'pad.PurchaserID',
                'pad.FlatCost',
                'pad.ReceivedAmount',
                'pad.BalanceAmount',
                'ppp.PrivatePurchaserName',
                'ppp.PurchaserFatherName',
                'ppp.Address',
                'ppp.MobileNo',
                'ppp.ApplicationNo',
                'ppp.PPPId',
                'ppp.MemberID',
                'ppp.DistrictId',
                'd.DistrictName',
                DB::raw("COALESCE(pad.ReceivedAmount, 0) + COALESCE(crd.receipt_total, 0) as total_paid")
            ])
            ->whereRaw("COALESCE(pad.ReceivedAmount, 0) + COALESCE(crd.receipt_total, 0) >= 60000");

        $totalMissing = $query->count();
        $this->info("Found {$totalMissing} missing physical possession applications.");

        if ($totalMissing === 0) {
            $this->info("All applications are already initialized.");
            return Command::SUCCESS;
        }

        $chunkSize = 500;
        $processed = 0;

        $query->chunkById($chunkSize, function ($missing) use (&$processed, $totalMissing) {
            DB::transaction(function () use ($missing, &$processed) {
                foreach ($missing as $p) {
                    // Find or create citizen user
                    $user = null;
                    if (!empty($p->MobileNo)) {
                        $user = User::where('mobile', $p->MobileNo)->first();
                    }
                    if (!$user) {
                        $user = User::where('private_purchaser_id', $p->PurchaserID)->first();
                    }

                    if (!$user) {
                        $user = User::create([
                            'name' => $p->PrivatePurchaserName,
                            'mobile' => $p->MobileNo ?? '99999' . str_pad($p->PurchaserID, 5, '0', STR_PAD_LEFT),
                            'role' => 'citizen',
                            'private_purchaser_id' => $p->PurchaserID,
                        ]);
                    } else {
                        if (empty($user->private_purchaser_id)) {
                            $user->private_purchaser_id = $p->PurchaserID;
                            $user->save();
                        }
                    }

                    // Create Physical Possession Application
                    PhysicalPossessionApplication::create([
                        'user_id' => $user->id,
                        'private_purchaser_id' => $p->PurchaserID,
                        'asset_id' => $p->AssetId,
                        'application_number' => 'PP-' . now()->format('Y') . '-' . ($p->ApplicationNo ?? rand(1000, 9999)),
                        'slip_id' => 'SLIP-' . uniqid(),
                        'district_id' => $p->DistrictId,
                        'district_name' => $p->DistrictName,
                        'mobile' => $p->MobileNo,
                        'applicant_name' => $p->PrivatePurchaserName,
                        'father_name' => $p->PurchaserFatherName ?? '',
                        'address' => $p->Address ?? '',
                        'flat_cost' => $p->FlatCost,
                        'received_amount' => $p->ReceivedAmount,
                        'balance_amount' => $p->BalanceAmount,
                        'physical_possession_status' => 'Eligible for Physical Possession',
                        'status' => 'pending',
                    ]);
                    $processed++;
                }
            });

            $this->info("Processed {$processed} / {$totalMissing} records...");
        }, 'pad.PropertyAuctionId', 'PropertyAuctionId');

        $this->info("Successfully completed! Total initialized: {$processed}");
        return Command::SUCCESS;
    }
}
