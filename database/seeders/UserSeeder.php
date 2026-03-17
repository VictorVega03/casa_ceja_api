<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@casaceja.com',
            'phone' => '0000000000',
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'user_type' => 1,
            'branch_id' => null,
            'active' => true,            
        ]);
    }
}