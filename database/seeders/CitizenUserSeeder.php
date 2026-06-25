<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleGroup;
use App\Models\User;
use App\Support\IndianMobileNumber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CitizenUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Running citizens:sync-users command to populate citizen users...');
        $this->command->call('citizens:sync-users');
    }
}
