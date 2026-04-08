<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['id' => 1,  'name' => 'Casa Ceja Carranza',       'address' => 'E. Carranza 212 Z.C. Tampico',          'email' => 'houseceja@gmail.com',         'razon_social' => 'CASA CEJA CARRANZA',       'active' => true],
            ['id' => 2,  'name' => 'Casa Ceja Obregon',        'address' => 'Obregon Z.C. Tampico',                  'email' => 'houseceja@gmail.com',         'razon_social' => 'CASA CEJA',                'active' => false],
            ['id' => 3,  'name' => 'Casa Ceja Obregon #408',   'address' => 'Obregon #408 Z.C. Tampico',             'email' => 'houseceja@gmail.com',         'razon_social' => 'CASA CEJA OBREGON',        'active' => true],
            ['id' => 4,  'name' => 'Casa Ceja Olmos',          'address' => 'Olmos 111 Z.C. Tampico',                'email' => 'casacejapapeleria@gmail.com', 'razon_social' => 'CASA CEJA OLMOS',          'active' => true],
            ['id' => 5,  'name' => 'Casa Ceja B.Juarez 1',     'address' => 'B. Juarez #1111 Z.C. Tampico',          'email' => 'casacejapapeleria@gmail.com', 'razon_social' => 'CASA CEJA B.JUAREZ1',      'active' => true],
            ['id' => 6,  'name' => 'Casa Ceja B.Juarez 2',     'address' => 'B. Juarez #2222 Z.C. Tampico',          'email' => 'casacejapapeleria@gmail.com', 'razon_social' => 'CASA CEJA B.JUAREZ2',      'active' => true],
            ['id' => 7,  'name' => 'Casa Ceja Allende',        'address' => 'Allende 311 Z.C. Altamira',             'email' => 'houseceja@gmail.com',         'razon_social' => 'CASA CEJA ALLENDE',        'active' => true],
            ['id' => 8,  'name' => 'Casa Ceja Hidalgo',        'address' => 'Hidalgo 100-A Z.C. Altamira',           'email' => 'houseceja@gmail.com',         'razon_social' => 'CASA CEJA HIDALGO',        'active' => true],
            ['id' => 9,  'name' => 'Casa Ceja Fco. I. Madero', 'address' => 'Fco. I. Madero 500 Z.C. Tampico',       'email' => 'casacejapapeleria@gmail.com', 'razon_social' => 'CASA CEJA FCO. I. MADERO', 'active' => true],
            ['id' => 10, 'name' => 'Casa Ceja Aduana',         'address' => 'Aduana 700 Z.C. Tampico',               'email' => 'casacejapapeleria@gmail.com', 'razon_social' => 'CASA CEJA ADUANA',         'active' => true],
            ['id' => 11, 'name' => 'Casa Ceja 1ero. de Mayo',  'address' => '1ero. de Mayo 800 Z.C. Cd. Madero',     'email' => 'casacejapapeleria@gmail.com', 'razon_social' => 'CASA CEJA 1ERO. DE MAYO',  'active' => true],
            ['id' => 12, 'name' => 'Casa Ceja 13 de Enero',    'address' => '13 de Enero 900 Z.C. Cd. Madero',       'email' => 'casacejapapeleria@gmail.com', 'razon_social' => 'CASA CEJA 13 DE ENERO',    'active' => true],
            ['id' => 13, 'name' => 'CEDI Ceja Obregon',        'address' => 'Obregon 408 Z.C. Tampico',              'email' => 'houseceja@gmail.com',         'razon_social' => 'CEDI CEJA OBREGON',        'active' => true],
            ['id' => 14, 'name' => 'Marcelo Reyes Mujica',     'address' => 'Col. Morelos Tampico',                  'email' => 'chelovendedor@gmail.com',     'razon_social' => 'MARCELO REYES MUJICA',     'active' => true],
            ['id' => 15, 'name' => 'Isabel Lara',              'address' => 'Altamira',                              'email' => 'isabelvendedora@gmail.com',   'razon_social' => 'ISABEL LARA',              'active' => true],
        ];

        foreach ($branches as $branch) {
            DB::table('branches')->updateOrInsert(
                ['id' => $branch['id']],
                array_merge($branch, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}