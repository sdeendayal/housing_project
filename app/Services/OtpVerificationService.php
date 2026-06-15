<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Log;

class OtpVerificationService
{
    public const EXPIRY_MINUTES = 10;

    public const RESEND_COOLDOWN_SECONDS = 60;

    public const MAX_ATTEMPTS = 5;

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

        $otpCode = $this->generateOtpCode();

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

            if (app()->environment('local')) {
                Log::info("{$logLabel} local OTP for testing", [
                    'mobile' => $mobile,
                    'purpose' => $purpose,
                    'otp' => $otpCode,
                ]);
            }
        }

        // SMS Gateway API will be integrated here later

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

    private function generateOtpCode(): string
    {
        if (app()->environment('local')) {
            return '111111';
        }

        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
