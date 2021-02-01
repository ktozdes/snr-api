<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@test.loc',
            'password' => bcrypt('pass'),
            'user_role_id' => 1
        ]);
        $admin->createToken('authToken')->accessToken;

        $operator = User::create([
            'name' => 'operator',
            'email' => 'operator@test.loc',
            'password' => bcrypt('pass'),
            'user_role_id' => 2
        ]);
        $operator->createToken('authToken')->accessToken;

        $client = User::create([
            'name' => 'client',
            'email' => 'client@test.loc',
            'password' => bcrypt('pass'),
            'user_role_id' => 3
        ]);
        $client->createToken('authToken')->accessToken;

    }
}
