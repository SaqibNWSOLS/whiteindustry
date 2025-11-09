<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Role};
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Administrator
        $admin = User::create([
            'first_name' => 'Pierre',
            'last_name' => 'Blanc',
            'email' => 'admin@whiteindustry.com',
            'password' => Hash::make('password'),
            'phone' => '+213 555 123 456',
            'job_title' => 'Production Manager',
            'department' => 'Production',
            'status' => 'active',
        ]);
        $admin->assignRole('administrator');

        // Manager
        $manager = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Dubois',
            'email' => 'marie.dubois@whiteindustry.com',
            'password' => Hash::make('password'),
            'phone' => '+213 555 123 457',
            'job_title' => 'Sales Manager',
            'department' => 'Sales',
            'status' => 'active',
        ]);
        $manager->assignRole('manager');

        // Sales User
        $sales = User::create([
            'first_name' => 'Ahmed',
            'last_name' => 'Benali',
            'email' => 'ahmed.benali@whiteindustry.com',
            'password' => Hash::make('password'),
            'phone' => '+213 555 123 458',
            'job_title' => 'Sales Representative',
            'department' => 'Sales',
            'status' => 'active',
        ]);
        $sales->assignRole('sales_user');

        // Production User
        $production = User::create([
            'first_name' => 'Fatima',
            'last_name' => 'Zahra',
            'email' => 'fatima.zahra@whiteindustry.com',
            'password' => Hash::make('password'),
            'phone' => '+213 555 123 459',
            'job_title' => 'Production Supervisor',
            'department' => 'Production',
            'status' => 'active',
        ]);
        $production->assignRole('production_user');

        // Quality Control
        $qc = User::create([
            'first_name' => 'Karim',
            'last_name' => 'Messaoudi',
            'email' => 'karim.messaoudi@whiteindustry.com',
            'password' => Hash::make('password'),
            'phone' => '+213 555 123 460',
            'job_title' => 'QC Specialist',
            'department' => 'Quality Control',
            'status' => 'active',
        ]);
        $qc->assignRole('quality_control');

        // Warehouse User
        $warehouse = User::create([
            'first_name' => 'Rachid',
            'last_name' => 'Amara',
            'email' => 'rachid.amara@whiteindustry.com',
            'password' => Hash::make('password'),
            'phone' => '+213 555 123 461',
            'job_title' => 'Warehouse Manager',
            'department' => 'Warehouse',
            'status' => 'active',
        ]);
        $warehouse->assignRole('warehouse_user');

        // Accountant
        $accountant = User::create([
            'first_name' => 'Samira',
            'last_name' => 'Khelifi',
            'email' => 'samira.khelifi@whiteindustry.com',
            'password' => Hash::make('password'),
            'phone' => '+213 555 123 462',
            'job_title' => 'Senior Accountant',
            'department' => 'Finance',
            'status' => 'active',
        ]);
        $accountant->assignRole('accountant');
    }
}