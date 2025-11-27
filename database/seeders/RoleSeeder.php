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
            'name' => 'administrator',
            'guard_name' => 'web',
        ]);
        
        // Attach all permissions to admin
        $admin->givePermissionTo(Permission::all());

        // Manager Role - Most permissions except user/role management
        $manager = Role::create([
            'name' => 'manager',
            'guard_name' => 'web',
        ]);
        
        $managerPermissions = Permission::whereNotIn('name', [
            'Manage Users', 'Manage Roles', 'Assign Roles', 'Manage Settings'
        ])->get();
        
        $manager->givePermissionTo($managerPermissions);

        // Sales User Role
        $sales = Role::create([
            'name' => 'sales_user',
            'guard_name' => 'web',
        ]);
        
        $salesPermissions = Permission::whereIn('name', [
            'View Customers', 'Manage Customers',
            'View Leads', 'Manage Leads', 'Convert Leads',
            'View Quotes', 'Manage Quotes',
            'View Orders', 'Manage Orders',
            'View Invoices',
            'View Sales Reports',
        ])->get();
        
        $sales->givePermissionTo($salesPermissions);

        // Production User Role
        $production = Role::create([
            'name' => 'production_user',
            'guard_name' => 'web',
        ]);
        
        $productionPermissions = Permission::whereIn('name', [
            'View Production', 'Manage Production', 'Update Production Status',
            'View Inventory', 'Adjust Inventory',
            'View Products',
            'View Orders',
            'View Production Reports',
        ])->get();
        
        $production->givePermissionTo($productionPermissions);

        // Quality Control Role
        $qc = Role::create([
            'name' => 'quality_control',
            'guard_name' => 'web',
        ]);
        
        $qcPermissions = Permission::whereIn('name', [
            'View Production', 'Manage Quality Control',
            'View Inventory',
            'View Products',
            'View Production Reports',
        ])->get();
        
        $qc->givePermissionTo($qcPermissions);

        // Warehouse User Role
        $warehouse = Role::create([
            'name' => 'warehouse_user',
            'guard_name' => 'web',
        ]);
        
        $warehousePermissions = Permission::whereIn('name', [
            'View Inventory', 'Manage Inventory', 'Adjust Inventory',
            'View Products',
            'View Orders',
            'View Inventory Reports',
        ])->get();
        
        $warehouse->givePermissionTo($warehousePermissions);

        // Accountant Role
        $accountant = Role::create([
            'name' => 'accountant',
            'guard_name' => 'web',
        ]);
        
        $accountantPermissions = Permission::whereIn('name', [
            'View Invoices', 'Manage Invoices', 'Void Invoices',
            'View Payments', 'Manage Payments',
            'View Orders',
            'View Customers',
            'View Financial Reports', 'View Sales Reports',
            'Export Reports',
        ])->get();
        
        $accountant->givePermissionTo($accountantPermissions);

        // Basic User Role - Read only
        $user = Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]);
        
        $userPermissions = Permission::where('name', 'like', 'View%')->get();
        $user->givePermissionTo($userPermissions);

        // Customer Role - limited to their own records
        $customerRole = Role::create([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);
        
        $customerPermissions = Permission::whereIn('name', [
            'View Orders', 'View Invoices', 'View Quotes'
        ])->get();
        
        $customerRole->givePermissionTo($customerPermissions);
    }
}