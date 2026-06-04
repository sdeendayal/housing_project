<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $digits = preg_replace('/\D/', '', (string) $this->input('mobile', ''));

        if (strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }

        $this->merge([
            'mobile' => $digits,
            'captcha' => trim((string) $this->input('captcha', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'digits:10', 'regex:/^[6-9]\d{9}$/'],
            'captcha' => ['required', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits' => 'Mobile number must be exactly 10 digits.',
            'mobile.regex' => 'Please enter a valid 10-digit Indian mobile number (starts with 6–9).',
            'captcha.required' => 'Captcha is required before sending OTP.',
            'captcha.max' => 'Captcha is invalid.',
        ];
    }
}
