<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\RoleGroup;
use App\Models\RoleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MMGAYUserSeeder extends Seeder
{
    public function run(): void
    {
        $districtRole = Role::where('slug', 'district_ceo')->first();
        $dcRole = Role::where('slug', 'dc')->first();

        if (! $districtRole || ! $dcRole) {
            $this->command->error('MMGAY officer roles not found. Run RoleSeeder first.');

            return;
        }

        $users = [

            // =========================
            // District CEO
            // =========================

            [
                'name' => 'Panchkula',
                'email' => 'drdapkl@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Panchkula',
            ],

            [
                'name' => 'Ambala',
                'email' => 'drdaamb@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Ambala',
            ],

            [
                'name' => 'Yamunanagar',
                'email' => 'drdaynr@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Yamunanagar',
            ],

            [
                'name' => 'Kurukshetra',
                'email' => 'drdakkr@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Kurukshetra',
            ],
            [
                'name' => 'Kaithal',
                'email' => 'drdaktl@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Kaithal',
            ],
            [
                'name' => 'Karnal',
                'email' => 'drdaknl@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Karnal',
            ],
            [
                'name' => 'Panipat',
                'email' => 'drdappi@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Panipat',
            ],
            [
                'name' => 'Sonipat',
                'email' => 'drdason@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Sonipat',
            ],
            [
                'name' => 'Rohtak',
                'email' => 'drdarht@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Rohtak',
            ],
            [
                'name' => 'Jhajjar',
                'email' => 'drdajjr@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Jhajjar',
            ],
            [
                'name' => 'Gurugram',
                'email' => 'drdaggm@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Gurugram',
            ],
            [
                'name' => 'Faridabad',
                'email' => 'drdafbd@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Faridabad',
            ],
            [
                'name' => 'Nuh',
                'email' => 'drdanuh@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Nuh',
            ],
            [
                'name' => 'Rewari',
                'email' => 'drdarew@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Rewari',
            ],
            [
                'name' => 'Mahendragarh',
                'email' => 'drdamgr@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Mahendragarh',
            ],
            [
                'name' => 'Hisar',
                'email' => 'drdahsr@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Hisar',
            ],
            [
                'name' => 'Sirsa',
                'email' => 'drdassr@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Sirsa',
            ],
            [
                'name' => 'Fatehabad',
                'email' => 'drdaftb@hry.nic.in',
                'mobile' => null,
                'role' => 'district_ceo',
                'district_name' => 'Fatehabad',
            ],

            // .......
            // isi tarah saare District CEO
            // .......


            // =========================
            // DC
            // =========================

            [
                'name' => 'Sh.Anshaj Singh DCAmbala',
                'email' => 'commamb@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Ambala',
            ],
            [
                'name' => 'Sh. Ashok Kumar Garg DCHisar',
                'email' => 'commhsr@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Hisar',
            ],
            [
                'name' => 'Sh. Sanjay Joon DCFaridabad',
                'email' => 'comm.fbd-hry@gov.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Faridabad',
            ],
            [
                'name' => 'Sh. Ramesh Chander DCGurugram',
                'email' => 'commgrg@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Gurgaon',
            ],
            [
                'name' => 'Sh. Rajiv Rattan DCKarnal',
                'email' => 'kr.commissioner-hry@gov.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Karnal',
            ],
            [
                'name' => 'Sh. Phool Chand Meena DCRohtak',
                'email' => 'commroh@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Rohtak',
            ],

            // =========================
            // DC USERS (DISTRICT LEVEL)
            // =========================

            [
                'name' => 'DC Ambala',
                'email' => 'dcamb@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Ambala',
            ],
            [
                'name' => 'DC Bhiwani',
                'email' => 'dcbhw@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Bhiwani',
            ],
            [
                'name' => 'DC Charki Dadri',
                'email' => 'dccharkhidadri@gmail.com',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Charkhi Dadri',
            ],
            [
                'name' => 'DC Faridabad',
                'email' => 'dcfbd@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Faridabad',
            ],
            [
                'name' => 'DC Fatehabad',
                'email' => 'dcftb@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Fatehabad',
            ],
            [
                'name' => 'DC Gurgaon',
                'email' => 'dcgrg@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Gurgaon',
            ],
            [
                'name' => 'DC Hisar',
                'email' => 'dchsr@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Hisar',
            ],
            [
                'name' => 'DC Jhajjar',
                'email' => 'dcjjr@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Jhajjar',
            ],
            [
                'name' => 'DC Jind',
                'email' => 'dcjnd@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Jind',
            ],
            [
                'name' => 'DC Kaithal',
                'email' => 'dcktl@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Kaithal',
            ],
            [
                'name' => 'DC Karnal',
                'email' => 'dckrl@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Karnal',
            ],
            [
                'name' => 'DC Kurukshetra',
                'email' => 'dckrk@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Kurukshetra',
            ],
            [
                'name' => 'DC Mahendragarh',
                'email' => 'dcnrl@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Mahendragarh',
            ],
            [
                'name' => 'DC Nuh',
                'email' => 'dcnuh@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Nuh',
            ],
            [
                'name' => 'DC Palwal',
                'email' => 'dcpwl@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Palwal',
            ],
            [
                'name' => 'DC Panchkula',
                'email' => 'dcpkl@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Panchkula',
            ],
            [
                'name' => 'DC Panipat',
                'email' => 'dcpnp@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Panipat',
            ],
            [
                'name' => 'DC Rewari',
                'email' => 'dcrwr@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Rewari',
            ],
            [
                'name' => 'DC Rohtak',
                'email' => 'dcroh@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Rohtak',
            ],
            [
                'name' => 'DC Sirsa',
                'email' => 'dcsrs@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Sirsa',
            ],
            [
                'name' => 'DC Sonipat',
                'email' => 'dcsnp@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Sonipat',
            ],
            [
                'name' => 'DC Yamunanagar',
                'email' => 'dcynr@hry.nic.in',
                'mobile' => null,
                'role' => 'dc',
                'district_name' => 'Yamunanagar',
            ],

            // .......
            // baaki saare DC
            // .......
        ];

        foreach ($users as $data) {

            $user = User::firstOrCreate(

                [
                    'email' => $data['email']
                ],

                [
                    'name' => $data['name'],
                    'mobile' => $data['mobile'],
                    'password' => Hash::make('123456'),
                    'role' => $data['role'],
                    'scheme' => 'MMGAY',
                    'district_name' => $data['district_name'],
                    'Is_Active' => 1,
                    'Is_Deleted' => 0,
                ]
            );

            $role = $data['role'] == 'district_ceo'
                ? $districtRole
                : $dcRole;

            RoleType::updateOrCreate(
                [
                    'user_id' => $user->id
                ],
                [
                    'role_id' => $role->id,
                ]
            );
        }
    }
}