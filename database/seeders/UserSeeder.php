<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user_super_admin = User::create([
            'username' => 'super_admin',
            'name' => 'Super Admin',
            'email' => 'super_admin@renata.id',
            'password'=> bcrypt('123456789')
        ]);

        $user_super_admin->assignRole('super-admin');

        $user_admin = User::create([
            'username' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@renata.id',
            'password'=> bcrypt('123456789')
        ]);

        $user_admin->assignRole('admin');

        $user_cashier = User::create([
            'username' => 'cashier1',
            'name' => 'Cashier Crew 1',
            'email' => 'cashier1@renata.id',
            'password'=> bcrypt('123456789')
        ]);

        $user_cashier->assignRole('cashier');

    }
}
