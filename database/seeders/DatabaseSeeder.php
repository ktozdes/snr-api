<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('production')) {
            $this->call([
                OrganizationSeeder::class,
                KeywordSeeder::class,
            ]);
        $this->call([
            UserRoleSeeder::class,
            UserSeeder::class,
        ]);
        }
    }
}
