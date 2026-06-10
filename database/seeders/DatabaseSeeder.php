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
        ]);

        // ~8.5 lakh rows — run only when SEED_INSTALLMENT_LEDGER=true
        // Or use: php artisan import:installment-ledger
        if (filter_var(env('SEED_INSTALLMENT_LEDGER', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->call([
                LedgerSeeder::class,
                InstallmentDueSeeder::class,
            ]);
        }
    }
}