<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'administrator')->first();

        User::firstOrCreate(
            ['email' => 'admin@logistic.app'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('password'),
                'role_id' => $adminRole ? $adminRole->id : 1,
                'status' => 'active',
            ]
        );
    }
}
