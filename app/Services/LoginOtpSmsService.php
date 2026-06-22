<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LoginOtpSmsService
{
    public static function shouldSendSms(): bool
    {
        return app()->environment('production');
    }

    public function send(
        string $mobile,
        string $otpCode,
        ?string $logLabel = null,
        ?string $message = null,
        ?string $templateId = null
    ): void {
        $templateId = $templateId ?: config('otp-login.sms_template_id');
        $message = $message ?: $this->buildMessage($otpCode);

        $this->dispatchSms($mobile, $message, $templateId, $logLabel, 'otpmsg', 'OTP SMS');
    }

    public function sendAlphanumericMessage(
        string $mobile,
        string $value,
        string $messageTemplate,
        string $templateId,
        ?string $logLabel = null,
        string $serviceType = 'singlemsg'
    ): void {
        $message = str_replace('{#alphanumeric#}', $value, $messageTemplate);

        $this->dispatchSms($mobile, $message, $templateId, $logLabel, $serviceType, 'Notification SMS');
    }

    private function dispatchSms(
        string $mobile,
        string $message,
        string $templateId,
        ?string $logLabel,
        string $serviceType,
        string $logType
    ): void {
        if (! self::shouldSendSms()) {
            Log::info($logLabel ? "{$logLabel} {$logType} skipped (non-production)" : "{$logType} skipped (non-production)", [
                'mobile' => $mobile,
                'template_id' => $templateId,
            ]);

            return;
        }

        try {
            $response = $this->sendSMS($mobile, $message, $templateId, $serviceType);

            Log::info($logLabel ? "{$logLabel} {$logType} sent" : "{$logType} sent", [
                'mobile' => $mobile,
                'template_id' => $templateId,
                'response' => $response,
            ]);
        } catch (\Throwable $e) {
            Log::error($logLabel ? "{$logLabel} {$logType} failed" : "{$logType} failed", [
                'mobile' => $mobile,
                'template_id' => $templateId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function buildMessage(string $otpCode, ?string $template = null): string
    {
        $template ??= config('otp-login.sms_message');

        return str_replace(
            ['{#numeric#}', '{otp}'],
            [$otpCode, $otpCode],
            $template
        );
    }

    private function sendSMS(string $mobile, string $message, string $temp_id, string $serviceType = 'otpmsg'): mixed
    {
        $username = config('otp-login.sms_username');
        $password = config('otp-login.sms_password');
        $senderid = config('otp-login.sms_sender_id');
        $dept_key = config('otp-login.sms_secure_key');
        $encryp_password = sha1(trim($password));

        return $this->sendSingleSMS($username, $encryp_password, $senderid, $message, $mobile, $dept_key, $temp_id, $serviceType);
    }

    private function sendSingleSMS(
        string $username,
        string $encryp_password,
        string $senderid,
        string $message,
        string $mobileno,
        string $deptSecureKey,
        string $temp_id,
        string $serviceType = 'otpmsg'
    ): mixed {
        $key = hash('sha512', trim($username).trim($senderid).trim($message).trim($deptSecureKey));

        $data = [
            'username' => trim($username),
            'password' => trim($encryp_password),
            'senderid' => trim($senderid),
            'content' => trim($message),
            'smsservicetype' => $serviceType,
            'mobileno' => trim($mobileno),
            'key' => trim($key),
            'templateid' => trim($temp_id),
        ];

        return $this->postToUrl(config('otp-login.sms_gateway_url'), $data);
    }

    private function postToUrl(string $url, array $data): mixed
    {
        $post = curl_init();

        curl_setopt_array($post, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
        ]);

        $result = curl_exec($post);

        if ($result === false) {
            $error = curl_error($post);
            curl_close($post);

            throw new \RuntimeException("SMS gateway request failed: {$error}");
        }

        curl_close($post);

        return $result;
    }
}
