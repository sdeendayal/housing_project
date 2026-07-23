<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EwsDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('slug', 'ews_department')->first();
        if (!$role) {
            $role = Role::create([
                'name' => 'EWS Department',
                'slug' => 'ews_department',
                'dashboard_route' => 'ews.department.dashboard',
            ]);
        } else {
            $role->update(['dashboard_route' => 'ews.department.dashboard']);
        }

        // Clean up any existing EWS department users first
        $existingUsers = User::where('role', 'ews_department')->get();
        foreach ($existingUsers as $user) {
            RoleType::where('user_id', $user->id)->delete();
            $user->delete();
        }

        // Create new EWS department user
        $user = User::create([
            'name' => 'EWS Department User',
            'email' => 'ews_department@gmail.com',
            'mobile' => '8888888888',
            'password' => Hash::make('password'),
            'role' => 'ews_department',
            'scheme' => 'EWS',
            'Is_Active' => '1',
            'Is_Deleted' => '0',
        ]);

        RoleType::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'Is_Active' => '1',
            'Is_Deleted' => '0',
        ]);

        $this->command->info('EWS Department seeded successfully. Email: ews_department@gmail.com, Password: password');
    }
}
