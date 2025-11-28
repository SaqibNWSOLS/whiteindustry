<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // First seed permissions and roles
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // Then seed users with roles
        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
