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
     * Test MMGAV villager login page loads.
     */
    public function test_mmgav_villager_login_page_loads(): void
    {
        $response = $this->get('/mmgav/login');
        $response->assertStatus(200);
        $response->assertSee('Housing For All (MMGAV)');
        $response->assertSee('Villager Login');
    }

    /**
     * Test full MMGAV villager OTP authentication and dashboard access.
     */
    public function test_mmgav_villager_auth_and_dashboard(): void
    {
        $user = User::create([
            'name' => 'MMGAV Villager Test',
            'mobile' => '8888800001',
            'role' => 'villager',
            'scheme' => 'MMGAY',
            'password' => Hash::make('123456'),
            'Is_Active' => 1,
            'Is_Deleted' => 0,
        ]);

        $villagerRole = Role::where('slug', 'villager')->first();
        $villagerGroup = RoleGroup::where('slug', 'villager')->first();

        RoleType::create([
            'user_id' => $user->id,
            'role_id' => $villagerRole->id,
            'role_group_id' => $villagerGroup->id,
        ]);

        \Illuminate\Support\Facades\DB::table('districtmaster')->insertOrIgnore([
            'DistrictId' => 1,
            'DistrictName' => 'TEST_DISTRICT',
        ]);
        \Illuminate\Support\Facades\DB::table('blockmaster')->insertOrIgnore([
            'BlockId' => 1,
            'DistrictId' => 1,
            'BlockName' => 'TEST_BLOCK',
        ]);
        \Illuminate\Support\Facades\DB::table('villagemaster')->insertOrIgnore([
            'VillageId' => 1,
            'BlockId' => 1,
            'DistrictId' => 1,
            'VillageName' => 'TEST_VILLAGE',
        ]);

        \Illuminate\Support\Facades\DB::table('flatmaster')->insert([
            'FlatId' => 12345,
            'FlatNo' => 'TEST_FLAT_123',
            'VillageId' => 1,
            'BlockId' => 1,
            'DistrictId' => 1,
            'IsActive' => 1,
        ]);

        \Illuminate\Support\Facades\DB::table('ownermaster')->insert([
            'OwnerId' => 99999,
            'OwnerName' => 'MMGAV Villager Test',
            'FlatId' => 12345,
            'MobileNo' => '8888800001',
            'IsApproved' => 1,
            'IsPaid' => 1,
            'IsPaymentApproved' => 1,
        ]);

        $this->withSession(['captcha' => '1234']);

        $sendOtpRes = $this->post('/mmgav/login/send-otp', [
            'mobile' => '8888800001',
            'captcha' => '1234',
        ]);

        $sendOtpRes->assertRedirect('/mmgav/login/verify');

        $otpRecord = Otp::where('mobile_number', '8888800001')->latest()->first();
        $this->assertNotNull($otpRecord);
        $this->assertSame('mmgav_villager_login', $otpRecord->purpose);

        $verifyRes = $this->post('/mmgav/login/verify', [
            'otp' => $otpRecord->otp,
        ]);

        $verifyRes->assertRedirect('/mmgav/villager/dashboard');

        $dashboardRes = $this->get('/mmgav/villager/dashboard');
        $dashboardRes->assertStatus(200);
        $dashboardRes->assertSee('MMGAV Villager Test');
    }

    /**
     * Test cross-scheme user is rejected.
     */
    public function test_cross_scheme_user_is_rejected(): void
    {
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

        $this->withSession(['captcha' => '1234']);

        $sendOtpRes = $this->post('/mmgav/login/send-otp', [
            'mobile' => '8888800002',
            'captcha' => '1234',
        ]);

        $sendOtpRes->assertSessionHas('error');
    }

    /**
     * Test MMSAY citizen cannot access MMGAV villager dashboard.
     */
    public function test_mmsay_citizen_cannot_access_mmgav_villager_dashboard(): void
    {
        $user = User::create([
            'name' => 'MMSAY Citizen Test',
            'mobile' => '8888800003',
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

        $response = $this->actingAs($user)->get('/mmgav/villager/dashboard');
        $response->assertRedirect(route('mmgav.villager.login'));
    }

    /**
     * Legacy citizen login URL redirects to villager login.
     */
    public function test_legacy_citizen_login_url_redirects(): void
    {
        $response = $this->get('/mmgav-citizen-login');
        $response->assertRedirect('/mmgav/login');
    }
}
