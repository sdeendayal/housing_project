<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PhysicalPossessionUserSeeder extends Seeder
{
    public function run(): void
    {
        // Demo ke liye pehle 10 citizen users ka password set karte hain
        $password = Hash::make('password123');

        $users = User::whereNotNull('mobile')
            ->where('role', 'citizen')
            ->take(10)
            ->get();

        foreach ($users as $user) {
            $user->update(['password' => $password]);
        }

        $this->command->info('PP demo users password set: password123 (first 10 citizens with mobile)');
    }
}
