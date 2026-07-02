<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Run order matters:
     *   1. RoleSeeder       — creates ADMIN/VIEWER roles (needed before users)
     *   2. SampleDataSeeder — tournaments, teams, stadiums, referees, coaches,
     *                         groups, team registrations, matches, test users
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SampleDataSeeder::class,
        ]);
    }
}
