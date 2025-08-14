<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class QuickShieldSetup extends Command
{
    protected $signature = 'shield:quick-setup';
    protected $description = 'Quick setup Shield for existing admin';

    public function handle()
    {
        $this->info('Setting up Shield quickly...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create super_admin role
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        // Create basic permissions
        $permissions = [
            'view_user', 'view_any_user', 'create_user', 'update_user', 'delete_user', 'delete_any_user',
            'view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role', 'delete_any_role',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Assign all permissions to super admin
        $superAdmin->syncPermissions(Permission::all());

        // Assign super_admin role to admin users
        $adminUsers = User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            $admin->assignRole('super_admin');
            $this->info("Assigned super_admin role to: {$admin->email}");
        }

        $this->info('Shield setup completed! Try refreshing your browser.');
        $this->info('You should now see Role management in the navigation.');
    }
}