<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DepartmentUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Department User',
                'email' => 'department@gmail.com',
                'password' => Hash::make('123456'),
                'role' => 'department',
            ]
        ]);
    }
}