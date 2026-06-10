<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CitizenUserSeeder extends Seeder
{
    public function run(): void
    {
        $citizenGroup = RoleGroup::where('slug', 'citizen')->first();
        $citizenRole = Role::where('slug', 'citizen')->first();

        if (! $citizenGroup || ! $citizenRole) {
            $this->command->error('Citizen role/group not found. Run RoleGroupSeeder and RoleSeeder first.');

            return;
        }

        // Get mobiles already registered to skip duplicates quickly
        $existingMobiles = User::whereNotNull('mobile')->pluck('mobile')->flip()->all();

        $purchasers = DB::table('property_private_purchasers')
            ->where('IsActive', 1)
            ->where('IsDeleted', 0)
            ->where('Is_UserLogin_Deleted', 0)
            ->whereNotNull('MobileNo')
            ->where('MobileNo', '!=', '')
            ->select('PrivatePurchaserName', 'MobileNo')
            ->orderBy('PrivatePurchaserId')
            ->get();

        $created = 0;
        $skipped = 0;
        $seenMobiles = [];
        $now = now();
        $passwordHash = Hash::make(Str::random(32));

        foreach ($purchasers as $purchaser) {
            $mobile = $this->normalizeMobile($purchaser->MobileNo);

            if (! $mobile || isset($seenMobiles[$mobile]) || isset($existingMobiles[$mobile])) {
                $skipped++;

                continue;
            }

            $seenMobiles[$mobile] = true;

            $userId = DB::table('users')->insertGetId([
                'name' => trim($purchaser->PrivatePurchaserName) ?: 'Citizen User',
                'email' => null,
                'mobile' => $mobile,
                'password' => $passwordHash,
                'role' => 'citizen',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('role_types')->insert([
                'user_id' => $userId,
                'role_id' => $citizenRole->id,
                'role_group_id' => $citizenGroup->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $created++;
        }

        $this->command->info("Citizen users created: {$created}, skipped: {$skipped}");
    }

    private function normalizeMobile($mobile): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $mobile);

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) !== 10 || ! preg_match('/^[6-9]/', $digits)) {
            return null;
        }

        return $digits;
    }
}
