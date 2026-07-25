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
            PptSonipatSeeder::class,
            PptFaridabadSeeder::class,
            PptGurugramSeeder::class,
            PptPanipatSeeder::class,
            PptRohtakSeeder::class,
            PptRewariSeeder::class,
            AllEwsDataSeeder::class,
            EwsUserSeeder::class,
            AwsFlatsCridSeeder::class,
            EwsPppExclusionSeeder::class,
            EwsPropertyInIndiaSeeder::class,
            GurugramPropertyRejectSeeder::class,
            FaridabadPropertyRejectSeeder::class,
            PanipatPropertyRejectSeeder::class,
            RewariPropertyRejectSeeder::class,
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

        // Populate member_id and ppt_member_id columns globally from ppt_members table
        $this->command->info("Performing global sync of member_id and ppt_member_id in all_ews_data_1 table...");
        try {
            $affectedMemberIds = \Illuminate\Support\Facades\DB::update("
                UPDATE all_ews_data_1 
                JOIN ppt_members ON all_ews_data_1.mobile_number = ppt_members.mobileNo
                SET all_ews_data_1.member_id = ppt_members.memberID
            ");
            $affectedPptMemberIds = \Illuminate\Support\Facades\DB::update("
                UPDATE all_ews_data_1
                JOIN (
                    SELECT mobileNo, MIN(id) as min_id 
                    FROM ppt_members 
                    GROUP BY mobileNo
                ) as sub ON all_ews_data_1.mobile_number = sub.mobileNo
                SET all_ews_data_1.ppt_member_id = sub.min_id
            ");
            $this->command->info("Successfully sync'd {$affectedMemberIds} member_id and {$affectedPptMemberIds} ppt_member_id records globally.");
        } catch (\Exception $e) {
            $this->command->error("Error during global sync of member IDs: " . $e->getMessage());
        }
    }
}