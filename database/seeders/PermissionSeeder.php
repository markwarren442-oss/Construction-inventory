<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'Dashboard'],

            // User & Access Management
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'User Management'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'User Management'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'User Management'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'User Management'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'User Management'],
            ['name' => 'View Activity Logs', 'slug' => 'activity-logs.view', 'module' => 'User Management'],

            // Material Management
            ['name' => 'View Materials', 'slug' => 'materials.view', 'module' => 'Material Management'],
            ['name' => 'Create Materials', 'slug' => 'materials.create', 'module' => 'Material Management'],
            ['name' => 'Edit Materials', 'slug' => 'materials.edit', 'module' => 'Material Management'],
            ['name' => 'Delete Materials', 'slug' => 'materials.delete', 'module' => 'Material Management'],
            ['name' => 'Manage Categories', 'slug' => 'categories.manage', 'module' => 'Material Management'],
            ['name' => 'Manage Suppliers', 'slug' => 'suppliers.manage', 'module' => 'Material Management'],

            // QR Code Management
            ['name' => 'Generate QR Codes', 'slug' => 'qrcodes.generate', 'module' => 'QR Code Management'],
            ['name' => 'View QR Codes', 'slug' => 'qrcodes.view', 'module' => 'QR Code Management'],
            ['name' => 'Scan QR Codes', 'slug' => 'qrcodes.scan', 'module' => 'QR Code Management'],
            ['name' => 'Print QR Codes', 'slug' => 'qrcodes.print', 'module' => 'QR Code Management'],

            // Material Transactions
            ['name' => 'View Transactions', 'slug' => 'transactions.view', 'module' => 'Material Transactions'],
            ['name' => 'Create Transactions', 'slug' => 'transactions.create', 'module' => 'Material Transactions'],
            ['name' => 'Receive Materials', 'slug' => 'transactions.receive', 'module' => 'Material Transactions'],
            ['name' => 'Issue Materials', 'slug' => 'transactions.issue', 'module' => 'Material Transactions'],
            ['name' => 'Transfer Materials', 'slug' => 'transactions.transfer', 'module' => 'Material Transactions'],

            // Location Management
            ['name' => 'View Locations', 'slug' => 'locations.view', 'module' => 'Location Management'],
            ['name' => 'Manage Sites', 'slug' => 'sites.manage', 'module' => 'Location Management'],
            ['name' => 'Manage Locations', 'slug' => 'locations.manage', 'module' => 'Location Management'],

            // Inventory Monitoring
            ['name' => 'View Inventory', 'slug' => 'inventory.view', 'module' => 'Inventory Monitoring'],
            ['name' => 'Adjust Inventory', 'slug' => 'inventory.adjust', 'module' => 'Inventory Monitoring'],

            // Reports
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'Reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'Reports'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        // Assign all permissions to Administrator
        $admin = Role::where('slug', 'administrator')->first();
        if ($admin) {
            $admin->permissions()->sync(Permission::pluck('id'));
        }

        // Assign permissions to Inventory Officer
        $officer = Role::where('slug', 'inventory-officer')->first();
        if ($officer) {
            $officerPerms = Permission::whereIn('slug', [
                'dashboard.view', 'materials.view', 'materials.create', 'materials.edit',
                'categories.manage', 'suppliers.manage',
                'qrcodes.generate', 'qrcodes.view', 'qrcodes.scan', 'qrcodes.print',
                'transactions.view', 'transactions.create', 'transactions.receive', 'transactions.issue', 'transactions.transfer',
                'locations.view', 'inventory.view', 'inventory.adjust',
                'reports.view', 'reports.export',
            ])->pluck('id');
            $officer->permissions()->sync($officerPerms);
        }

        // Assign permissions to Site Personnel
        $sitePersonnel = Role::where('slug', 'site-personnel')->first();
        if ($sitePersonnel) {
            $sitePerms = Permission::whereIn('slug', [
                'dashboard.view', 'materials.view',
                'qrcodes.view', 'qrcodes.scan',
                'transactions.view', 'transactions.create',
                'locations.view', 'inventory.view',
            ])->pluck('id');
            $sitePersonnel->permissions()->sync($sitePerms);
        }

        // Assign permissions to Project Manager
        $manager = Role::where('slug', 'project-manager')->first();
        if ($manager) {
            $managerPerms = Permission::whereIn('slug', [
                'dashboard.view', 'materials.view',
                'qrcodes.view',
                'transactions.view',
                'locations.view', 'inventory.view',
                'reports.view', 'reports.export',
                'activity-logs.view',
            ])->pluck('id');
            $manager->permissions()->sync($managerPerms);
        }
    }
}
