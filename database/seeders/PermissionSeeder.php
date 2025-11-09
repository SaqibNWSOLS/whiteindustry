<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // User Management
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'module' => 'user_management', 'description' => 'Create, edit, and delete users'],
            ['name' => 'view_users', 'display_name' => 'View Users', 'module' => 'user_management', 'description' => 'View user list and details'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'module' => 'user_management', 'description' => 'Create, edit, and delete roles'],
            ['name' => 'assign_roles', 'display_name' => 'Assign Roles', 'module' => 'user_management', 'description' => 'Assign roles to users'],
            ['name' => 'view_activity_logs', 'display_name' => 'View Activity Logs', 'module' => 'user_management', 'description' => 'View system activity logs'],

            // CRM
            ['name' => 'manage_customers', 'display_name' => 'Manage Customers', 'module' => 'crm', 'description' => 'Create, edit, and delete customers'],
            ['name' => 'view_customers', 'display_name' => 'View Customers', 'module' => 'crm', 'description' => 'View customer list and details'],
            ['name' => 'manage_leads', 'display_name' => 'Manage Leads', 'module' => 'crm', 'description' => 'Create, edit, and delete leads'],
            ['name' => 'view_leads', 'display_name' => 'View Leads', 'module' => 'crm', 'description' => 'View lead list and details'],
            ['name' => 'convert_leads', 'display_name' => 'Convert Leads', 'module' => 'crm', 'description' => 'Convert leads to customers'],
            ['name' => 'manage_quotes', 'display_name' => 'Manage Quotes', 'module' => 'crm', 'description' => 'Create, edit, and delete quotes'],
            ['name' => 'view_quotes', 'display_name' => 'View Quotes', 'module' => 'crm', 'description' => 'View quote list and details'],
            ['name' => 'approve_quotes', 'display_name' => 'Approve Quotes', 'module' => 'crm', 'description' => 'Approve or reject quotes'],

            // Orders
            ['name' => 'manage_orders', 'display_name' => 'Manage Orders', 'module' => 'orders', 'description' => 'Create, edit, and delete orders'],
            ['name' => 'view_orders', 'display_name' => 'View Orders', 'module' => 'orders', 'description' => 'View order list and details'],
            ['name' => 'cancel_orders', 'display_name' => 'Cancel Orders', 'module' => 'orders', 'description' => 'Cancel orders'],

            // Production
            ['name' => 'manage_production', 'display_name' => 'Manage Production', 'module' => 'production', 'description' => 'Create and edit production orders'],
            ['name' => 'view_production', 'display_name' => 'View Production', 'module' => 'production', 'description' => 'View production orders'],
            ['name' => 'update_production_status', 'display_name' => 'Update Production Status', 'module' => 'production', 'description' => 'Update production order status'],
            ['name' => 'manage_qc', 'display_name' => 'Manage Quality Control', 'module' => 'production', 'description' => 'Perform quality control checks'],

            // Inventory
            ['name' => 'manage_inventory', 'display_name' => 'Manage Inventory', 'module' => 'inventory', 'description' => 'Create, edit, and delete inventory items'],
            ['name' => 'view_inventory', 'display_name' => 'View Inventory', 'module' => 'inventory', 'description' => 'View inventory list and details'],
            ['name' => 'adjust_inventory', 'display_name' => 'Adjust Inventory', 'module' => 'inventory', 'description' => 'Adjust inventory quantities'],
            ['name' => 'view_inventory_reports', 'display_name' => 'View Inventory Reports', 'module' => 'inventory', 'description' => 'View inventory reports'],

            // Products
            ['name' => 'manage_products', 'display_name' => 'Manage Products', 'module' => 'products', 'description' => 'Create, edit, and delete products'],
            ['name' => 'view_products', 'display_name' => 'View Products', 'module' => 'products', 'description' => 'View product list and details'],

            // Invoicing
            ['name' => 'manage_invoices', 'display_name' => 'Manage Invoices', 'module' => 'invoicing', 'description' => 'Create, edit, and delete invoices'],
            ['name' => 'view_invoices', 'display_name' => 'View Invoices', 'module' => 'invoicing', 'description' => 'View invoice list and details'],
            ['name' => 'void_invoices', 'display_name' => 'Void Invoices', 'module' => 'invoicing', 'description' => 'Void or cancel invoices'],

            // Payments
            ['name' => 'manage_payments', 'display_name' => 'Manage Payments', 'module' => 'payments', 'description' => 'Record and manage payments'],
            ['name' => 'view_payments', 'display_name' => 'View Payments', 'module' => 'payments', 'description' => 'View payment list and details'],

            // Reports
            ['name' => 'view_sales_reports', 'display_name' => 'View Sales Reports', 'module' => 'reports', 'description' => 'View sales reports'],
            ['name' => 'view_financial_reports', 'display_name' => 'View Financial Reports', 'module' => 'reports', 'description' => 'View financial reports'],
            ['name' => 'view_production_reports', 'display_name' => 'View Production Reports', 'module' => 'reports', 'description' => 'View production reports'],
            ['name' => 'export_reports', 'display_name' => 'Export Reports', 'module' => 'reports', 'description' => 'Export reports to PDF/Excel'],

            // Tasks
            ['name' => 'manage_tasks', 'display_name' => 'Manage Tasks', 'module' => 'workflow', 'description' => 'Create, edit, and delete tasks'],
            ['name' => 'view_tasks', 'display_name' => 'View Tasks', 'module' => 'workflow', 'description' => 'View task list and details'],
            ['name' => 'assign_tasks', 'display_name' => 'Assign Tasks', 'module' => 'workflow', 'description' => 'Assign tasks to users'],

            // Documents
            ['name' => 'manage_documents', 'display_name' => 'Manage Documents', 'module' => 'documents', 'description' => 'Upload, edit, and delete documents'],
            ['name' => 'view_documents', 'display_name' => 'View Documents', 'module' => 'documents', 'description' => 'View and download documents'],

            // System
            ['name' => 'access_admin_panel', 'display_name' => 'Access Admin Panel', 'module' => 'system', 'description' => 'Access administration panel'],
            ['name' => 'manage_settings', 'display_name' => 'Manage Settings', 'module' => 'system', 'description' => 'Modify system settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
