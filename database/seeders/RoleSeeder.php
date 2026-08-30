<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrator', 'slug' => 'administrator', 'description' => 'Full system access and management'],
            ['name' => 'Inventory Officer', 'slug' => 'inventory-officer', 'description' => 'Manages materials, QR codes, inventory, and transactions'],
            ['name' => 'Site Personnel', 'slug' => 'site-personnel', 'description' => 'Handles materials and records movements via QR scanning'],
            ['name' => 'Project Manager', 'slug' => 'project-manager', 'description' => 'Monitors inventory, movements, reports, and stock levels'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
