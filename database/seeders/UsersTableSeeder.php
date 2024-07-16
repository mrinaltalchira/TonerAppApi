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
            
            'name' => 'Peter parker',
            'email' => 'peter@parker.com',
            'token' => '',
            'phone' => '+919214429001',
            'is_active' => '0',
            'user_role' => 'admin',
            'password' => Hash::make('123456'), // Hashing the password 
            'machine_module' => '1',
            'client_module' => '1',
            'user_module' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
