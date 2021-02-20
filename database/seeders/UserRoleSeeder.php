<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = UserRole::create([
            'name' => 'administrator'
        ]);
        $operator = UserRole::create([
            'name' => 'operator'
        ]);
        $client = UserRole::create([
            'name' => 'client'
        ]);

        $permissions = config('constants.permissions');

        foreach ($permissions as $key => $value) {
            RolePermission::create([
                'name' => $key,
                'user_role_id' => $admin->id,
                'permission_const_id' => $value,
                'permissions' => '1111'
            ]);
            RolePermission::create([
                'name' => $key,
                'user_role_id' => $operator->id,
                'permission_const_id' => $value,
                'permissions' => '1110'
            ]);
            RolePermission::create([
                'name' => $key,
                'user_role_id' => $client->id,
                'permission_const_id' => $value,
                'permissions' => '1000'
            ]);
        }
    }
}
