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
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $this->call([
            ImportSqlTablesSeeder::class,
            EmOfficeSeeder::class,
            DistrictSeeder::class,
            CitySeeder::class,
            SectorSeeder::class,
            CitySectorAssociationSeeder::class,
            PropertyRegistrationSeeder::class,
            PropertyPrivatePurchasersSeeder::class,
            GhumantuPurchaserSeeder::class,
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
            PropertyDrawDocumentsSeeder::class,
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
            AllEwsData544Seeder::class,
            GurugramAllEwsDataSeeder::class,
            FaridabadAllEwsDataSeeder::class,
            PanipatAllEwsDataSeeder::class,
            RewariAllEwsDataSeeder::class,
            RohtakAllEwsDataSeeder::class,
            EwsUserSeeder::class,
            AwsFlatsCridSeeder::class,
            EwsPppExclusionSeeder::class,
            GurugramPppExclusionSeeder::class,
            FaridabadPppExclusionSeeder::class,
            PanipatPppExclusionSeeder::class,
            RewariPppExclusionSeeder::class,
            RohtakPppExclusionSeeder::class,
            EwsPropertyInIndiaSeeder::class,
            GurugramPropertyRejectSeeder::class,
            FaridabadPropertyRejectSeeder::class,
            PanipatPropertyRejectSeeder::class,
            RewariPropertyRejectSeeder::class,
            RohtakPropertyRejectSeeder::class,
            EwsHouseOwnershipRejectSeeder::class,
            GurugramHouseOwnershipRejectSeeder::class,
            FaridabadHouseOwnershipRejectSeeder::class,
            PanipatHouseOwnershipRejectSeeder::class,
            RewariHouseOwnershipRejectSeeder::class,
            RohtakHouseOwnershipRejectSeeder::class,
            EwsEligibleDrawListSeeder::class,
            GurugramEligibleDrawListSeeder::class,
            FaridabadEligibleDrawListSeeder::class,
            PanipatEligibleDrawListSeeder::class,
            RewariEligibleDrawListSeeder::class,
            RohtakEligibleDrawListSeeder::class,
            EwsBookingsSeeder::class,
            EwsEligibleSeeder::class,
            EwsAllottedSeeder::class,
            EwsWaitingListSeeder::class,
            EwsDeveloperSeeder::class,
            MmsayOldRegistrationDataSeeder::class,
            MmsayEligibleBeneficiariesSeeder::class,
            AdcNotVerifiedSeeder::class,
        ]);

        // Sync MMGAY citizen owners into users table
        $this->command->call('citizens:sync-mmgay-users');

        // Copy remaining unregistered ppt_members into all_ews_data_1 as 'verify_In_survey_app' = 'No'
        $this->command->info("Copying remaining unregistered ppt_members to all_ews_data_1 as 'No'...");
        try {
            $existingMemberIds = \Illuminate\Support\Facades\DB::table('all_ews_data_1')
                ->whereNotNull('member_id')
                ->pluck('member_id')
                ->toArray();
            $existingMemberIdsMap = array_flip($existingMemberIds);

            $pptMembers = \Illuminate\Support\Facades\DB::table('ppt_members')->get();
            
            $uniqueToInsert = [];
            foreach ($pptMembers as $member) {
                $mid = $member->memberID;
                if (empty($mid)) {
                    continue;
                }
                if (!isset($existingMemberIdsMap[$mid]) && !isset($uniqueToInsert[$mid])) {
                    $uniqueToInsert[$mid] = [
                        'application_number'   => $member->familyID,
                        'full_name'            => $member->fullName,
                        'mobile_number'        => $member->mobileNo,
                        'bt_name'              => $member->btName,
                        'dist_name'            => $member->districtName ?? $member->district,
                        'dist_id'              => $member->district_id,
                        'property_type'        => $member->property_type ?? 'flat',
                        'member_id'            => $member->memberID,
                        'ppt_member_id'        => $member->id,
                        'secure_id'            => \Illuminate\Support\Str::random(32),
                        'verify_In_survey_app' => 'No',
                        'created_at'           => now(),
                        'updated_at'           => now()
                    ];
                }
            }

            $insertCount = count($uniqueToInsert);
            if ($insertCount > 0) {
                $batch = [];
                $batchSize = 1000;
                $inserted = 0;
                foreach ($uniqueToInsert as $insertRow) {
                    $batch[] = $insertRow;
                    if (count($batch) >= $batchSize) {
                        \Illuminate\Support\Facades\DB::table('all_ews_data_1')->insert($batch);
                        $inserted += count($batch);
                        $batch = [];
                    }
                }
                if (count($batch) > 0) {
                    \Illuminate\Support\Facades\DB::table('all_ews_data_1')->insert($batch);
                    $inserted += count($batch);
                }
                $this->command->info("Successfully copied {$inserted} unregistered records to all_ews_data_1.");
            } else {
                $this->command->info("No unregistered records to copy.");
            }
        } catch (\Exception $e) {
            $this->command->error("Error copying unregistered records: " . $e->getMessage());
        }

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

        // Auto-match ppp_exclusion
        $this->command->info("Matching ppp_exclusion column in all_ews_data_1 with ews_reject_ppp_exclusion_2...");
        try {
            \Illuminate\Support\Facades\DB::statement("UPDATE all_ews_data_1 SET ppp_exclusion = 0");
            
            // Match by aadhar_no
            $affectedAadhar = \Illuminate\Support\Facades\DB::update("
                UPDATE all_ews_data_1 
                JOIN ews_reject_ppp_exclusion_2 ON all_ews_data_1.aadhar_no = ews_reject_ppp_exclusion_2.aadhar_no
                SET all_ews_data_1.ppp_exclusion = 1
                WHERE all_ews_data_1.aadhar_no IS NOT NULL 
                  AND all_ews_data_1.aadhar_no != '' 
                  AND all_ews_data_1.aadhar_no != 'NULL'
                  AND all_ews_data_1.aadhar_no != 'null'
                  AND all_ews_data_1.application_number NOT IN (SELECT application_number FROM ews_eligible_draw_list_5 WHERE application_number IS NOT NULL)
                  AND all_ews_data_1.aadhar_no NOT IN (SELECT aadhar_no FROM ews_eligible_draw_list_5 WHERE aadhar_no IS NOT NULL)
            ");
            
            // Match by application_number
            $affectedApp = \Illuminate\Support\Facades\DB::update("
                UPDATE all_ews_data_1 
                JOIN ews_reject_ppp_exclusion_2 ON all_ews_data_1.application_number = ews_reject_ppp_exclusion_2.application_number
                SET all_ews_data_1.ppp_exclusion = 1
                WHERE all_ews_data_1.application_number IS NOT NULL 
                  AND all_ews_data_1.application_number != '' 
                  AND all_ews_data_1.application_number != 'NULL'
                  AND all_ews_data_1.application_number != 'null'
                  AND all_ews_data_1.application_number NOT IN (SELECT application_number FROM ews_eligible_draw_list_5 WHERE application_number IS NOT NULL)
                  AND all_ews_data_1.aadhar_no NOT IN (SELECT aadhar_no FROM ews_eligible_draw_list_5 WHERE aadhar_no IS NOT NULL)
            ");
            
            $this->command->info("Successfully matched and updated PPP exclusions.");
        } catch (\Exception $e) {
            $this->command->error("Error matching ppp_exclusion: " . $e->getMessage());
        }

        // Auto-match property_in_india
        $this->command->info("Matching property_in_india column in all_ews_data_1 with ews_reject_property_in_india_3...");
        try {
            \Illuminate\Support\Facades\DB::statement("UPDATE all_ews_data_1 SET property_in_india = 0");
            
            // Match by aadhar_no (only if NOT ppp_exclusion)
            $affectedAadhar = \Illuminate\Support\Facades\DB::update("
                UPDATE all_ews_data_1 
                JOIN ews_reject_property_in_india_3 ON all_ews_data_1.aadhar_no = ews_reject_property_in_india_3.aadhar_no
                SET all_ews_data_1.property_in_india = 1
                WHERE all_ews_data_1.aadhar_no IS NOT NULL 
                  AND all_ews_data_1.aadhar_no != '' 
                  AND all_ews_data_1.aadhar_no != 'NULL'
                  AND all_ews_data_1.aadhar_no != 'null'
                  AND all_ews_data_1.ppp_exclusion = 0
                  AND all_ews_data_1.application_number NOT IN (SELECT application_number FROM ews_eligible_draw_list_5 WHERE application_number IS NOT NULL)
                  AND all_ews_data_1.aadhar_no NOT IN (SELECT aadhar_no FROM ews_eligible_draw_list_5 WHERE aadhar_no IS NOT NULL)
            ");
            
            // Match by application_number (only if NOT ppp_exclusion)
            $affectedApp = \Illuminate\Support\Facades\DB::update("
                UPDATE all_ews_data_1 
                JOIN ews_reject_property_in_india_3 ON all_ews_data_1.application_number = ews_reject_property_in_india_3.application_number
                SET all_ews_data_1.property_in_india = 1
                WHERE all_ews_data_1.application_number IS NOT NULL 
                  AND all_ews_data_1.application_number != '' 
                  AND all_ews_data_1.application_number != 'NULL'
                  AND all_ews_data_1.application_number != 'null'
                  AND all_ews_data_1.ppp_exclusion = 0
                  AND all_ews_data_1.application_number NOT IN (SELECT application_number FROM ews_eligible_draw_list_5 WHERE application_number IS NOT NULL)
                  AND all_ews_data_1.aadhar_no NOT IN (SELECT aadhar_no FROM ews_eligible_draw_list_5 WHERE aadhar_no IS NOT NULL)
            ");
            
            $this->command->info("Successfully matched and updated properties in India.");
        } catch (\Exception $e) {
            $this->command->error("Error matching property_in_india: " . $e->getMessage());
        }

        // Auto-match house_ownership
        $this->command->info("Matching house_ownership column in all_ews_data_1 with ews_house_ownership_reject_4...");
        try {
            \Illuminate\Support\Facades\DB::statement("UPDATE all_ews_data_1 SET house_ownership = 0");
            
            // Match by aadhar_no (only if NOT ppp_exclusion and NOT property_in_india)
            $affectedAadhar = \Illuminate\Support\Facades\DB::update("
                UPDATE all_ews_data_1 
                JOIN ews_house_ownership_reject_4 ON all_ews_data_1.aadhar_no = ews_house_ownership_reject_4.aadhar_no
                SET all_ews_data_1.house_ownership = 1
                WHERE all_ews_data_1.aadhar_no IS NOT NULL 
                  AND all_ews_data_1.aadhar_no != '' 
                  AND all_ews_data_1.aadhar_no != 'NULL'
                  AND all_ews_data_1.aadhar_no != 'null'
                  AND all_ews_data_1.ppp_exclusion = 0
                  AND all_ews_data_1.property_in_india = 0
                  AND all_ews_data_1.application_number NOT IN (SELECT application_number FROM ews_eligible_draw_list_5 WHERE application_number IS NOT NULL)
                  AND all_ews_data_1.aadhar_no NOT IN (SELECT aadhar_no FROM ews_eligible_draw_list_5 WHERE aadhar_no IS NOT NULL)
            ");
            
            // Match by application_number (only if NOT ppp_exclusion and NOT property_in_india)
            $affectedApp = \Illuminate\Support\Facades\DB::update("
                UPDATE all_ews_data_1 
                JOIN ews_house_ownership_reject_4 ON all_ews_data_1.application_number = ews_house_ownership_reject_4.application_number
                SET all_ews_data_1.house_ownership = 1
                WHERE all_ews_data_1.application_number IS NOT NULL 
                  AND all_ews_data_1.application_number != '' 
                  AND all_ews_data_1.application_number != 'NULL'
                  AND all_ews_data_1.application_number != 'null'
                  AND all_ews_data_1.ppp_exclusion = 0
                  AND all_ews_data_1.property_in_india = 0
                  AND all_ews_data_1.application_number NOT IN (SELECT application_number FROM ews_eligible_draw_list_5 WHERE application_number IS NOT NULL)
                  AND all_ews_data_1.aadhar_no NOT IN (SELECT aadhar_no FROM ews_eligible_draw_list_5 WHERE aadhar_no IS NOT NULL)
            ");
            
            $this->command->info("Successfully matched and updated house ownership.");
        } catch (\Exception $e) {
            $this->command->error("Error matching house_ownership: " . $e->getMessage());
        }

        // Initialize Physical Possession Applications for eligible beneficiaries
        // $this->command->call('app:initialize-possession');
    }
}