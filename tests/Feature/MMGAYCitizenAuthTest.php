<?php

namespace Tests\Feature;

use App\Models\Otp;
use App\Models\User;
use App\Models\Role;
use App\Models\RoleGroup;
use App\Models\RoleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MMGAYCitizenAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test MMGAY citizen login page loads.
     */
    public function test_mmgay_citizen_login_page_loads(): void
    {
        $response = $this->get('/mmgav-citizen-login');
        $response->assertStatus(200);
        $response->assertSee('Housing For All (MMGAV)');
    }

    /**
     * Test full MMGAY citizen OTP authentication and dashboard access.
     */
    public function test_mmgay_citizen_auth_and_dashboard(): void
    {
        // 1. Create MMGAY citizen user
        $user = User::create([
            'name' => 'MMGAY Citizen Test',
            'mobile' => '8888800001',
            'role' => 'citizen',
            'scheme' => 'MMGAY',
            'password' => Hash::make('123456'),
            'Is_Active' => 1,
            'Is_Deleted' => 0,
        ]);

        $citizenRole = Role::where('slug', 'citizen')->first();
        $citizenGroup = RoleGroup::where('slug', 'citizen')->first();

        RoleType::create([
            'user_id' => $user->id,
            'role_id' => $citizenRole->id,
            'role_group_id' => $citizenGroup->id,
        ]);

        // 2. Put captcha in session
        $this->withSession(['captcha' => '1234']);

        // 3. Send OTP
        $sendOtpRes = $this->post('/mmgav-citizen-login/send-otp', [
            'mobile' => '8888800001',
            'captcha' => '1234',
        ]);

        $sendOtpRes->assertRedirect('/mmgav-citizen-login/verify');

        // 4. Fetch OTP from DB
        $otpRecord = Otp::where('mobile_number', '8888800001')->latest()->first();
        $this->assertNotNull($otpRecord);

        // 5. Verify OTP
        $verifyRes = $this->post('/mmgav-citizen-login/verify', [
            'otp' => $otpRecord->otp,
        ]);

        $verifyRes->assertRedirect('/mmgav/citizen/dashboard');

        // 6. Access dashboard
        $dashboardRes = $this->get('/mmgav/citizen/dashboard');
        $dashboardRes->assertStatus(200);
        $dashboardRes->assertSee('MMGAY Citizen Test');
    }

    /**
     * Test cross-scheme user is rejected.
     */
    public function test_cross_scheme_user_is_rejected(): void
    {
        // Create MMSAY citizen user
        $user = User::create([
            'name' => 'MMSAY Citizen Test',
            'mobile' => '8888800002',
            'role' => 'citizen',
            'scheme' => 'MMSAY',
            'password' => Hash::make('123456'),
            'Is_Active' => 1,
            'Is_Deleted' => 0,
        ]);

        $citizenRole = Role::where('slug', 'citizen')->first();
        $citizenGroup = RoleGroup::where('slug', 'citizen')->first();

        RoleType::create([
            'user_id' => $user->id,
            'role_id' => $citizenRole->id,
            'role_group_id' => $citizenGroup->id,
        ]);

        // Put captcha in session
        $this->withSession(['captcha' => '1234']);

        // Attempt login on MMGAY citizen endpoint
        $sendOtpRes = $this->post('/mmgav-citizen-login/send-otp', [
            'mobile' => '8888800002',
            'captcha' => '1234',
        ]);

        $sendOtpRes->assertSessionHas('error');
    }
}
