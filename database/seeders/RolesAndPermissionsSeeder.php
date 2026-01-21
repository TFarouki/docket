<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Permissions
        $permissions = [
            'view parties', 'create parties', 'edit parties', 'delete parties',
            'view matters', 'create matters', 'edit matters', 'delete matters',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
            'view invoices', 'create invoices', 'edit invoices', 'delete invoices',
            'view documents', 'create documents', 'edit documents', 'delete documents',
            'view appointments', 'create appointments', 'edit appointments', 'delete appointments',
            'view reports', 'view financial reports',
            'access settings', 'manage users'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Define Roles and Assign Permissions

        // 2. Define Roles and Assign Permissions

        // Super Admin & Admin: Full Access
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->givePermissionTo(Permission::all());

        // Partner: Full case management + billing
        $partner = Role::firstOrCreate(['name' => 'partner']);
        $partner->givePermissionTo(Permission::all());

        // Employed Lawyer
        $employedLawyer = Role::firstOrCreate(['name' => 'employed-lawyer']);
        $employedLawyer->givePermissionTo([
            'view parties', 'view matters', 'create matters', 'edit matters',
            'view tasks', 'create tasks', 'edit tasks',
            'view documents', 'create documents', 'edit documents',
            'view appointments', 'create appointments', 'edit appointments'
        ]);

        // Trainee Lawyer
        $traineeLawyer = Role::firstOrCreate(['name' => 'trainee-lawyer']);
        $traineeLawyer->givePermissionTo([
            'view parties', 'view matters', 'view tasks', 'edit tasks', 'view documents', 'view appointments'
        ]);

        // Manager
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'view parties', 'edit parties', 'view matters', 'view invoices', 'view reports', 'access settings', 'manage users'
        ]);

        // Secretary
        $secretary = Role::firstOrCreate(['name' => 'secretary']);
        $secretary->givePermissionTo([
            'view parties', 'create parties', 'edit parties', 'view appointments', 'create appointments', 'edit appointments'
        ]);

        // Clerk
        $clerk = Role::firstOrCreate(['name' => 'clerk']);
        $clerk->givePermissionTo(['view matters', 'edit matters', 'create documents', 'view tasks', 'edit tasks']);

        // Runner
        $runner = Role::firstOrCreate(['name' => 'runner']);
        $runner->givePermissionTo(['view tasks', 'edit tasks']);

        // Trainee Employee
        $traineeEmployee = Role::firstOrCreate(['name' => 'trainee-employee']);
        $traineeEmployee->givePermissionTo(['view tasks']);
    }
}
