<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class TruncateTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //RolePermission::query()->delete();
        UserRole::query()->delete();
    }
}
