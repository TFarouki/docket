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
            'view clients', 'create clients', 'edit clients', 'delete clients',
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

        // Trainee: Basic task execution
        $trainee = Role::firstOrCreate(['name' => 'trainee']);
        $trainee->givePermissionTo(['view tasks', 'edit tasks', 'view matters']);

        // Clerk (كاتب الإجراءات): Drafting, Documents
        $clerk = Role::firstOrCreate(['name' => 'clerk']);
        $clerk->givePermissionTo(['view matters', 'edit matters', 'create documents', 'view tasks', 'edit tasks']);

        // Secretary (الاستقبال): Appointments, Clients
        $secretary = Role::firstOrCreate(['name' => 'secretary']);
        $secretary->givePermissionTo([
            'view clients', 'create clients', 'edit clients',
            'view appointments', 'create appointments', 'edit appointments'
        ]);

        // Associate (مداوم): Full case work, but maybe not finance
        $associate = Role::firstOrCreate(['name' => 'associate']);
        $associate->givePermissionTo([
            'view clients', 'create clients', 'edit clients',
            'view matters', 'create matters', 'edit matters',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
            'view documents', 'create documents'
        ]);

        // Manager (مدبر المكتب): Admin + Finance (billing)
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'view clients', 'edit clients',
            'view matters',
            'view invoices', 'create invoices', 'edit invoices',
            'view reports', 'access settings', 'manage users'
        ]);

        // Partner (شريك): Like Associate + Profit View
        $partner = Role::firstOrCreate(['name' => 'partner']);
        $partner->givePermissionTo(Permission::all()); // Or specific subset

        // Owner: Everything
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->givePermissionTo(Permission::all());
    }
}
