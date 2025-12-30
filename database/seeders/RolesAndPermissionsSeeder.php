<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Permissions from web.php
        $permissions = [
            'view_permissions',
            'create_permissions',
            'edit_permissions',
            'delete_permissions',

            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',

            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Category Permissions
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
        ];

        // Create Permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Grant all permissions to Admin role
        $adminRole->givePermissionTo(Permission::all());

        // Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Admin role to the user
        $adminUser->assignRole($adminRole);

        // Create a regular user for testing (if needed, otherwise remove)
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}