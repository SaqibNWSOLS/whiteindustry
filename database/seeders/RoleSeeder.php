<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Administrator Role - All permissions
        $admin = Role::create([
            'name' => 'Administrator',
            'guard_name' => 'web',
        ]);
        
        // Attach all permissions to admin
        $admin->givePermissionTo(Permission::all());

        // Manager Role - Most permissions except user/role management
        $manager = Role::create([
            'name' => 'Manager',
            'guard_name' => 'web',
        ]);
        
        $managerPermissions = Permission::whereNotIn('group', [
            'Manage User', 'Manage Role'
        ])->get();
        
        $manager->givePermissionTo($managerPermissions);

        // Sales User Role
        $sales = Role::create([
            'name' => 'Sales User',
            'guard_name' => 'web',
        ]);
        
        $salesPermissions = Permission::whereIn('group', [
            'Manage Customer', 'Manage Orders', 'Manage Quotes', 'Manage Invoices', 'Manage Payments'
        ])->orWhere('name', 'View Products')
          ->get();
        
        $sales->givePermissionTo($salesPermissions);

        // Production User Role
        $production = Role::create([
            'name' => 'Production User',
            'guard_name' => 'web',
        ]);
        
        $productionPermissions = Permission::whereIn('group', [
            'Manage Production', 'Manage Inventory', 'Manage Product'
        ])->orWhere('name', 'View Orders')
          ->orWhere('name', 'View Tasks')
          ->get();
        
        $production->givePermissionTo($productionPermissions);

        // Quality Control Role
        $qc = Role::create([
            'name' => 'Quality Control',
            'guard_name' => 'web',
        ]);
        
        $qcPermissions = Permission::whereIn('group', [
            'Manage Quality & Control', 'Manage Production', 'Manage Inventory', 'Manage Product'
        ])->orWhere('name', 'View Orders')
          ->get();
        
        $qc->givePermissionTo($qcPermissions);

        // Warehouse User Role
        $warehouse = Role::create([
            'name' => 'Warehouse User',
            'guard_name' => 'web',
        ]);
        
        $warehousePermissions = Permission::whereIn('group', [
            'Manage Inventory', 'Manage Product'
        ])->orWhere('name', 'View Orders')
          ->orWhere('name', 'View Production')
          ->get();
        
        $warehouse->givePermissionTo($warehousePermissions);

        // Accountant Role
        $accountant = Role::create([
            'name' => 'Accountant',
            'guard_name' => 'web',
        ]);
        
        $accountantPermissions = Permission::whereIn('group', [
            'Manage Invoices', 'Manage Payments', 'Manage Orders', 'Manage Customer'
        ])->get();
        
        $accountant->givePermissionTo($accountantPermissions);

        // Basic User Role - Read only
        $user = Role::create([
            'name' => 'User',
            'guard_name' => 'web',
        ]);
        
        $userPermissions = Permission::where('name', 'like', 'View%')->get();
        $user->givePermissionTo($userPermissions);

        // R&D User Role (Additional role for R&D permissions)
        $rd = Role::create([
            'name' => 'R&D User',
            'guard_name' => 'web',
        ]);
        
        $rdPermissions = Permission::whereIn('group', [
            'Manage R&D', 'Manage Product'
        ])->orWhere('name', 'View Tasks')
          ->get();
        
        $rd->givePermissionTo($rdPermissions);
    }
}