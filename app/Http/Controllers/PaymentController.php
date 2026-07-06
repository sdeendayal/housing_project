<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\User;

class PaymentController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();

        return view('payment.dashboard', [
            'pageTitle' => 'Payment',
            'displayName' => $user->name ?? 'Citizen',
            'applicationId' => 'HR-MMSAY-2024-211707',
            'totalPropertyCost' => '₹ 1,00,000',
            'amountPaid' => '₹ 77,776',
            'outstandingBalance' => '₹ 22,224',
            'nextInstallmentDue' => '10 Jul 2026',
            'nextInstallmentAmount' => '₹ 2,222',
            'paymentProgress' => 78,
            'paymentHistory' => [
                [
                    'transaction_id' => 'TXN20260415001',
                    'date' => '15 Apr 2026',
                    'amount' => '₹ 2,222',
                    'mode' => 'UPI',
                    'status' => 'Processing',
                ],
                [
                    'transaction_id' => 'TXN20260310002',
                    'date' => '10 Mar 2026',
                    'amount' => '₹ 2,222',
                    'mode' => 'Net Banking',
                    'status' => 'Pending',
                ],
                [
                    'transaction_id' => 'TXN20260210003',
                    'date' => '10 Feb 2026',
                    'amount' => '₹ 2,222',
                    'mode' => 'Debit Card',
                    'status' => 'Processing',
                ],
            ],
        ]);
    }

    public function payForm(): View
    {
        $user = Auth::user();
        $purchaser = $this->findPurchaserForUser($user);

        $auction = null;
        $flatCost = 0.0;
        $outstanding = 0.0;
        $amountToPay = 2222.00;
        $totalPaid = 0.0;
        $maxAllowedAmount = 100000.00;
        $isLastInstallment = false;

        if ($purchaser) {
            $auction = DB::table('property_auction_detail as pad')
                ->where('pad.PurchaserID', $purchaser->PrivatePurchaserId)
                ->where('pad.IsDeleted', 0)
                ->where('pad.IsActive', 1)
                ->orderByDesc('pad.CreatedDate')
                ->first();

            if ($auction) {
                $flatCost = (float) $auction->FlatCost;
            }
        }

        if ($auction) {
            $paymentDetails = $this->getPaymentDetails(
                $auction->AssetId,
                $flatCost,
                (float) ($auction->ReceivedAmount ?? 0)
            );
            $paymentSummary = $this->resolvePaymentSummary(
                $flatCost,
                (float) ($auction->ReceivedAmount ?? 0),
                (float) ($auction->BalanceAmount ?? 0),
                $paymentDetails
            );
            
            $totalPaid = (float) $paymentSummary['totalPaid'];
            $outstanding = $paymentSummary['outstanding'];

            $firstUnpaid = collect($paymentDetails['installments'])->first(function ($inst) {
                return in_array($inst->status, ['overdue', 'partial', 'upcoming']);
            });

            $firstUnpaidInstNumber = $firstUnpaid ? (int)$firstUnpaid->installment_number : null;
            $isLastInstallment = ($firstUnpaidInstNumber === 36);

            $maxAllowedAmount = max(0.0, 100000.00 - $totalPaid);

            if ($maxAllowedAmount <= 0) {
                return redirect()->route('citizen.payment-status')
                    ->with('warning', 'You have already completed the maximum allowed payment of ₹1,00,000.');
            }

            if ($isLastInstallment) {
                $amountToPay = $maxAllowedAmount;
            } else {
                if ($firstUnpaid) {
                    $amountToPay = min($firstUnpaid->total_due, $maxAllowedAmount);
                } else {
                    $amountToPay = min($outstanding, $maxAllowedAmount);
                }
            }
        }

        $submittedAt = $purchaser?->CreateDate ? Carbon::parse($purchaser->CreateDate) : null;
        $applicationNo = $purchaser?->ApplicationNo;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.($submittedAt?->format('Y') ?? now()->format('Y')).'-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');

        return view('payment.pay', [
            'pageTitle' => 'Secure Payment',
            'displayName' => $user->name ?? 'Citizen',
            'applicationId' => $applicationId,
            'applicantName' => $purchaser->PrivatePurchaserName ?? ($user->name ?? 'Citizen'),
            'plotNumber' => $purchaser->Flat_Id ?? '—',
            'mobile' => $user->mobile ?? ($purchaser->MobileNo ?? '—'),
            'email' => $user->email ?? 'citizen@example.com',
            'amountToPay' => number_format($amountToPay, 2, '.', ''),
            'amountRaw' => round($amountToPay),
            'merchantOrderId' => 'MMSAY-ORD-'.now()->format('YmdHis'),
            'assetId' => $auction?->AssetId ?? 0,
            'isLastInstallment' => $isLastInstallment,
            'maxAllowedAmount' => $maxAllowedAmount,
        ]);
    }

    public function paySubmit(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $purchaser = $this->findPurchaserForUser($user);

        $assetId = (int)$request->input('asset_id', 0);
        $amountRaw = (float)$request->input('amount_raw', '100');

        // Fetch properties and verify limits
        $auction = null;
        $flatCost = 0.0;
        if ($purchaser) {
            $auction = DB::table('property_auction_detail as pad')
                ->where('pad.AssetId', $assetId)
                ->where('pad.PurchaserID', $purchaser->PrivatePurchaserId)
                ->where('pad.IsDeleted', 0)
                ->where('pad.IsActive', 1)
                ->first();
            if ($auction) {
                $flatCost = (float)$auction->FlatCost;
            }
        }

        if ($auction) {
            $paymentDetails = $this->getPaymentDetails(
                $auction->AssetId,
                $flatCost,
                (float) ($auction->ReceivedAmount ?? 0)
            );
            $paymentSummary = $this->resolvePaymentSummary(
                $flatCost,
                (float) ($auction->ReceivedAmount ?? 0),
                (float) ($auction->BalanceAmount ?? 0),
                $paymentDetails
            );
            
            $totalPaid = (float) $paymentSummary['totalPaid'];
            $maxAllowedAmount = max(0.0, 100000.00 - $totalPaid);

            if ($maxAllowedAmount <= 0) {
                return redirect()->route('citizen.payment-status')
                    ->with('error', 'You have already reached the ₹1,00,000 maximum payment limit.');
            }

            $firstUnpaid = collect($paymentDetails['installments'])->first(function ($inst) {
                return in_array($inst->status, ['overdue', 'partial', 'upcoming']);
            });

            $firstUnpaidInstNumber = $firstUnpaid ? (int)$firstUnpaid->installment_number : null;
            $isLastInstallment = ($firstUnpaidInstNumber === 36);

            if ($isLastInstallment) {
                // Force exactly $maxAllowedAmount
                $amountRaw = $maxAllowedAmount;
            } else {
                if ($amountRaw > $maxAllowedAmount) {
                    return redirect()->back()
                        ->with('error', 'Payment amount cannot exceed the ₹1,00,000 total limit. Maximum allowed amount is ₹' . number_format($maxAllowedAmount, 2));
                }
            }
        }

        $amount = number_format((float)$amountRaw, 2, '.', '');
        
        $merchantTxnNo = 'MMSAY-TXN-' . now()->format('YmdHis') . rand(100, 999);
        $currencyCode = '356'; // INR
        $payType = '0';
        $customerEmailID = $user->email ?? 'test@gmail.com';
        $transactionType = 'SALE';
        $returnURL = route('citizen.payment.callback');
        $txnDate = now()->format('YmdHis');
        $customerMobileNo = $user->mobile ?? ($purchaser->MobileNo ?? '9146153247');
        
        // Strip out any non-alphanumeric chars for name
        $customerName = preg_replace('/[^A-Za-z0-9 ]/', '', $purchaser->PrivatePurchaserName ?? ($user->name ?? 'Citizen'));
        
        // Context parameters to help preserve state in webhook/callback
        $addlParam1 = (string)$assetId;
        $addlParam2 = (string)$user->id;

        $params = [
            'merchantId' => config('services.phicommerce.mid', 'T_88882'),
            'merchantTxnNo' => $merchantTxnNo,
            'amount' => $amount,
            'currencyCode' => $currencyCode,
            'payType' => $payType,
            'customerEmailID' => $customerEmailID,
            'transactionType' => $transactionType,
            'returnURL' => $returnURL,
            'txnDate' => $txnDate,
            'customerMobileNo' => $customerMobileNo,
            'customerName' => $customerName,
            'addlParam1' => $addlParam1,
            'addlParam2' => $addlParam2,
        ];

        // Sort keys case-sensitively (ASCII order)
        ksort($params);

        // Concatenate values
        $hashText = '';
        foreach ($params as $k => $v) {
            if (is_bool($v)) {
                $hashText .= $v ? 'true' : 'false';
            } else {
                $hashText .= (string)$v;
            }
        }

        $secretKey = config('services.phicommerce.secret_key', 'abc');
        $secureHash = hash_hmac('sha256', $hashText, $secretKey);
        
        $params['secureHash'] = $secureHash;

        // Log transaction initiation in DB
        DB::table('payment_transactions')->insert([
            'user_id' => $user->id,
            'asset_id' => $assetId,
            'merchant_txn_no' => $merchantTxnNo,
            'amount' => (float)$amountRaw,
            'status' => 'PENDING',
            'request_payload_dump' => json_encode($params),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Initiating Phicommerce Payment request', [
            'url' => config('services.phicommerce.url'),
            'txn' => $merchantTxnNo,
            'hashText' => $hashText,
            'secureHash' => $secureHash
        ]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'secureHash' => $secureHash
            ])->post(config('services.phicommerce.url'), $params);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['redirectURI']) && isset($data['tranCtx'])) {
                    $redirectUrl = $data['redirectURI'] . '?tranCtx=' . $data['tranCtx'];
                    return redirect()->away($redirectUrl);
                }
                
                Log::error('Phicommerce response missing redirectURI or tranCtx', ['response' => $data]);
                return redirect()->back()->with('error', 'Unable to initiate gateway redirection.');
            }

            Log::error('Phicommerce initiation API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return redirect()->back()->with('error', 'Gateway error. Please try again later.');

        } catch (\Exception $e) {
            Log::error('Phicommerce request exception', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Payment gateway connection failed.');
        }
    }

    public function payCallback(Request $request): RedirectResponse
    {
        Log::info('Phicommerce Payment Callback received', $request->all());

        $responseParams = $request->all();

        if (!$responseParams || !isset($responseParams['secureHash'])) {
            Log::error('Phicommerce callback secureHash missing', ['params' => $responseParams]);
            return redirect()->route('citizen.payment-status')->with('error', 'Invalid callback hash.');
        }

        $receivedHash = $responseParams['secureHash'];
        unset($responseParams['secureHash']);

        // Sort keys case-sensitively
        ksort($responseParams);

        // Concatenate values
        $hashText = '';
        foreach ($responseParams as $k => $v) {
            if (is_bool($v)) {
                $hashText .= $v ? 'true' : 'false';
            } else {
                $hashText .= (string)$v;
            }
        }

        $secretKey = config('services.phicommerce.secret_key', 'abc');
        $calculatedHash = hash_hmac('sha256', $hashText, $secretKey);

        if ($calculatedHash !== $receivedHash) {
            Log::error('Phicommerce Callback secureHash verification failed', [
                'calculated' => $calculatedHash,
                'received' => $receivedHash,
                'hashText' => $hashText
            ]);

            // Update log table
            $merchantTxnNo = $responseParams['merchantTxnNo'] ?? null;
            if ($merchantTxnNo) {
                DB::table('payment_transactions')
                    ->where('merchant_txn_no', $merchantTxnNo)
                    ->update([
                        'status' => 'FAIL',
                        'response_code' => $responseParams['responseCode'] ?? 'SIGNATURE_MISMATCH',
                        'response_description' => 'Payment signature verification failed.',
                        'response_payload_dump' => json_encode($request->all()),
                        'updated_at' => now(),
                    ]);
            }

            return redirect()->route('citizen.payment.result', [
                'status' => 'FAIL',
                'message' => 'Payment signature verification failed.',
                'txn_id' => $responseParams['txnID'] ?? 'N/A',
                'order_id' => $responseParams['merchantTxnNo'] ?? 'N/A',
                'amount' => '₹ ' . number_format((float)($responseParams['amount'] ?? 0), 2),
                'mode' => $responseParams['paymentMode'] ?? 'N/A',
            ]);
        }

        $status = $request->input('status');
        $isSuccess = ($status === 'SUC' || ($responseParams['responseCode'] ?? '') === '0000');

        // Update transaction log status
        $merchantTxnNo = $responseParams['merchantTxnNo'] ?? null;
        if ($merchantTxnNo) {
            DB::table('payment_transactions')
                ->where('merchant_txn_no', $merchantTxnNo)
                ->update([
                    'status' => $isSuccess ? 'SUCCESS' : 'FAIL',
                    'gateway_txn_id' => $responseParams['txnID'] ?? null,
                    'payment_id' => $responseParams['paymentID'] ?? null,
                    'payment_mode' => $responseParams['paymentMode'] ?? null,
                    'response_code' => $responseParams['responseCode'] ?? null,
                    'response_description' => $responseParams['respDescription'] ?? null,
                    'response_payload_dump' => json_encode($request->all()),
                    'updated_at' => now(),
                ]);
        }

        if ($isSuccess) {
            $assetId = (int)($responseParams['addlParam1'] ?? 0);
            $userId = (int)($responseParams['addlParam2'] ?? 0);
            
            DB::beginTransaction();
            try {
                // Fetch property registration details to populate cash receipt correctly
                $property = DB::table('property_registration')
                    ->where('AssetId', $assetId)
                    ->first();

                if ($property) {
                    $maxId = DB::table('cash_receipt_details')->max('id') ?? 0;
                    $newId = $maxId + 1;

                    DB::table('cash_receipt_details')->insert([
                        'id' => $newId,
                        'asset_number' => $assetId,
                        'total_paid_amount' => (float)$responseParams['amount'],
                        'receipt_number' => $responseParams['paymentID'] ?? ($responseParams['txnID'] ?? $responseParams['merchantTxnNo']),
                        'BranchId' => $property->BranchId,
                        'DistrictId' => $property->DistrictId,
                        'CityId' => $property->CityId,
                        'SectorId' => $property->SectorId,
                        'IsActive' => 1,
                        'IsDeleted' => 0,
                        'created_date' => Carbon::now(),
                        'CreatedBy' => $userId ?: null,
                        'CompanyId' => $property->CompanyId ?? 544,
                    ]);
                }

                DB::commit();
                Log::info('Payment successfully registered in database', ['txn' => $responseParams['txnID']]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error storing cash receipt in callback', ['message' => $e->getMessage()]);
            }

            return redirect()->route('citizen.payment.result', [
                'status' => 'SUCCESS',
                'txn_id' => $responseParams['txnID'] ?? 'N/A',
                'order_id' => $responseParams['merchantTxnNo'] ?? 'N/A',
                'amount' => '₹ ' . number_format((float)$responseParams['amount'], 2),
                'mode' => $responseParams['paymentMode'] ?? 'N/A',
                'date' => Carbon::parse($responseParams['paymentDateTime'] ?? now())->format('d M Y H:i:s'),
            ]);
        } else {
            return redirect()->route('citizen.payment.result', [
                'status' => 'FAIL',
                'message' => $responseParams['respDescription'] ?? ($request->input('respDescription') ?? 'Transaction Failed'),
                'txn_id' => $responseParams['txnID'] ?? 'N/A',
                'order_id' => $responseParams['merchantTxnNo'] ?? 'N/A',
                'amount' => '₹ ' . number_format((float)($responseParams['amount'] ?? 0), 2),
                'mode' => $responseParams['paymentMode'] ?? 'N/A',
            ]);
        }
    }

    public function result(Request $request): View
    {
        $user = Auth::user();
        
        $paymentResult = null;
        if ($request->has('status')) {
            $paymentResult = [
                'status' => $request->query('status'),
                'txn_id' => $request->query('txn_id'),
                'order_id' => $request->query('order_id'),
                'amount' => $request->query('amount'),
                'mode' => $request->query('mode'),
                'message' => $request->query('message'),
                'date' => $request->query('date'),
            ];
        } else {
            $paymentResult = session('payment_result');
        }

        if (!$paymentResult) {
            // Fallback for demo or direct visit
            $paymentResult = [
                'status' => 'DEMO',
                'txn_id' => 'TXN' . now()->format('YmdHis'),
                'order_id' => 'MMSAY-DEMO-001',
                'amount' => '₹ 2,222.00',
                'mode' => 'UPI',
                'applicant' => $user->name ?? 'Citizen',
            ];
        }

        return view('payment.result', [
            'pageTitle' => 'Payment Status Details',
            'displayName' => $user->name ?? 'Citizen',
            'applicationId' => 'HR-MMSAY-2024-211707',
            'payment' => $paymentResult,
        ]);
    }

    public function reconcile($id): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $tx = DB::table('payment_transactions')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$tx) {
            return redirect()->back()->with('error', 'Transaction not found.');
        }

        if ($tx->status !== 'PENDING') {
            return redirect()->back()->with('warning', 'This transaction status is already finalized.');
        }

        // Prepare request parameters for the command API
        $amountStr = number_format((float)$tx->amount, 2, '.', '');
        $mid = config('services.phicommerce.mid', 'T_88882');
        $secretKey = config('services.phicommerce.secret_key', 'abc');
        
        $params = [
            'amount' => $amountStr,
            'merchantID' => $mid,
            'merchantTxnNo' => $tx->merchant_txn_no,
            'originalTxnNo' => $tx->merchant_txn_no,
            'transactionType' => 'STATUS',
        ];

        // Sort keys case-sensitively (ASCII order)
        ksort($params);

        // Concatenate values
        $hashText = implode('', $params);
        $secureHash = hash_hmac('sha256', $hashText, $secretKey);
        
        $params['secureHash'] = $secureHash;

        Log::info('Reconciling payment transaction via Status Check API', [
            'txn_id' => $tx->id,
            'merchant_txn_no' => $tx->merchant_txn_no,
            'amount' => $amountStr
        ]);

        try {
            $response = Http::asForm()->post(config('services.phicommerce.command_url', 'https://uat.stage.phicommerce.com/pg/api/command'), $params);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Phicommerce Status Check API response received', ['response' => $data]);

                // Check for failure case: Before transaction hits payphi or has been cancelled
                $responseCode = $data['responseCode'] ?? '';
                $txnStatus = $data['txnStatus'] ?? '';
                $txnResponseCode = $data['txnResponseCode'] ?? '';
                $respDescription = $data['respDescription'] ?? '';
                $txnRespDescription = $data['txnRespDescription'] ?? '';

                if ($responseCode === 'P0039') {
                    return redirect()->back()->with('error', 'Transaction was not initiated with the gateway.');
                }

                if ($responseCode === 'P0020' || $txnStatus === 'REJ' || $txnResponseCode === '039') {
                    // Update to FAIL
                    DB::table('payment_transactions')
                        ->where('id', $tx->id)
                        ->update([
                            'status' => 'FAIL',
                            'response_code' => $txnResponseCode ?: $responseCode,
                            'response_description' => $txnRespDescription ?: ($respDescription ?: 'Transaction Cancelled/Rejected'),
                            'response_payload_dump' => json_encode($data),
                            'updated_at' => now(),
                        ]);

                    return redirect()->back()->with('error', 'Payment check completed. The transaction was cancelled or rejected by the gateway.');
                }

                if ($txnStatus === 'SUC' && $txnResponseCode === '0000') {
                    // Transaction is successful! Reconcile and create cash receipt
                    $assetId = $tx->asset_id;
                    $userId = $tx->user_id;

                    DB::beginTransaction();
                    try {
                        // Check if cash receipt was already created for this receipt to avoid double entries
                        $receiptNo = $data['paymentID'] ?? ($data['txnID'] ?? $tx->merchant_txn_no);
                        $existingReceipt = DB::table('cash_receipt_details')
                            ->where('receipt_number', $receiptNo)
                            ->first();

                        if (!$existingReceipt) {
                            $property = DB::table('property_registration')
                                ->where('AssetId', $assetId)
                                ->first();

                            if ($property) {
                                $maxId = DB::table('cash_receipt_details')->max('id') ?? 0;
                                $newId = $maxId + 1;

                                DB::table('cash_receipt_details')->insert([
                                    'id' => $newId,
                                    'asset_number' => $assetId,
                                    'total_paid_amount' => (float)$tx->amount,
                                    'receipt_number' => $receiptNo,
                                    'BranchId' => $property->BranchId,
                                    'DistrictId' => $property->DistrictId,
                                    'CityId' => $property->CityId,
                                    'SectorId' => $property->SectorId,
                                    'IsActive' => 1,
                                    'IsDeleted' => 0,
                                    'created_date' => Carbon::now(),
                                    'CreatedBy' => $userId ?: null,
                                    'CompanyId' => $property->CompanyId ?? 544,
                                ]);
                            }
                        }

                        DB::table('payment_transactions')
                            ->where('id', $tx->id)
                            ->update([
                                'status' => 'SUCCESS',
                                'gateway_txn_id' => $data['txnID'] ?? null,
                                'payment_id' => $data['paymentID'] ?? null,
                                'payment_mode' => $data['paymentMode'] ?? null,
                                'response_code' => $txnResponseCode,
                                'response_description' => $txnRespDescription ?: 'Transaction successful',
                                'response_payload_dump' => json_encode($data),
                                'updated_at' => now(),
                            ]);

                        DB::commit();
                        return redirect()->back()->with('success', 'Success! Transaction status resolved, and cash receipt has been generated.');

                    } catch (\Exception $ex) {
                        DB::rollBack();
                        Log::error('Reconcile DB update exception', ['message' => $ex->getMessage()]);
                        return redirect()->back()->with('error', 'Error generating cash receipt.');
                    }
                }

                // If still pending (P0030 / Awaiting user action / REQ)
                return redirect()->back()->with('warning', 'This transaction is still awaiting action from the user or bank.');
            }

            Log::error('Phicommerce status check API failed response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return redirect()->back()->with('error', 'Gateway status check server returned an error.');

        } catch (\Exception $e) {
            Log::error('Phicommerce reconcile API check exception', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Connection to gateway failed.');
        }
    }

    // Helper methods for fetching user-specific outstanding dues (from CitizenAuthController)
    private function findPurchaserForUser(User $user): ?object
    {
        if ($user->private_purchaser_id) {
            return DB::table('property_private_purchasers as ppp')
                ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                ->where('ppp.PrivatePurchaserId', $user->private_purchaser_id)
                ->select('ppp.*', 'd.DistrictName')
                ->first();
        }

        $mobile = $user->mobile;
        if (!$mobile) {
            return null;
        }

        $variants = array_unique([
            $mobile,
            '91' . $mobile,
            (int) $mobile,
        ]);

        return DB::table('property_private_purchasers as ppp')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->where('ppp.IsActive', 1)
            ->where('ppp.IsDeleted', 0)
            ->where(function ($query) use ($variants, $mobile) {
                $query->whereIn('ppp.MobileNo', $variants)
                    ->orWhereRaw('RIGHT(CAST(ppp.MobileNo AS CHAR), 10) = ?', [$mobile]);
            })
            ->select('ppp.*', 'd.DistrictName')
            ->orderBy('ppp.PrivatePurchaserId')
            ->first();
    }

    private function getPaymentDetails(?int $assetId, float $flatCost = 0.0, float $receivedAmount = 0.0): array
    {
        if (!$assetId) {
            return [
                'installments' => collect(),
                'receipts' => collect(),
                'installmentStats' => ['total' => 0, 'paid' => 0, 'overdue' => 0, 'upcoming' => 0],
                'installmentPaidTotal' => 0.0,
            ];
        }

        $ledgerByNumber = DB::table('ledger')
            ->where('AssetId', $assetId)
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->get()
            ->keyBy('InstallmentNumber');

        $installmentRows = DB::table('installment_due')
            ->where('AssetId', $assetId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderBy('InstallmentNumber')
            ->get();

        $lastInstallmentNumber = (int) $installmentRows->max('InstallmentNumber');
        $lastInstallmentTargetEmi = 0.0;
        
        if ($installmentRows->isNotEmpty()) {
            $firstInstallmentsEmiSum = $installmentRows->reject(function ($row) use ($lastInstallmentNumber) {
                return (int) $row->InstallmentNumber === $lastInstallmentNumber;
            })->sum('EMIAmount');
            $lastInstallmentTargetEmi = max(0.0, $flatCost - $receivedAmount - $firstInstallmentsEmiSum);
        }

        $paymentPool = $this->resolveInstallmentPaymentPool($assetId, $ledgerByNumber);
        $installmentPaidTotal = $paymentPool['pool'];
        
        $allocations = $this->allocateInstallmentsFromPayments(
            $installmentRows,
            $paymentPool['receiptRows'],
            $ledgerByNumber,
            $lastInstallmentTargetEmi
        );
        $remainingBalance = $this->remainingFlatBalance($flatCost, $receivedAmount, $ledgerByNumber);

        $installments = $installmentRows->map(function ($row) use (
            $ledgerByNumber,
            $remainingBalance,
            $lastInstallmentNumber,
            $allocations,
            $lastInstallmentTargetEmi
        ) {
            $ledger = $ledgerByNumber->get($row->InstallmentNumber);
            $dueDate = Carbon::parse($row->DueDate);
            $today = Carbon::today();
            $installmentNumber = (int) $row->InstallmentNumber;
            
            $dueAmount = $installmentNumber === $lastInstallmentNumber && $lastInstallmentTargetEmi > 0
                ? $lastInstallmentTargetEmi
                : (float) $row->DueAmount;
                
            $scheduleEmiAmount = (float) $row->EMIAmount;
            $allocation = $allocations[$installmentNumber] ?? [
                'allocated' => 0.0,
                'paid_on' => null,
                'first_payment_on' => null,
                'last_payment_on' => null,
            ];
            $allocated = (float) $allocation['allocated'];

            $status = $this->resolveInstallmentStatus(
                $allocated,
                $dueAmount,
                $installmentNumber === $lastInstallmentNumber && $lastInstallmentTargetEmi > 0 ? $lastInstallmentTargetEmi : $scheduleEmiAmount,
                $installmentNumber === $lastInstallmentNumber,
                $dueDate,
                $today
            );

            $paidOn = match ($status) {
                'paid' => $allocation['paid_on'] ?? $allocation['first_payment_on'] ?? $allocation['last_payment_on'],
                'partial' => $allocation['first_payment_on'] ?? $allocation['last_payment_on'],
                default => null,
            };

            if ($status === 'paid' && !$paidOn && $row->LastSettledDate) {
                $paidOn = Carbon::parse($row->LastSettledDate);
            }

            $emiAmount = $this->emiAmountForInstallment(
                $row,
                $ledger,
                $remainingBalance,
                $lastInstallmentNumber,
                $status === 'paid',
                $lastInstallmentTargetEmi
            );
            
            $totalDue = (int) $row->InstallmentNumber === $lastInstallmentNumber
                ? $emiAmount
                : (float) $row->DueAmount;

            return (object) [
                'installment_number' => (int) $row->InstallmentNumber,
                'due_date_formatted' => $dueDate->format('d M Y'),
                'emi_amount' => $emiAmount,
                'emi_formatted' => $this->formatIndianCurrency($emiAmount),
                'principal' => (float) $row->PrincipleAmount,
                'interest' => (float) $row->InterestAmount,
                'gst' => (float) $row->GSTAmount,
                'total_due' => $totalDue,
                'total_due_formatted' => $this->formatIndianCurrency($totalDue),
                'balance_after' => (float) $row->RunningClosingBalance,
                'balance_after_formatted' => $this->formatIndianCurrency((float) $row->RunningClosingBalance),
                'paid_on_formatted' => $paidOn?->format('d M Y') ?? '—',
                'status' => $status,
            ];
        });

        return [
            'installments' => $installments,
            'installmentPaidTotal' => $installmentPaidTotal,
        ];
    }

    private function resolveInstallmentPaymentPool(int $assetId, $ledgerByNumber): array
    {
        $receiptRows = DB::table('cash_receipt_details')
            ->where('asset_number', $assetId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderBy('created_date')
            ->get(['total_paid_amount', 'created_date']);

        $receiptTotal = (float) $receiptRows->sum(fn ($row) => (float) $row->total_paid_amount);
        $ledgerTotal = (float) $ledgerByNumber->sum(fn ($row) => (float) $row->Payment);
        $pool = $receiptTotal > 0 ? $receiptTotal : $ledgerTotal;

        return [
            'pool' => $pool,
            'receiptRows' => $receiptRows,
        ];
    }

    private function allocateInstallmentsFromPayments(
        $installmentRows,
        $receiptRows,
        $ledgerByNumber,
        float $lastInstallmentTargetEmi = 0.0
    ): array {
        $chunks = [];

        if ($receiptRows->isNotEmpty()) {
            foreach ($receiptRows as $receipt) {
                $amount = (float) $receipt->total_paid_amount;
                if ($amount <= 0) continue;

                $chunks[] = [
                    'remaining' => $amount,
                    'date' => $receipt->created_date ? Carbon::parse($receipt->created_date) : null,
                ];
            }
        } else {
            foreach ($ledgerByNumber->sortKeys() as $ledger) {
                $amount = (float) $ledger->Payment;
                if ($amount <= 0) continue;

                $chunks[] = [
                    'remaining' => $amount,
                    'date' => $ledger->CreateDate ? Carbon::parse($ledger->CreateDate) : null,
                ];
            }
        }

        $chunkIndex = 0;
        $allocations = [];
        $lastInstallmentNumber = (int) $installmentRows->max('InstallmentNumber');

        foreach ($installmentRows as $row) {
            $installmentNumber = (int) $row->InstallmentNumber;
            $dueAmount = $this->installmentAllocationAmount($row, $lastInstallmentNumber, $lastInstallmentTargetEmi);
            $allocated = 0.0;
            $paidOn = null;
            $firstPaymentOn = null;
            $lastPaymentOn = null;

            while ($allocated < ($dueAmount - 0.01) && $chunkIndex < count($chunks)) {
                $need = $dueAmount - $allocated;
                $take = min($chunks[$chunkIndex]['remaining'], $need);

                if ($take > 0) {
                    $allocated += $take;
                    $chunks[$chunkIndex]['remaining'] -= $take;

                    if ($chunks[$chunkIndex]['date']) {
                        if ($firstPaymentOn === null) {
                            $firstPaymentOn = $chunks[$chunkIndex]['date'];
                        }
                        $lastPaymentOn = $chunks[$chunkIndex]['date'];
                    }

                    if ($allocated >= ($dueAmount - 0.01) && $chunks[$chunkIndex]['date']) {
                        $paidOn = $firstPaymentOn ?? $chunks[$chunkIndex]['date'];
                    }
                }

                if ($chunks[$chunkIndex]['remaining'] <= 0.01) {
                    $chunkIndex++;
                } elseif ($take <= 0) {
                    break;
                }
            }

            $allocations[$installmentNumber] = [
                'allocated' => $allocated,
                'paid_on' => $paidOn,
                'first_payment_on' => $firstPaymentOn,
                'last_payment_on' => $lastPaymentOn,
            ];
        }

        return $allocations;
    }

    private function installmentAllocationAmount(object $row, int $lastInstallmentNumber, float $lastInstallmentTargetEmi = 0.0): float
    {
        if ((int) $row->InstallmentNumber === $lastInstallmentNumber) {
            return $lastInstallmentTargetEmi > 0 ? $lastInstallmentTargetEmi : (float) $row->DueAmount;
        }

        $emiAmount = (float) $row->EMIAmount;
        return $emiAmount > 0 ? $emiAmount : (float) $row->DueAmount;
    }

    private function resolveInstallmentStatus(
        float $allocated,
        float $dueAmount,
        float $emiAmount,
        bool $isLastInstallment,
        Carbon $dueDate,
        Carbon $today
    ): string {
        $target = $isLastInstallment ? $dueAmount : ($emiAmount > 0 ? $emiAmount : $dueAmount);

        if ($isLastInstallment && $emiAmount > 0 && $allocated >= ($emiAmount - 0.01)) {
            return 'paid';
        }

        if ($target > 0 && $allocated >= ($target - 0.01)) {
            return 'paid';
        }

        if ($dueDate->lt($today)) {
            return $allocated > 0 ? 'partial' : 'overdue';
        }

        return 'upcoming';
    }

    private function remainingFlatBalance(float $flatCost, float $receivedAmount, $ledgerRows): float
    {
        if ($flatCost <= 0) {
            return 0.0;
        }
        $installmentPayments = (float) collect($ledgerRows)->sum(fn ($row) => (float) $row->Payment);
        return max(0.0, round($flatCost - $receivedAmount - $installmentPayments, 2));
    }

    private function emiAmountForInstallment(
        $row,
        $ledger,
        float $remainingBalance,
        int $lastInstallmentNumber,
        bool $isPaid,
        float $lastInstallmentTargetEmi = 0.0
    ): float {
        if ((int) $row->InstallmentNumber === $lastInstallmentNumber) {
            if ($isPaid) {
                return $ledger ? (float) $ledger->Payment : $lastInstallmentTargetEmi;
            }
            return $remainingBalance;
        }
        return (float) $row->EMIAmount;
    }

    private function resolvePaymentSummary(
        float $flatCost,
        float $fallbackPaid,
        float $fallbackOutstanding,
        array $paymentDetails
    ): array {
        $installments = $paymentDetails['installments'];

        if ($installments->isEmpty()) {
            $totalPaid = $fallbackPaid;
            $outstanding = $fallbackOutstanding;
        } else {
            $installmentPayments = (float) ($paymentDetails['installmentPaidTotal'] ?? 0);
            $totalPaid = $fallbackPaid + $installmentPayments;
            $outstanding = $flatCost > 0 ? max(0.0, $flatCost - $totalPaid) : 0.0;
        }

        $paymentProgress = $flatCost > 0 ? (int) min(100, round(($totalPaid / $flatCost) * 100)) : 0;

        return [
            'totalPaid' => $totalPaid,
            'outstanding' => $outstanding,
            'paymentProgress' => $paymentProgress,
        ];
    }

    private function formatIndianCurrency(float $amount): string
    {
        if ($amount <= 0) {
            return '₹ 0';
        }
        $rounded = (int) round($amount);
        $lastThree = substr((string) $rounded, -3);
        $rest = substr((string) $rounded, 0, -3);
        if ($rest !== '') {
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
        }
        return '₹ ' . ($rest ? $rest . ',' : '') . $lastThree;
    }
}
