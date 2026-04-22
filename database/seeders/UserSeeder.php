<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Administrador',
                'email'     => 'admin@casaceja.com',
                'phone'     => '8001234567',
                'username'  => 'admin',
                'password'  => Hash::make('admin'),
                'user_type' => 1,
                'branch_id' => null,
                'active'    => true,
            ],
            [
                'name'      => 'Usuario Inventario',
                'email'     => 'inventario@casaceja.com',
                'phone'     => '8001234568',
                'username'  => 'inventario',
                'password'  => Hash::make('inventario'),
                'user_type' => 2,
                'branch_id' => null,
                'active'    => true,
            ],
            [
                'name'      => 'Usuario Cajero',
                'email'     => 'cajero@casaceja.com',
                'phone'     => '8001234569',
                'username'  => 'cajero',
                'password'  => Hash::make('cajero'),
                'user_type' => 3,
                'branch_id' => 1,
                'active'    => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }
    }
}