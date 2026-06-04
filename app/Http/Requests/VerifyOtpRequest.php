<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $digits = preg_replace('/\D/', '', (string) $this->input('otp', ''));

        if (strlen($digits) > 6) {
            $digits = substr($digits, 0, 6);
        }

        $this->merge(['otp' => $digits]);
    }

    public function rules(): array
    {
        return [
            'otp' => ['required', 'digits:6', 'regex:/^\d{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be exactly 6 digits.',
        ];
    }
}
