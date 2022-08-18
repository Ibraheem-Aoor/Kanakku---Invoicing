<?php

namespace Database\Seeders;

use Kanakku\Models\Company;
use Illuminate\Database\Seeder;
use Kanakku\Models\User;
use Kanakku\Models\Setting;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'email' => 'admin@example.com',
            'name' => 'Jane Doe',
            'role' => 'super admin',
            'password' => 'Admin@123'
        ]);

        $company = Company::create([
            'name' => 'xyz',
            'unique_hash' => str_random(20)
        ]);

        $user->company_id = $company->id;
        $user->save();

        Setting::setSetting('profile_complete', 0);
    }
}
