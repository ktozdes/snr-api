<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
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
        $userRoles = UserRole::all();
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@test.loc',
            'password' => bcrypt('pass'),
            'user_role_id' => $userRoles->where('name', 'administrator')->first()->id
        ]);
        $admin->createToken('authToken')->accessToken;

        $operator = User::create([
            'name' => 'operator',
            'email' => 'operator@test.loc',
            'password' => bcrypt('pass'),
            'user_role_id' => $userRoles->where('name', 'operator')->first()->id
        ]);
        $operator->createToken('authToken')->accessToken;

        $client = User::create([
            'name' => 'client',
            'email' => 'client@test.loc',
            'password' => bcrypt('pass'),
            'user_role_id' => $userRoles->where('name', 'client')->first()->id
        ]);
        $client->createToken('authToken')->accessToken;

    }
}
