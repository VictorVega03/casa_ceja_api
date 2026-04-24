<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::updateOrCreate(
            ['name' => 'Varios'],
            [
                'phone'   => null,
                'email'   => null,
                'address' => null,
                'active'  => true,
            ]
        );
    }
}
