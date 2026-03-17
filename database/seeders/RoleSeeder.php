<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador', 'key' => 'admin', 'access_level' => 1, 'description' => 'Acceso total al sistema', 'active' => 1],
            ['name' => 'Inventario', 'key' => 'inventory', 'access_level' => 2, 'description' => 'Acceso al módulo de inventario', 'active' => 1],
            ['name' => 'Cajero', 'key' => 'cashier', 'access_level' => 3, 'description' => 'Acceso al módulo POS', 'active' => 1],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}