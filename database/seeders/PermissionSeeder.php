<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'View Users', 'group' => 'Manage User'],
            ['name' => 'Create Users', 'group' => 'Manage User'],
            ['name' => 'Edit Users', 'group' => 'Manage User'],
            ['name' => 'Delete Users', 'group' => 'Manage User'],
            ['name' => 'View Customer', 'group' => 'Manage Customer'],
            ['name' => 'Create Customer', 'group' => 'Manage Customer'],
            ['name' => 'Edit Customer', 'group' => 'Manage Customer'],
            ['name' => 'Delete Customer', 'group' => 'Manage Customer'],
            ['name' => 'View Roles',   'group' => 'Manage Role'],
            ['name' => 'Create Roles', 'group' => 'Manage Role'],
            ['name' => 'Edit Roles',   'group' => 'Manage Role'],
            ['name' => 'Delete Roles', 'group' => 'Manage Role'],
            ['name' => 'View Products',   'group' => 'Manage Product'],
            ['name' => 'Create Products', 'group' => 'Manage Product'],
            ['name' => 'Edit Products',   'group' => 'Manage Product'],
            ['name' => 'Delete Products', 'group' => 'Manage Product'],
            ['name' => 'View Inventory',   'group' => 'Manage Inventory'],
            ['name' => 'View Tasks', 'group' => 'Manage Tasks'],
            ['name' => 'Create Tasks', 'group' => 'Manage Tasks'],
            ['name' => 'Edit Tasks', 'group' => 'Manage Tasks'],
            ['name' => 'Delete Tasks', 'group' => 'Manage Tasks'],
            ['name' => 'View Orders',   'group' => 'Manage Orders'],
            ['name' => 'Create Orders', 'group' => 'Manage Orders'],
            ['name' => 'Edit Orders',   'group' => 'Manage Orders'],
            ['name' => 'Delete Orders', 'group' => 'Manage Orders'],
            ['name' => 'View Quotes',   'group' => 'Manage Quotes'],
            ['name' => 'Create Quotes', 'group' => 'Manage Quotes'],
            ['name' => 'Edit Quotes',   'group' => 'Manage Quotes'],
            ['name' => 'Delete Quotes', 'group' => 'Manage Quotes'],
            ['name' => 'View Invoices',   'group' => 'Manage Invoices'],
            ['name' => 'Create Invoices', 'group' => 'Manage Invoices'],
            ['name' => 'Edit Invoices',   'group' => 'Manage Invoices'],
            ['name' => 'Delete Invoices', 'group' => 'Manage Invoices'],
            ['name' => 'View Payments',   'group' => 'Manage Payments'],
            ['name' => 'Create Payments', 'group' => 'Manage Payments'],
            ['name' => 'Edit Payments',   'group' => 'Manage Payments'],
            ['name' => 'Delete Payments', 'group' => 'Manage Payments'],
            ['name' => 'View Production',   'group' => 'Manage Production'],
            ['name' => 'Create Production', 'group' => 'Manage Production'],
            ['name' => 'Edit Production',   'group' => 'Manage Production'],
            ['name' => 'Delete Production', 'group' => 'Manage Production'],
            ['name' => 'View R&D',   'group' => 'Manage R&D'],
            ['name' => 'Approve R&D',   'group' => 'Manage R&D'],
            ['name' => 'View Quality & Control',   'group' => 'Manage Quality & Control'],
            ['name' => 'Approve Quality & Control',   'group' => 'Manage Quality & Control']
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name'=>$permission['name'],'group'=>$permission['group'],'guard_name'=>'web']);
        }
    }
}
