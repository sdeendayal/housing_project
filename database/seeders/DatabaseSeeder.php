<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ImportSqlTablesSeeder::class,
            EmOfficeSeeder::class,
            DistrictSeeder::class,
            CitySeeder::class,
            SectorSeeder::class,
            CitySectorAssociationSeeder::class,
            PropertyRegistrationSeeder::class,
            PropertyPrivatePurchasersSeeder::class,
            PropertyAuctionDetailSeeder::class,
            CashReceiptDetailsSeeder::class,
            RoleGroupSeeder::class,
            RoleSeeder::class,
            CitizenUserSeeder::class,
            DepartmentUserSeeder::class,
            DistrictOfficerSeeder::class,
            PhysicalPossessionUserSeeder::class,
            LedgerSeeder::class,
            InstallmentDueSeeder::class,
            MMGAYUserSeeder::class,
        ]);

        // Sync MMGAY citizen owners into users table
        $this->command->call('citizens:sync-mmgay-users');
    }
}