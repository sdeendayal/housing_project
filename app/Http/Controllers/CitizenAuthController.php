<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CitizenAuthController extends Controller
{
    // OTP settings
    private const OTP_EXPIRY_MINUTES = 10;
    private const OTP_RESEND_COOLDOWN_SECONDS = 60;
    private const OTP_MAX_ATTEMPTS = 5;

    // Step 1: Show mobile number + captcha page
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->belongsToRoleGroup('citizen')) {
            return redirect()->intended('/mmsay/citizen/dashboard');
        }

        $captcha = rand(1000, 9999);
        session(['captcha' => $captcha]);

        return view('mmsay.citizenLogin', compact('captcha'));
    }

    // Step 2: Validate mobile + captcha, generate OTP, go to verify page
    public function sendOtp(SendOtpRequest $request)
    {
        if ($request->captcha != session('captcha')) {
            return back()->withInput()->with('error', 'Invalid captcha. Please try again.');
        }

        $mobile = $request->mobile;

        $user = User::where('mobile', $mobile)->first();
        if (! $user || ! $user->belongsToRoleGroup('citizen')) {
            return back()->withInput()->with('error', 'Mobile number is not registered as a citizen account.');
        }

        // Resend cooldown check
        $latestOtp = Otp::where('mobile_number', $mobile)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($latestOtp && $this->isOtpResendOnCooldown($latestOtp)) {
            $waitSeconds = $this->remainingOtpCooldownSeconds($latestOtp);

            return back()->withInput()->with('error', "Please wait {$waitSeconds} seconds before requesting a new OTP.");
        }

        try {
            DB::transaction(function () use ($mobile) {
                // Invalidate all previous active OTPs for this mobile
                Otp::where('mobile_number', $mobile)
                    ->whereNull('verified_at')
                    ->where('expires_at', '>', now())
                    ->update(['verified_at' => now()]);

                $otpCode = app()->environment('local')
                    ? '111111'
                    : str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                Otp::create([
                    'mobile_number' => $mobile,
                    'otp' => $otpCode,
                    'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
                    'verified_at' => null,
                    'attempts' => 0,
                ]);

                Log::info('Citizen OTP generated', ['mobile' => $mobile]);

                if (app()->environment('local')) {
                    Log::info('Local OTP for testing', ['mobile' => $mobile, 'otp' => $otpCode]);
                }
            });
        } catch (\Exception $e) {
            Log::error('Citizen OTP generation failed', ['mobile' => $mobile, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Unable to send OTP. Please try again.');
        }

        // Store mobile in session for the OTP verification page
        session(['citizen_login_mobile' => $mobile]);

        return redirect()->route('citizen.login.verify-page')
            ->with('success', 'OTP sent successfully to your mobile number.');
    }

    // Step 3: Show OTP verification page
    public function showVerifyOtp()
    {
        $mobile = session('citizen_login_mobile');

        if (! $mobile) {
            return redirect()->route('citizen.login')
                ->with('error', 'Please enter your mobile number first.');
        }

        return view('mmsay.citizenOtpVerify', compact('mobile'));
    }

    // Step 4: Verify OTP and login citizen
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $mobile = session('citizen_login_mobile');

        if (! $mobile) {
            return redirect()->route('citizen.login')
                ->with('error', 'Session expired. Please start again.');
        }

        $user = User::where('mobile', $mobile)->first();
        if (! $user || ! $user->belongsToRoleGroup('citizen')) {
            return redirect()->route('citizen.login')
                ->with('error', 'Mobile number is not registered as a citizen account.');
        }

        $otpRecord = Otp::where('mobile_number', $mobile)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otpRecord) {
            return back()->with('error', 'No active OTP found. Please request a new OTP.');
        }

        if ($otpRecord->isExpired()) {
            $otpRecord->update(['verified_at' => now()]);

            return back()->with('error', 'OTP has expired. Please request a new OTP.');
        }

        if ($otpRecord->attempts >= self::OTP_MAX_ATTEMPTS) {
            $otpRecord->update(['verified_at' => now()]);

            return back()->with('error', 'Too many failed attempts. Please request a new OTP.');
        }

        if ($request->otp !== $otpRecord->otp) {
            $otpRecord->increment('attempts');

            Log::warning('Citizen OTP verification failed', [
                'mobile' => $mobile,
                'attempts' => $otpRecord->attempts,
            ]);

            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        try {
            DB::transaction(function () use ($mobile, $user, $request) {
                Otp::where('mobile_number', $mobile)->delete();

                Auth::login($user);
                $request->session()->regenerate();

                session()->forget('citizen_login_mobile');
            });

            Log::info('Citizen logged in via OTP', ['user_id' => $user->id, 'mobile' => $mobile]);
        } catch (\Exception $e) {
            Log::error('Citizen login failed', ['mobile' => $mobile, 'error' => $e->getMessage()]);

            return back()->with('error', 'Login failed. Please try again.');
        }

        return redirect()->intended('/mmsay/citizen/dashboard')
            ->with('success', 'Login successful! Welcome back.');
    }

    // Resend OTP from verification page (no captcha needed — mobile already verified in step 1)
    public function resendOtp(Request $request)
    {
        $mobile = session('citizen_login_mobile');

        if (! $mobile) {
            return redirect()->route('citizen.login')
                ->with('error', 'Session expired. Please start again.');
        }

        $user = User::where('mobile', $mobile)->first();
        if (! $user || ! $user->belongsToRoleGroup('citizen')) {
            return redirect()->route('citizen.login')
                ->with('error', 'Mobile number is not registered as a citizen account.');
        }

        $latestOtp = Otp::where('mobile_number', $mobile)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($latestOtp && $this->isOtpResendOnCooldown($latestOtp)) {
            $waitSeconds = $this->remainingOtpCooldownSeconds($latestOtp);

            return back()->with('warning', "Please wait {$waitSeconds} seconds before resending OTP.");
        }

        try {
            DB::transaction(function () use ($mobile) {
                Otp::where('mobile_number', $mobile)
                    ->whereNull('verified_at')
                    ->where('expires_at', '>', now())
                    ->update(['verified_at' => now()]);

                $otpCode = app()->environment('local')
                    ? '111111'
                    : str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                Otp::create([
                    'mobile_number' => $mobile,
                    'otp' => $otpCode,
                    'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
                    'verified_at' => null,
                    'attempts' => 0,
                ]);

                Log::info('Citizen OTP resent', ['mobile' => $mobile]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to resend OTP. Please try again.');
        }

        return back()->with('success', 'A new OTP has been sent to your mobile number.');
    }

    // Citizen dashboard — logged-in user data from DB (no helpers / view composers)
    public function dashboard(): View
    {
        $user = Auth::user();
        $purchaser = $this->findPurchaserForUser($user);

        $auction = null;
        $assetName = null;
        $totalPaid = 0.0;
        $outstanding = 0.0;
        $flatCost = 0.0;
        $purchaseDate = null;

        if ($purchaser) {
            $auction = DB::table('property_auction_detail as pad')
                ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
                ->where('pad.PurchaserID', $purchaser->PrivatePurchaserId)
                ->where('pad.IsDeleted', 0)
                ->where('pad.IsActive', 1)
                ->select('pad.*', 'pr.AssetName')
                ->orderByDesc('pad.CreatedDate')
                ->first();

            if ($auction) {
                $flatCost = (float) $auction->FlatCost;
                $totalPaid = (float) $auction->ReceivedAmount;
                $outstanding = (float) $auction->BalanceAmount;
                $assetName = $auction->AssetName;
                $purchaseDate = $auction->CreatedDate
                    ? Carbon::parse($auction->CreatedDate)
                    : null;
            }
        }

        $paymentProgress = $flatCost > 0
            ? (int) min(100, round(($totalPaid / $flatCost) * 100))
            : 0;

        $submittedAt = $purchaser?->CreateDate ? Carbon::parse($purchaser->CreateDate) : null;
        $verifiedAt = ($purchaser?->ModifiedDate && $purchaser->ModifiedDate !== $purchaser->CreateDate)
            ? Carbon::parse($purchaser->ModifiedDate)
            : ($submittedAt ? $submittedAt->copy()->addDays(16) : null);
        $allottedAt = $auction?->CreatedDate ? Carbon::parse($auction->CreatedDate) : null;

        $isAllotted = $auction !== null;
        $isFullyPaid = $isAllotted && $outstanding <= 0 && $flatCost > 0;
        $hasOutstanding = $isAllotted && $outstanding > 0;

        $statusSteps = [
            [
                'key' => 'submitted',
                'label' => 'Submitted',
                'icon' => 'check',
                'state' => $submittedAt ? 'completed' : 'pending',
                'date' => $submittedAt?->format('d M Y'),
            ],
            [
                'key' => 'verified',
                'label' => 'Verified',
                'icon' => 'verified',
                'state' => $verifiedAt ? 'completed' : 'pending',
                'date' => $verifiedAt?->format('d M Y'),
            ],
            [
                'key' => 'allotted',
                'label' => 'Allotted',
                'icon' => 'real_estate_agent',
                'state' => ! $isAllotted ? 'pending' : ($hasOutstanding ? 'active' : 'completed'),
                'date' => $hasOutstanding ? 'In Progress' : $allottedAt?->format('d M Y'),
            ],
            [
                'key' => 'payment',
                'label' => 'Pending',
                'icon' => 'pending',
                'state' => $hasOutstanding ? 'active' : ($isFullyPaid ? 'completed' : 'pending'),
                'date' => null,
            ],
            [
                'key' => 'registered',
                'label' => 'Registered',
                'icon' => 'task_alt',
                'state' => $isFullyPaid ? 'completed' : 'pending',
                'date' => null,
            ],
        ];

        $completedCount = collect($statusSteps)->where('state', 'completed')->count();
        $hasActive = collect($statusSteps)->contains('state', 'active');
        $timelineProgress = match (true) {
            $isFullyPaid => 100,
            $hasActive => min(90, 35 + ($completedCount * 12)),
            $completedCount > 0 => min(85, $completedCount * 20),
            default => 0,
        };

        $applicationNo = $purchaser?->ApplicationNo;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.($submittedAt?->format('Y') ?? now()->format('Y')).'-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');

        $displayName = $user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen');
        $flatStatus = $isAllotted
            ? ($isFullyPaid ? 'Registered' : 'Allotted')
            : ($purchaser ? 'Application Submitted' : '—');
        $category = $purchaser?->CasteCategoryName ?? '—';

        return view('mmsayCitizenDashboard', [
            'displayName' => $displayName,
            'applicationId' => $applicationId,
            'purchaseDate' => $purchaseDate?->format('d M Y') ?? '—',
            'totalPaidFormatted' => $this->formatIndianCurrency($totalPaid),
            'outstandingFormatted' => $this->formatIndianCurrency($outstanding),
            'paymentProgress' => $paymentProgress,
            'flatStatus' => $flatStatus,
            'category' => $category,
            'statusSteps' => $statusSteps,
            'timelineProgress' => $timelineProgress,
            'hasOutstanding' => $hasOutstanding,
            'assetName' => $assetName,
        ]);
    }

    // Citizen profile — logged-in user data from DB
    public function profile(): View
    {
        $user = Auth::user();
        $purchaser = $this->findPurchaserForUser($user);

        $applicationNo = $purchaser?->ApplicationNo;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');

        $fullName = strtoupper(trim($user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen')));
        $fatherName = $purchaser?->PurchaserFatherName
            ? strtoupper(trim($purchaser->PurchaserFatherName))
            : '—';
        $mobile = $user->mobile ?? ($purchaser?->MobileNo ? (string) $purchaser->MobileNo : '—');
        $aadhaarMasked = $this->maskIdentifier($purchaser?->MemberID ?? $purchaser?->PPPId);
        $category = $purchaser?->CasteCategoryName ?? '—';
        $district = $purchaser?->DistrictName ?? '—';
        $annualIncome = '—';
        $address = $purchaser?->Address ? strtoupper(trim($purchaser->Address)) : '—';

        $isActive = $purchaser
            && (int) $purchaser->IsActive === 1
            && (int) $purchaser->IsDeleted === 0
            && (int) ($purchaser->Is_UserLogin_Active ?? 1) === 1
            && (int) ($purchaser->Is_UserLogin_Deleted ?? 0) === 0;

        return view('mmsayCitizenProfile', [
            'fullName' => $fullName,
            'applicationId' => $applicationId,
            'applicationNo' => $applicationNo ? (string) $applicationNo : '—',
            'accountStatus' => $isActive ? 'Active' : 'Inactive',
            'fatherName' => $fatherName,
            'mobile' => $mobile,
            'aadhaarMasked' => $aadhaarMasked,
            'category' => $category,
            'district' => strtoupper($district),
            'annualIncome' => $annualIncome,
            'address' => $address,
        ]);
    }

    private function isOtpResendOnCooldown(Otp $latestOtp): bool
    {
        return $this->otpCooldownElapsedSeconds($latestOtp) < self::OTP_RESEND_COOLDOWN_SECONDS;
    }

    private function remainingOtpCooldownSeconds(Otp $latestOtp): int
    {
        $remaining = self::OTP_RESEND_COOLDOWN_SECONDS - $this->otpCooldownElapsedSeconds($latestOtp);

        return max(1, (int) ceil($remaining));
    }

    private function otpCooldownElapsedSeconds(Otp $latestOtp): int
    {
        return (int) $latestOtp->created_at->diffInSeconds(now());
    }

    private function findPurchaserForUser(User $user): ?object
    {
        $mobile = $user->mobile;

        if (! $mobile) {
            return null;
        }

        $variants = array_unique([
            $mobile,
            '91'.$mobile,
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

    private function maskIdentifier(?string $value): string
    {
        if (! $value || trim($value) === '') {
            return '—';
        }

        $clean = preg_replace('/\s+/', '', $value);
        $lastFour = strlen($clean) >= 4 ? substr($clean, -4) : $clean;

        return 'XXXX-XXXX-'.$lastFour;
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

        return '₹ '.($rest ? $rest.',' : '').$lastThree;
    }

    // Logout citizen
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/mmsay-citizen-login')->with('success', 'Logged out successfully.');
    }
}
