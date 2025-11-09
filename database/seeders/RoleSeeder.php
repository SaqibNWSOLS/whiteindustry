<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Role, Permission};

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Administrator Role - All permissions
        $admin = Role::create([
            'name' => 'administrator',
            'display_name' => 'Administrator',
            'description' => 'Full system access with all permissions',
            'level' => 100,
            'is_system_role' => true,
        ]);
    // Attach all permission IDs
    $admin->permissions()->attach(Permission::pluck('id'));

        // Manager Role - Most permissions except user/role management
        $manager = Role::create([
            'name' => 'manager',
            'display_name' => 'Manager',
            'description' => 'Management level access to most features',
            'level' => 50,
            'is_system_role' => true,
        ]);
        $managerPermissions = Permission::whereNotIn('name', [
            'manage_users', 'manage_roles', 'assign_roles', 'manage_settings'
        ])->pluck('id');
        $manager->permissions()->attach($managerPermissions);

        // Sales User Role
        $sales = Role::create([
            'name' => 'sales_user',
            'display_name' => 'Sales User',
            'description' => 'Access to CRM, quotes, and orders',
            'level' => 30,
            'is_system_role' => false,
        ]);
        $salesPermissions = Permission::whereIn('name', [
            'view_customers', 'manage_customers',
            'view_leads', 'manage_leads', 'convert_leads',
            'view_quotes', 'manage_quotes',
            'view_orders', 'manage_orders',
            'view_invoices',
            'view_sales_reports',
        ])->pluck('id');
        $sales->permissions()->attach($salesPermissions);

        // Production User Role
        $production = Role::create([
            'name' => 'production_user',
            'display_name' => 'Production User',
            'description' => 'Access to production and inventory',
            'level' => 30,
            'is_system_role' => false,
        ]);
        $productionPermissions = Permission::whereIn('name', [
            'view_production', 'manage_production', 'update_production_status',
            'view_inventory', 'adjust_inventory',
            'view_products',
            'view_orders',
            'view_production_reports',
        ])->pluck('id');
        $production->permissions()->attach($productionPermissions);

        // Quality Control Role
        $qc = Role::create([
            'name' => 'quality_control',
            'display_name' => 'Quality Control',
            'description' => 'Access to QC and production monitoring',
            'level' => 25,
            'is_system_role' => false,
        ]);
        $qcPermissions = Permission::whereIn('name', [
            'view_production', 'manage_qc',
            'view_inventory',
            'view_products',
            'view_production_reports',
        ])->pluck('id');
        $qc->permissions()->attach($qcPermissions);

        // Warehouse User Role
        $warehouse = Role::create([
            'name' => 'warehouse_user',
            'display_name' => 'Warehouse User',
            'description' => 'Access to inventory management',
            'level' => 20,
            'is_system_role' => false,
        ]);
        $warehousePermissions = Permission::whereIn('name', [
            'view_inventory', 'manage_inventory', 'adjust_inventory',
            'view_products',
            'view_orders',
            'view_inventory_reports',
        ])->pluck('id');
        $warehouse->permissions()->attach($warehousePermissions);

        // Accountant Role
        $accountant = Role::create([
            'name' => 'accountant',
            'display_name' => 'Accountant',
            'description' => 'Access to financial operations',
            'level' => 40,
            'is_system_role' => false,
        ]);
        $accountantPermissions = Permission::whereIn('name', [
            'view_invoices', 'manage_invoices', 'void_invoices',
            'view_payments', 'manage_payments',
            'view_orders',
            'view_customers',
            'view_financial_reports', 'view_sales_reports',
            'export_reports',
        ])->pluck('id');
        $accountant->permissions()->attach($accountantPermissions);

        // Basic User Role - Read only
        $user = Role::create([
            'name' => 'user',
            'display_name' => 'Basic User',
            'description' => 'Basic read-only access',
            'level' => 10,
            'is_system_role' => false,
        ]);
        $userPermissions = Permission::where('name', 'like', 'view_%')->pluck('id');
        $user->permissions()->attach($userPermissions);

        // Customer Role - limited to their own records (separate user type)
        $customerRole = Role::create([
            'name' => 'customer',
            'display_name' => 'Customer',
            'description' => 'Customer account (person or business)',
            'level' => 5,
            'is_system_role' => false,
        ]);
        // Grant minimal permissions for customers (view own invoices/orders)
        $customerPerms = Permission::whereIn('name', [
            'view_orders', 'view_invoices', 'view_quotes'
        ])->pluck('id');
        $customerRole->permissions()->attach($customerPerms);
    }
}