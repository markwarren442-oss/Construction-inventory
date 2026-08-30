<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $officerRole = Role::where('slug', 'inventory-officer')->first();
        $siteRole = Role::where('slug', 'site-personnel')->first();

        User::firstOrCreate(
            ['email' => 'inventory@logistic.app'],
            [
                'name' => 'Inventory Officer Demo',
                'password' => bcrypt('password'),
                'role_id' => $officerRole ? $officerRole->id : 2,
                'phone' => '+1 (555) 234-5678',
                'status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'site@logistic.app'],
            [
                'name' => 'Site Personnel Demo',
                'password' => bcrypt('password'),
                'role_id' => $siteRole ? $siteRole->id : 3,
                'phone' => '+1 (555) 876-5432',
                'status' => 'active',
            ]
        );
    }
}
