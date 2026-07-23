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
            RoleSeeder::class,
            RegistarySeeder::class,
            CitizenUserSeeder::class,
            DepartmentUserSeeder::class,
            DistrictOfficerSeeder::class,
            PhysicalPossessionUserSeeder::class,
            LedgerSeeder::class,
            InstallmentDueSeeder::class,
            DistrictMasterSeeder::class,
            BlockMasterSeeder::class,
            VillageMasterSeeder::class,
            SocialCategoryMasterSeeder::class,
            FlatMasterSeeder::class,
            OwnerMasterSeeder::class,
            MMGAYUserSeeder::class,
            MmgayBdoSeeder::class,
            EwsDepartmentSeeder::class,
            EwsMasterDistSeeder::class,
            PptMembersSeeder::class,
            PptGurugramSeeder::class,
            AllEwsDataSeeder::class,
            EwsUserSeeder::class,
            AwsFlatsCridSeeder::class,
            EwsPppExclusionSeeder::class,
            EwsPropertyInIndiaSeeder::class,
            EwsHouseOwnershipRejectSeeder::class,
            EwsEligibleDrawListSeeder::class,
            EwsBookingsSeeder::class,
            EwsEligibleSeeder::class,
            EwsAllottedSeeder::class,
            EwsWaitingListSeeder::class,
            EwsDeveloperSeeder::class,
        ]);

        // Sync MMGAY citizen owners into users table
        $this->command->call('citizens:sync-mmgay-users');
    }
}