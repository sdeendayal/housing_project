<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MmgayBdoSeeder extends Seeder
{
    public function run(): void
    {
        // Create MMGAV BDO role
        $bdoRole = Role::updateOrCreate(
            ['slug' => 'mmgav_bdeo'],
            [
                'name' => 'MMGAV BDO',
                'slug' => 'mmgav_bdeo',
                'dashboard_route' => 'mmgay.bdo.dashboard',
                'dashboard_path' => null,
            ]
        );

        $this->command->info('MMGAV BDO Role seeded.');

        $bdoUsers = [
            [
                'email' => 'bdpo.ambala_city@hry.nic.in',
                'district_id' => 1,
                'district_name' => 'AMBALA',
                'block_name' => 'AMBALA-I',
            ],
            [
                'email' => 'bdpo.shehzadpur@hry.nic.in',
                'district_id' => 1,
                'district_name' => 'AMBALA',
                'block_name' => 'Shahzadpur',
            ],
            [
                'email' => 'bdpo.saha@hry.nic.in',
                'district_id' => 1,
                'district_name' => 'AMBALA',
                'block_name' => 'Saha',
            ],
            [
                'email' => 'bdpo.naraingarh@hry.nic.in',
                'district_id' => 1,
                'district_name' => 'AMBALA',
                'block_name' => 'Naraingarh',
            ],
            [
                'email' => 'bdpo.barara@hry.nic.in',
                'district_id' => 1,
                'district_name' => 'AMBALA',
                'block_name' => 'Barara',
            ],
            [
                'email' => 'bdpoambalacantt@gmail.comn',
                'district_id' => 1,
                'district_name' => 'AMBALA',
                'block_name' => 'Ambala Cantt',
            ],
            [
                'email' => 'bdpo.barara@gmail.com',
                'district_id' => 1,
                'district_name' => 'AMBALA',
                'block_name' => 'Barara',
            ],
            [
                'email' => 'bdpo.barara@gmail.com',
                'district_id' => 1,
                'district_name' => 'AMBALA',
                'block_name' => 'Barara',
            ],
            [
                'email' => 'ambalacitybdpo@gmail.com',
                'district_id' => 1,
                'district_name' => 'AMBALA',
                'block_name' => 'AMBALA-I',
            ],
            [
                'email' => 'bdpobhiwani@gmail.com',
                'district_id' => 9,
                'district_name' => 'BHIWANI',
                'block_name' => 'Bhiwani',
            ],
            [
                'email' => 'bdpo.charkhidadri@hry.gov.in',
                'district_id' => 17,
                'district_name' => 'CHARKHI DADRI',
                'block_name' => 'DADRI',
            ],
            [
                'email' => 'bdpocharkhidadri@gmail.com',
                'district_id' => 17,
                'district_name' => 'CHARKHI DADRI',
                'block_name' => 'DADRI',
            ],
            [
                'email' => 'bdpofaridabad@gmail.com',
                'district_id' => 22,
                'district_name' => 'FARIDABAD',
                'block_name' => 'Faridabad',
            ],
            [
                'email' => 'bdpo.fatehabad@hry.nic.in.',
                'district_id' => 18,
                'district_name' => 'FATEHABAD',
                'block_name' => 'Fatehabad',
            ],
            [
                'email' => 'bdpo.gurgaon@hry.nic.in',
                'district_id' => 14,
                'district_name' => 'GURUGRAM',
                'block_name' => 'Gurugram',
            ],
            [
                'email' => 'bdpo.gurgaon@hry.nic.in',
                'district_id' => 14,
                'district_name' => 'GURUGRAM',
                'block_name' => 'Gurugram',
            ],
            [
                'email' => 'bdpo.sohna86@gmail.com',
                'district_id' => 14,
                'district_name' => 'GURUGRAM',
                'block_name' => 'Sohna',
            ],
            [
                'email' => 'bdpo.hisar2@hry.nic.in',
                'district_id' => 15,
                'district_name' => 'HISAR',
                'block_name' => 'HISAR-II',
            ],
            [
                'email' => 'bdpo.hisar2@hry.nic.in',
                'district_id' => 15,
                'district_name' => 'HISAR',
                'block_name' => 'HISAR-II',
            ],
            [
                'email' => 'bdpo.hisar1@hry.nic.in',
                'district_id' => 15,
                'district_name' => 'HISAR',
                'block_name' => 'HISAR-I',
            ],
            [
                'email' => 'bdpobahadurgarh@gmail.com',
                'district_id' => 16,
                'district_name' => 'JHAJJAR',
                'block_name' => 'Bahadurgarh',
            ],
            [
                'email' => 'jind@hry.nic.in',
                'district_id' => 8,
                'district_name' => 'JIND',
                'block_name' => 'Jind',
            ],
            [
                'email' => 'bdpo.jind@hry.nic.in',
                'district_id' => 8,
                'district_name' => 'JIND',
                'block_name' => 'Jind',
            ],
            [
                'email' => 'drda.jnd@hry.nic.in',
                'district_id' => 8,
                'district_name' => 'JIND',
                'block_name' => 'Jind',
            ],
            [
                'email' => 'bdpokaithal@gmail.com',
                'district_id' => 7,
                'district_name' => 'KAITHAL',
                'block_name' => 'KAITHAL',
            ],
            [
                'email' => 'bdpoguhlacheeka@gmail.com',
                'district_id' => 7,
                'district_name' => 'KAITHAL',
                'block_name' => 'Guhla Cheeka',
            ],
            [
                'email' => 'kalayatbdpo@gmail.com',
                'district_id' => 7,
                'district_name' => 'KAITHAL',
                'block_name' => 'Kalayat',
            ],
            [
                'email' => 'bdpo.pundri@gmail.com',
                'district_id' => 7,
                'district_name' => 'KAITHAL',
                'block_name' => 'Pundri',
            ],
            [
                'email' => 'bdpodhand@gmail.com',
                'district_id' => 7,
                'district_name' => 'KAITHAL',
                'block_name' => 'Dhand',
            ],
            [
                'email' => 'bdposiwan@gmail.com',
                'district_id' => 7,
                'district_name' => 'KAITHAL',
                'block_name' => 'Siwan',
            ],
            [
                'email' => 'bdpo.rajound@hry.nic.in',
                'district_id' => 7,
                'district_name' => 'KAITHAL',
                'block_name' => 'Rajound',
            ],
            [
                'email' => 'bdpo.karnal@hry.nic.in',
                'district_id' => 19,
                'district_name' => 'KARNAL',
                'block_name' => 'Karnal',
            ],
            [
                'email' => 'bdpoassandh@gmail.com',
                'district_id' => 19,
                'district_name' => 'KARNAL',
                'block_name' => 'Assandh',
            ],
            [
                'email' => 'bdpo.thenesar@hry.nic.in',
                'district_id' => 12,
                'district_name' => 'KURUKSHETRA',
                'block_name' => 'Thanesar',
            ],
            [
                'email' => 'bdpo.pehowa@hry.nic.in',
                'district_id' => 12,
                'district_name' => 'KURUKSHETRA',
                'block_name' => 'Pehowa',
            ],
            [
                'email' => 'bdpo.ladwa@hry.nic.in',
                'district_id' => 12,
                'district_name' => 'KURUKSHETRA',
                'block_name' => 'Ladwa',
            ],
            [
                'email' => 'bdpo.babain@hry.nic.in',
                'district_id' => 12,
                'district_name' => 'KURUKSHETRA',
                'block_name' => 'Babain',
            ],
            [
                'email' => 'bdpo.shahabad@hry.nic.in',
                'district_id' => 12,
                'district_name' => 'KURUKSHETRA',
                'block_name' => 'Shahabad',
            ],
            [
                'email' => 'bdpo.ismailabad@hry.nic.in',
                'district_id' => 12,
                'district_name' => 'KURUKSHETRA',
                'block_name' => 'Ismailabad',
            ],
            [
                'email' => 'bdpo.pipli@yahoo.com',
                'district_id' => 12,
                'district_name' => 'KURUKSHETRA',
                'block_name' => 'Pipli',
            ],
            [
                'email' => 'bdpo.ateli@mail.com',
                'district_id' => 11,
                'district_name' => 'MAHENDRAGARH',
                'block_name' => 'Ateli Nangal',
            ],
            [
                'email' => 'bdpo.kanina@gmail.com',
                'district_id' => 11,
                'district_name' => 'MAHENDRAGARH',
                'block_name' => 'Kanina',
            ],
            [
                'email' => 'bdpo.nizampur@gmail.com',
                'district_id' => 11,
                'district_name' => 'MAHENDRAGARH',
                'block_name' => 'Nizampur',
            ],
            [
                'email' => 'bdpo.nangalchaudhary@gmail.com',
                'district_id' => 11,
                'district_name' => 'MAHENDRAGARH',
                'block_name' => 'N/Chaudhary',
            ],
            [
                'email' => 'bdpo.sihma@gmail.com',
                'district_id' => 11,
                'district_name' => 'MAHENDRAGARH',
                'block_name' => 'Sihma',
            ],
            [
                'email' => 'mgarhbdpo1@gmail.com',
                'district_id' => 11,
                'district_name' => 'MAHENDRAGARH',
                'block_name' => 'M.Garh',
            ],
            [
                'email' => 'bdpo.narnaul@gmail.com',
                'district_id' => 11,
                'district_name' => 'MAHENDRAGARH',
                'block_name' => 'Narnaul',
            ],
            [
                'email' => 'satnalibdpo002@gmail.com',
                'district_id' => 11,
                'district_name' => 'MAHENDRAGARH',
                'block_name' => 'Satnali',
            ],
            [
                'email' => 'satnalibdpo@gmail.com',
                'district_id' => 11,
                'district_name' => 'MAHENDRAGARH',
                'block_name' => 'Satnali',
            ],
            [
                'email' => 'bdponuh1@gmail.com',
                'district_id' => 5,
                'district_name' => 'NUH',
                'block_name' => 'Nuh',
            ],
            [
                'email' => 'bdpopalwal@gmail.com',
                'district_id' => 6,
                'district_name' => 'PALWAL',
                'block_name' => 'Palwal',
            ],
            [
                'email' => 'bdpo.palwal@hry.nic.in',
                'district_id' => 6,
                'district_name' => 'PALWAL',
                'block_name' => 'Palwal',
            ],
            [
                'email' => 'bdpopinjore@gmail.com',
                'district_id' => 21,
                'district_name' => 'PANCHKULA',
                'block_name' => 'Pinjore',
            ],
            [
                'email' => 'bdpo.raipur_rani@hry.nic.in',
                'district_id' => 21,
                'district_name' => 'PANCHKULA',
                'block_name' => 'Raipur Rani',
            ],
            [
                'email' => 'bdpobarwala@gmail.com',
                'district_id' => 21,
                'district_name' => 'PANCHKULA',
                'block_name' => 'Barwala',
            ],
            [
                'email' => 'bdpomorni1@yahoo.com',
                'district_id' => 21,
                'district_name' => 'PANCHKULA',
                'block_name' => 'Morni',
            ],
            [
                'email' => 'bdpo.israna@hry.nic.in',
                'district_id' => 2,
                'district_name' => 'PANIPAT',
                'block_name' => 'Israna',
            ],
            [
                'email' => 'bdpo.panipat@hry.nic.in',
                'district_id' => 2,
                'district_name' => 'PANIPAT',
                'block_name' => 'Panipat',
            ],
            [
                'email' => 'bdpo.panipat@hry.nic.in',
                'district_id' => 2,
                'district_name' => 'PANIPAT',
                'block_name' => 'Panipat',
            ],
            [
                'email' => 'bdpo.sanaulikhrd-hry@gov.in',
                'district_id' => 2,
                'district_name' => 'PANIPAT',
                'block_name' => 'Sanoli Khurd',
            ],
            [
                'email' => 'bdpo.bapoli@hry.nic.in',
                'district_id' => 2,
                'district_name' => 'PANIPAT',
                'block_name' => 'Bapoli',
            ],
            [
                'email' => 'bdpomadlauda@gmail.com',
                'district_id' => 2,
                'district_name' => 'PANIPAT',
                'block_name' => 'Madlauda',
            ],
            [
                'email' => 'bdporewari@gmail.com',
                'district_id' => 3,
                'district_name' => 'REWARI',
                'block_name' => 'Rewari',
            ],
            [
                'email' => 'bawalbdpo@gmail.com',
                'district_id' => 3,
                'district_name' => 'REWARI',
                'block_name' => 'Bawal',
            ],
            [
                'email' => 'bdpodahina@gmail.com',
                'district_id' => 3,
                'district_name' => 'REWARI',
                'block_name' => 'DAHINA',
            ],
            [
                'email' => 'bdpodharuhera@gmail.com',
                'district_id' => 3,
                'district_name' => 'REWARI',
                'block_name' => 'DHARUHERA',
            ],
            [
                'email' => 'bdponahar@gmail.com',
                'district_id' => 3,
                'district_name' => 'REWARI',
                'block_name' => 'NAHAR',
            ],
            [
                'email' => 'bdpokhol1@gmail.com',
                'district_id' => 3,
                'district_name' => 'REWARI',
                'block_name' => 'Khol',
            ],
            [
                'email' => 'bdpojatusana@gmail.com',
                'district_id' => 3,
                'district_name' => 'REWARI',
                'block_name' => 'Jatusana',
            ],
            [
                'email' => 'bdpo.rohtak@hry.nic.in',
                'district_id' => 4,
                'district_name' => 'ROHTAK',
                'block_name' => 'Rohtak',
            ],
            [
                'email' => 'bdpo.sampla@hry.nic.in',
                'district_id' => 4,
                'district_name' => 'ROHTAK',
                'block_name' => 'Sampla',
            ],
            [
                'email' => 'bdpo.meham@hry.nic.in',
                'district_id' => 4,
                'district_name' => 'ROHTAK',
                'block_name' => 'Meham',
            ],
            [
                'email' => 'bdpo.kalanaur@hry.nic.in',
                'district_id' => 4,
                'district_name' => 'ROHTAK',
                'block_name' => 'Kalanaur',
            ],
            [
                'email' => 'bdpo.lakhan_majra@hry.nic.in',
                'district_id' => 4,
                'district_name' => 'ROHTAK',
                'block_name' => 'Lakhan Majra',
            ],
            [
                'email' => 'bdpobrg@gmail.com',
                'district_id' => 13,
                'district_name' => 'SIRSA',
                'block_name' => 'Baragudha',
            ],
            [
                'email' => 'bdpo.dabwali@hry.nic.in',
                'district_id' => 13,
                'district_name' => 'SIRSA',
                'block_name' => 'Dabwali',
            ],
            [
                'email' => 'bdpo.ellenabad@hry.nic.in',
                'district_id' => 13,
                'district_name' => 'SIRSA',
                'block_name' => 'Ellenabad',
            ],
            [
                'email' => 'bdpo.chopta@gmail.com',
                'district_id' => 13,
                'district_name' => 'SIRSA',
                'block_name' => 'Nathusari Chopta',
            ],
            [
                'email' => 'bdpo.odhan@hry.nic.in',
                'district_id' => 13,
                'district_name' => 'SIRSA',
                'block_name' => 'Odhan',
            ],
            [
                'email' => 'bdpo.rania@hry.nic.in',
                'district_id' => 13,
                'district_name' => 'SIRSA',
                'block_name' => 'Rania',
            ],
            [
                'email' => 'bdpo.sirsa@gmail.com',
                'district_id' => 13,
                'district_name' => 'SIRSA',
                'block_name' => 'Sirsa',
            ],
            [
                'email' => 'bdposnp@gmail.com',
                'district_id' => 10,
                'district_name' => 'SONIPAT',
                'block_name' => 'SONIPAT',
            ],
            [
                'email' => 'bdpomurthal2@gmail.com',
                'district_id' => 10,
                'district_name' => 'SONIPAT',
                'block_name' => 'Murthal',
            ],
            [
                'email' => 'bdpokharkhoda@gmail.com',
                'district_id' => 10,
                'district_name' => 'SONIPAT',
                'block_name' => 'Kharkhoda',
            ],
            [
                'email' => 'bdpoganaur@gmail.com',
                'district_id' => 10,
                'district_name' => 'SONIPAT',
                'block_name' => 'GANAUR',
            ],
            [
                'email' => 'bdpogohana@gmail.com',
                'district_id' => 10,
                'district_name' => 'SONIPAT',
                'block_name' => 'Gohana',
            ],
            [
                'email' => 'bdpomundlana@gmail.com',
                'district_id' => 10,
                'district_name' => 'SONIPAT',
                'block_name' => 'Mundlana',
            ],
            [
                'email' => 'jagadhribdpo@gmail.com',
                'district_id' => 20,
                'district_name' => 'YAMUNA NAGAR',
                'block_name' => 'JAGADHRI',
            ],
            [
                'email' => 'bdpo.mustafabad@hry.nic.in',
                'district_id' => 20,
                'district_name' => 'YAMUNA NAGAR',
                'block_name' => 'Saraswati Nagar',
            ],
            [
                'email' => 'bdpo.chhachhrauli@hry.nic.in',
                'district_id' => 20,
                'district_name' => 'YAMUNA NAGAR',
                'block_name' => 'Chhachhrauli',
            ],
            [
                'email' => 'bdpo.radaur@hry.nic.in',
                'district_id' => 20,
                'district_name' => 'YAMUNA NAGAR',
                'block_name' => 'Radaur',
            ],
            [
                'email' => 'bdpo.sadhoura@gmail.com',
                'district_id' => 20,
                'district_name' => 'YAMUNA NAGAR',
                'block_name' => 'Sadhaura',
            ],
            [
                'email' => 'bdpo.bilaspur@hry.nic.in',
                'district_id' => 20,
                'district_name' => 'YAMUNA NAGAR',
                'block_name' => 'Bilaspur',
            ],
            [
                'email' => 'khizrabadbdpo1@gmail.com',
                'district_id' => 20,
                'district_name' => 'YAMUNA NAGAR',
                'block_name' => 'Pratap Nagar',
            ],
        ];

        $count = 0;
        foreach ($bdoUsers as $bdo) {
            $blockId = $this->getOrCreateBlock($bdo['block_name'], $bdo['district_id']);

            $user = User::updateOrCreate(
                ['email' => $bdo['email']],
                [
                    'name' => $bdo['block_name'],
                    'email' => $bdo['email'],
                    'mobile' => '8888888888',
                    'password' => Hash::make('123456'),
                    'role' => 'mmgav_bdeo',
                    'scheme' => 'MMGAY',
                    'Is_Active' => '1',
                    'Is_Deleted' => '0',
                    'district_id' => $bdo['district_id'],
                    'district_name' => $bdo['district_name'],
                    'block_id' => $blockId,
                    'block_name' => $bdo['block_name'],
                ]
            );

            RoleType::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role_id' => $bdoRole->id,
                ]
            );
            $count++;
        }

        $this->command->info("Successfully seeded {$count} MMGAY BDO Users.");
    }

    private function getOrCreateBlock($blockName, $districtId)
    {
        $block = DB::table('blockmaster')
            ->where('DistrictId', $districtId)
            ->where('BlockName', $blockName)
            ->first();

        if ($block) {
            return $block->BlockId;
        }

        $maxId = DB::table('blockmaster')->max('BlockId');
        $newId = $maxId ? ($maxId + 1) : 1;

        DB::table('blockmaster')->insert([
            'BlockId' => $newId,
            'DistrictId' => $districtId,
            'BlockName' => $blockName,
        ]);

        return $newId;
    }
}
