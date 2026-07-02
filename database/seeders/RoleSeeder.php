<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Seeds the two roles defined in the design spec (section 4.1):
 *   ADMIN  — manages tournaments/teams/matches through Laravel CRUD
 *   VIEWER — read-only access (or just public pages unauthenticated)
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(
            ['role_name' => 'ADMIN'],
            ['description' => 'Full access — manage tournaments, teams, matches, and all data.']
        );

        Role::updateOrCreate(
            ['role_name' => 'VIEWER'],
            ['description' => 'Read-only access to public pages.']
        );
    }
}
