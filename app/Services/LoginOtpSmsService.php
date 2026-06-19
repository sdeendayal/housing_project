<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LoginOtpSmsService
{
    public function send(string $mobile, string $otpCode, ?string $logLabel = null): void
    {
        $templateId = config('otp-login.sms_template_id');
        $message = $this->buildMessage($otpCode);

        try {
            $response = $this->sendSMS($mobile, $message, $templateId);

            Log::info($logLabel ? "{$logLabel} OTP SMS sent" : 'OTP SMS sent', [
                'mobile' => $mobile,
                'template_id' => $templateId,
                'response' => $response,
            ]);
        } catch (\Throwable $e) {
            Log::error($logLabel ? "{$logLabel} OTP SMS failed" : 'OTP SMS failed', [
                'mobile' => $mobile,
                'template_id' => $templateId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function buildMessage(string $otpCode): string
    {
        return str_replace(
            ['{#numeric#}', '{otp}'],
            [$otpCode, $otpCode],
            config('otp-login.sms_message')
        );
    }

    private function sendSMS(string $mobile, string $message, string $temp_id): mixed
    {
        $username = config('otp-login.sms_username');
        $password = config('otp-login.sms_password');
        $senderid = config('otp-login.sms_sender_id');
        $dept_key = config('otp-login.sms_secure_key');
        $encryp_password = sha1(trim($password));

        return $this->sendSingleSMS($username, $encryp_password, $senderid, $message, $mobile, $dept_key, $temp_id);
    }

    private function sendSingleSMS(
        string $username,
        string $encryp_password,
        string $senderid,
        string $message,
        string $mobileno,
        string $deptSecureKey,
        string $temp_id
    ): mixed {
        $key = hash('sha512', trim($username).trim($senderid).trim($message).trim($deptSecureKey));

        $data = [
            'username' => trim($username),
            'password' => trim($encryp_password),
            'senderid' => trim($senderid),
            'content' => trim($message),
            'smsservicetype' => 'otpmsg',
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
