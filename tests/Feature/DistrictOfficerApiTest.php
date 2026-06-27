<?php

namespace Tests\Feature;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DistrictOfficerApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database
        $this->seed();
    }

    /**
     * Test the captcha refresh endpoint.
     */
    public function test_can_refresh_captcha(): void
    {
        $response = $this->getJson('/api/possession/refresh-captcha');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'captcha_key',
                'captcha'
            ]);
    }

    /**
     * Test send OTP validation rules.
     */
    public function test_send_otp_validation_errors(): void
    {
        $response = $this->postJson('/api/possession/department/login/send-otp', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mobile', 'captcha']);
    }

    /**
     * Test send OTP with invalid captcha.
     */
    public function test_send_otp_invalid_captcha(): void
    {
        $response = $this->postJson('/api/possession/department/login/send-otp', [
            'mobile' => '9999900005',
            'captcha' => 'wrong_captcha',
            'captcha_key' => 'some-random-key'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'success' => false,
                'message' => 'Invalid captcha. Please try again.'
            ]);
    }

    /**
     * Test send OTP with non-registered mobile.
     */
    public function test_send_otp_non_registered_mobile(): void
    {
        // 1. Get captcha
        $captchaRes = $this->getJson('/api/possession/refresh-captcha');
        $key = $captchaRes['captcha_key'];
        $code = $captchaRes['captcha'];

        // 2. Send OTP with unregistered mobile
        $response = $this->postJson('/api/possession/department/login/send-otp', [
            'mobile' => '8888888888',
            'captcha' => $code,
            'captcha_key' => $key
        ]);

        $response->assertStatus(404)
            ->assertJsonFragment([
                'success' => false,
                'message' => 'Mobile number is not registered as a department officer account.'
            ]);
    }

    /**
     * Test full authentication flow and dashboard access.
     */
    public function test_full_auth_flow_and_dashboard(): void
    {
        // 1. Refresh Captcha
        $captchaRes = $this->getJson('/api/possession/refresh-captcha');
        $key = $captchaRes['captcha_key'];
        $code = $captchaRes['captcha'];

        // 2. Send OTP (Seeded Rohtak Officer: 9999900005)
        $sendOtpRes = $this->postJson('/api/possession/department/login/send-otp', [
            'mobile' => '9999900005',
            'captcha' => $code,
            'captcha_key' => $key
        ]);

        $sendOtpRes->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        // 3. Fetch OTP from DB
        $otpRecord = Otp::where('mobile_number', '9999900005')->latest()->first();
        $this->assertNotNull($otpRecord);

        // 4. Verify OTP and log in
        $verifyRes = $this->postJson('/api/possession/department/login/verify', [
            'mobile' => '9999900005',
            'otp' => $otpRecord->otp
        ]);

        $verifyRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'token',
                'user' => [
                    'id', 'name', 'mobile', 'email', 'role', 'district_id', 'district_name'
                ]
            ]);

        $token = $verifyRes['token'];

        // 5. Access dashboard with Sanctum bearer token
        $dashboardRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/possession/officer/dashboard');

        $dashboardRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'officer' => ['id', 'name', 'district_id', 'district_name'],
                'stats' => ['awaiting_schedule', 'scheduled', 'submitted', 'verified', 'rejected'],
                'chart' => ['labels', 'data'],
                'recent_applications',
                'pending_applications',
                'user_count',
                'approval_rate',
                'week_total'
            ]);

        // 6. Access reports endpoint
        $reportsRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/possession/officer/reports');

        $reportsRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'monthly_stats'
            ]);

        // 7. Logout
        $logoutRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/possession/officer/logout');

        $logoutRes->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'message' => 'Logged out successfully.'
            ]);

        // 8. Verify token is revoked by calling dashboard again
        $revokedRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/possession/officer/dashboard');

        $revokedRes->assertStatus(401);
    }
}
