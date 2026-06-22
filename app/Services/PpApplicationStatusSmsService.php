<?php

namespace App\Services;

use App\Models\PhysicalPossessionApplication;
use Illuminate\Support\Facades\Log;

class PpApplicationStatusSmsService
{
    public function __construct(
        private LoginOtpSmsService $smsService
    ) {}

    public function notifyCitizen(PhysicalPossessionApplication $application, string $action): void
    {
        $config = config('otp-login.pp_application_status_sms');

        if (! is_array($config) || empty($config['template_id']) || empty($config['message'])) {
            Log::warning('PP application status SMS config missing', [
                'application_id' => $application->id,
                'action' => $action,
            ]);

            return;
        }

        $statusLabel = $config['status_labels'][$action] ?? null;

        if (! $statusLabel) {
            Log::warning('PP application status SMS label missing', [
                'application_id' => $application->id,
                'action' => $action,
            ]);

            return;
        }

        $application->loadMissing('user');
        $mobile = $application->user?->mobile;

        if (empty($mobile)) {
            Log::warning('PP application status SMS skipped — citizen mobile missing', [
                'application_id' => $application->id,
                'user_id' => $application->user_id,
                'action' => $action,
            ]);

            return;
        }

        $this->smsService->sendAlphanumericMessage(
            $mobile,
            $statusLabel,
            $config['message'],
            $config['template_id'],
            'PP Application '.$application->application_number
        );
    }
}
