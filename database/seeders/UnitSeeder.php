<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'PZA', 'active' => true],
            ['name' => 'BL', 'active' => true],
            ['name' => 'BLISTER', 'active' => true],
            ['name' => 'BOLSA', 'active' => true],
            ['name' => 'BOTE', 'active' => true],
            ['name' => 'FRASCO', 'active' => true],
            ['name' => 'PAQ', 'active' => true],
            ['name' => 'TIRA', 'active' => true],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}