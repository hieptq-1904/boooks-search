<?php

namespace Database\Seeders;

use App\Enums\User as EnumsUser;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $profile = [
            'name' =>  'Danny',
            'password' => bcrypt('Admin123'),
        ];

        User::updateOrCreate([
            'email' => 'sa@socialnetwork.com',
        ], $profile)->assignRole(UserRole::USER);

        User::updateOrCreate([
            'email' => 'admin@socialnetwork.com',
        ], $profile)->assignRole(UserRole::ADMIN);

        $emails = [
            'trungvhb@vmodev.com',
            'chiennv@vmodev.com',
            'thientq@vmodev.com',
        ];

       foreach ($emails as $value) {
           User::updateOrCreate([
               'email' => $value,
           ], $profile)->assignRole(UserRole::USER);
       }
    }
}
