<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 0. IMPORTANT: Delete all existing roles and permissions to ensure fresh start
        Schema::disableForeignKeyConstraints();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        Schema::enableForeignKeyConstraints();

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
            Permission::create(['name' => $permission]);
        }

        // 2. Define Roles and Assign Permissions in SPECIFIC ORDER
        
        // Root: Hidden Role, Full Access
        $root = Role::create(['name' => 'root']);
        $root->givePermissionTo(Permission::all());

        // Admin: System Admin (مدبر النظام)
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Owner: General Manager (المدير العام)
        $owner = Role::create(['name' => 'owner']);
        $owner->givePermissionTo(Permission::all());

        // Partner: Colleague Lawyer (محامي زميل بالمكتب)
        $partner = Role::create(['name' => 'partner']);
        $partner->givePermissionTo(Permission::all());

        // Employed Lawyer (محامي مستخدم)
        $employedLawyer = Role::create(['name' => 'employed-lawyer']);
        $employedLawyer->givePermissionTo([
            'view parties', 'view matters', 'create matters', 'edit matters',
            'view tasks', 'create tasks', 'edit tasks',
            'view documents', 'create documents', 'edit documents',
            'view appointments', 'create appointments', 'edit appointments'
        ]);

        // Trainee Lawyer (محامي متدرب)
        $traineeLawyer = Role::create(['name' => 'trainee-lawyer']);
        $traineeLawyer->givePermissionTo([
            'view parties', 'view matters', 'view tasks', 'edit tasks', 'view documents', 'view appointments'
        ]);

        // Manager (مسير المكتب)
        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'view parties', 'edit parties', 
            'view matters', 
            'view invoices', 'create invoices', 'edit invoices',
            'view reports', 'access settings', 'manage users'
        ]);

        // Secretary (السكريتاريا)
        $secretary = Role::create(['name' => 'secretary']);
        $secretary->givePermissionTo([
            'view parties', 'create parties', 'edit parties', 
            'view appointments', 'create appointments', 'edit appointments'
        ]);

        // Clerk (كاتب المكتب)
        $clerk = Role::create(['name' => 'clerk']);
        $clerk->givePermissionTo(['view matters', 'edit matters', 'create documents', 'view tasks', 'edit tasks']);

        // Runner (كاتب الاجراءات)
        $runner = Role::create(['name' => 'runner']);
        $runner->givePermissionTo(['view tasks', 'edit tasks']);

        // Trainee Employee (كاتب متدرب)
        $traineeEmployee = Role::create(['name' => 'trainee-employee']);
        $traineeEmployee->givePermissionTo(['view tasks']);
    }
}
