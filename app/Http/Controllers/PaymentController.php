<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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

        return view('payment.pay', [
            'pageTitle' => 'Secure Payment',
            'displayName' => $user->name ?? 'Citizen',
            'applicationId' => 'HR-MMSAY-2024-211707',
            'applicantName' => $user->name ?? 'SANDEEP',
            'plotNumber' => 'JAJ_E-77',
            'mobile' => $user->mobile ?? '8569895157',
            'email' => $user->email ?? 'citizen@example.com',
            'amountToPay' => '2,222.00',
            'amountRaw' => '2222',
            'merchantOrderId' => 'MMSAY-ORD-'.now()->format('YmdHis'),
        ]);
    }

    public function paySubmit(Request $request): RedirectResponse
    {
        $mode = $request->input('payment_mode', 'UPI');

        session([
            'demo_payment' => [
                'txn_id' => 'TXN'.now()->format('YmdHis').rand(100, 999),
                'order_id' => 'MMSAY-ORD-'.now()->format('YmdHis'),
                'amount' => '₹ 2,222.00',
                'mode' => $mode,
                'applicant' => $request->input('applicant_name', 'Citizen'),
            ],
        ]);

        return redirect()->route('citizen.payment.result');
    }

    public function result(): View
    {
        $user = Auth::user();
        $payment = session('demo_payment', [
            'txn_id' => 'TXN'.now()->format('YmdHis'),
            'order_id' => 'MMSAY-DEMO-001',
            'amount' => '₹ 2,222.00',
            'mode' => 'UPI',
            'applicant' => $user->name ?? 'Citizen',
        ]);

        return view('payment.result', [
            'pageTitle' => 'Payment Processing',
            'displayName' => $user->name ?? 'Citizen',
            'applicationId' => 'HR-MMSAY-2024-211707',
            'payment' => $payment,
        ]);
    }
}
