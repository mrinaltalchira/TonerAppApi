<?php

namespace Database\Seeders; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->insert([
            'user_id' => 1, // Auto-incremented, can be omitted as it's handled by the database
            'name' => 'Peter parker',
            'email' => 'peter@parker.com',
            'phone' => '9214429001',
            'is_active' => true,
            'user_role' => 'admin',
            'password' => Hash::make('123456'), // Hashing the password
            'authority' => 'full',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
