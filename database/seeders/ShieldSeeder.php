<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Division;
use BezhanSalleh\FilamentShield\Support\Utils;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Super Admin role
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);

        // Generate permissions untuk semua resource yang ada
        $this->generateResourcePermissions();
        
        // Generate permissions berdasarkan divisi
        $this->generateDivisionBasedPermissions();

        // Assign semua permission ke super admin
        $superAdmin->syncPermissions(Permission::all());

        // Assign super admin ke user admin
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            $adminUser->assignRole('super_admin');
        }
    }

    private function generateResourcePermissions()
    {
        // List resource yang akan di-protect
        $resources = [
            'Employee',
            'Attendance', 
            'Task',
            'Client',
            'Service',
            'Meeting',
            'Division',
            'WorkhourPlan'
        ];

        foreach ($resources as $resource) {
            // Generate CRUD permissions untuk setiap resource
            $permissions = [
                "view_{$resource}",
                "view_any_{$resource}", 
                "create_{$resource}",
                "update_{$resource}",
                "delete_{$resource}",
                "delete_any_{$resource}",
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => strtolower($permission)]);
            }
        }
    }

    private function generateDivisionBasedPermissions()
    {
        $divisions = [
            'Human Resources' => [
                'view_employee',
                'view_any_employee',
                'create_employee', 
                'update_employee',
                'view_attendance',
                'view_any_attendance',
                'view_workhourplan',
                'view_any_workhourplan',
            ],
            'Project Management' => [
                'view_employee',
                'view_any_employee',
                'view_task',
                'view_any_task',
                'create_task',
                'update_task',
                'view_client',
                'view_any_client',
                'view_service',
                'view_any_service',
            ],
            'Marketing' => [
                'view_client',
                'view_any_client',
                'create_client',
                'update_client',
                'view_meeting',
                'view_any_meeting',
                'create_meeting',
                'update_meeting',
                'view_service',
                'view_any_service',
                'create_service',
                'update_service',
            ],
            'Finance' => [
                'view_client',
                'view_any_client',
                'view_service',
                'view_any_service',
                'update_service',
                'view_employee',
                'view_any_employee',
            ]
        ];

        foreach ($divisions as $divisionName => $permissions) {
            // Buat role untuk divisi
            $role = Role::firstOrCreate(['name' => strtolower($divisionName)]);
            
            // Assign permissions ke role
            $validPermissions = Permission::whereIn('name', $permissions)->get();
            $role->syncPermissions($validPermissions);

            // Buat divisi jika belum ada
            $division = Division::firstOrCreate([
                'division_name' => $divisionName
            ], [
                'required_workhours' => 8
            ]);
        }
    }
}