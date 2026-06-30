<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Log;

class OtpVerificationService
{
    public const EXPIRY_MINUTES = 10;

    public const RESEND_COOLDOWN_SECONDS = 60;

    public const MAX_ATTEMPTS = 5;

    private const LOGIN_PURPOSES = [
        Otp::PURPOSE_CITIZEN_LOGIN,
        Otp::PURPOSE_MMGAV_VILLAGER_LOGIN,
        Otp::PURPOSE_DEPARTMENT_LOGIN,
    ];

    private const DOCUMENT_OTP_PURPOSES = [
        Otp::PURPOSE_POSSESSION_CERTIFICATE,
        Otp::PURPOSE_ALLOTMENT_LETTER,
    ];

    public function __construct(
        private LoginOtpSmsService $loginOtpSmsService
    ) {}

    /**
     * @return array{success: bool, step?: string, message: string, resend_after?: int}
     */
    public function send(string $mobile, string $purpose, ?int $userId = null, ?string $logLabel = null): array
    {
        $cooldown = $this->resendCooldownRemaining($mobile, $purpose);

        if ($cooldown > 0) {
            return [
                'success' => false,
                'message' => "Please wait {$cooldown} seconds before requesting a new OTP.",
                'resend_after' => $cooldown,
            ];
        }

        $this->invalidateActiveOtps($mobile, $purpose);

        $otpCode = $this->generateOtpCode($mobile, $purpose);

        Otp::create([
            'mobile_number' => $mobile,
            'purpose' => $purpose,
            'user_id' => $userId,
            'otp' => $otpCode,
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
            'verified_at' => null,
            'attempts' => 0,
        ]);

        if ($logLabel) {
            Log::info("{$logLabel} OTP generated", ['mobile' => $mobile, 'purpose' => $purpose]);

            if (self::usesFixedTestOtp($mobile, $purpose)) {
                Log::info("{$logLabel} local test OTP", [
                    'mobile' => $mobile,
                    'purpose' => $purpose,
                    'otp' => $otpCode,
                ]);
            }
        }

        if (in_array($purpose, self::LOGIN_PURPOSES, true)) {
            $this->loginOtpSmsService->send($mobile, $otpCode, $logLabel);
        } elseif (in_array($purpose, self::DOCUMENT_OTP_PURPOSES, true)) {
            $this->sendDocumentOtpSms($mobile, $otpCode, $purpose, $logLabel);
        }

        return [
            'success' => true,
            'step' => 'otp_sent',
            'message' => 'OTP sent to your registered mobile number ending with '.substr($mobile, -4).'.',
            'resend_after' => self::RESEND_COOLDOWN_SECONDS,
        ];
    }

    /**
     * @return array{success: bool, step?: string, message: string, resend_after?: int}
     */
    public function resend(string $mobile, string $purpose, ?int $userId = null, ?string $logLabel = null): array
    {
        return $this->send($mobile, $purpose, $userId, $logLabel ? "{$logLabel} resent" : null);
    }

    /**
     * @return array{success: true, otp: Otp}|array{success: false, message: string}
     */
    public function verify(string $mobile, string $purpose, string $otp): array
    {
        $otpRecord = Otp::where('mobile_number', $mobile)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otpRecord || $otpRecord->isExpired()) {
            $otpRecord?->delete();

            return [
                'success' => false,
                'message' => 'OTP has expired. Please request a new OTP.',
            ];
        }

        if ($otpRecord->attempts >= self::MAX_ATTEMPTS) {
            $otpRecord->delete();

            return [
                'success' => false,
                'message' => 'Too many wrong attempts. Please request a new OTP.',
            ];
        }

        if ($otp !== $otpRecord->otp) {
            $otpRecord->increment('attempts');

            return [
                'success' => false,
                'message' => 'Invalid OTP. Please try again.',
            ];
        }

        $otpRecord->delete();

        return [
            'success' => true,
        ];
    }

    public function deleteVerifiedForPurpose(string $mobile, string $purpose): void
    {
        Otp::where('mobile_number', $mobile)
            ->where('purpose', $purpose)
            ->delete();
    }

    public function resendCooldownRemaining(string $mobile, string $purpose): int
    {
        $latestOtp = Otp::where('mobile_number', $mobile)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $latestOtp) {
            return 0;
        }

        $elapsedSeconds = (int) $latestOtp->created_at->diffInSeconds(now());

        if ($elapsedSeconds >= self::RESEND_COOLDOWN_SECONDS) {
            return 0;
        }

        return max(1, (int) ceil(self::RESEND_COOLDOWN_SECONDS - $elapsedSeconds));
    }

    private function invalidateActiveOtps(string $mobile, string $purpose): void
    {
        Otp::where('mobile_number', $mobile)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->delete();
    }

    public static function usesFixedTestOtp(string $mobile, string $purpose): bool
    {
        return app()->environment('local');
    }

    public static function generateLoginOtpCode(): string
    {
        if (self::usesFixedTestOtp('', '')) {
            return '111111';
        }

        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function generateOtpCode(string $mobile, string $purpose): string
    {
        return self::generateLoginOtpCode();
    }

    private function sendDocumentOtpSms(string $mobile, string $otpCode, string $purpose, ?string $logLabel): void
    {
        $config = config("otp-login.document_otp_sms.{$purpose}");

        if (! is_array($config) || empty($config['template_id']) || empty($config['message'])) {
            Log::warning('Document OTP SMS config missing; falling back to login template', [
                'purpose' => $purpose,
                'mobile' => $mobile,
            ]);
            $this->loginOtpSmsService->send($mobile, $otpCode, $logLabel);

            return;
        }

        $message = $this->loginOtpSmsService->buildMessage($otpCode, $config['message']);
        $this->loginOtpSmsService->send($mobile, $otpCode, $logLabel, $message, $config['template_id']);
    }
}
