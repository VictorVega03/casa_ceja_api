<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::create([
            'name' => 'Casa Ceja Carranza',
            'address' => 'E. Carranza 212 Z.C. Tampico',
            'email' => 'houseceja@gmail.com',
            'razon_social' => 'CASA CEJA CARRANZA',
            'active' => true,            
        ]);
    }
}