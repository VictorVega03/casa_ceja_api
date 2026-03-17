<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'SIN CATEGORÍA', 'discount' => 0, 'has_discount' => false, 'active' => true],
            ['name' => 'PAPELERIA', 'discount' => 0, 'has_discount' => false, 'active' => true],
            ['name' => 'FIESTA', 'discount' => 0, 'has_discount' => false, 'active' => true],
            ['name' => 'GLOBOS', 'discount' => 0, 'has_discount' => false, 'active' => true],
            ['name' => 'JUGUETERIA', 'discount' => 0, 'has_discount' => false, 'active' => true],
            ['name' => 'MERCERIA', 'discount' => 0, 'has_discount' => false, 'active' => true],
            ['name' => 'FERRETERIA', 'discount' => 0, 'has_discount' => false, 'active' => true],
            ['name' => 'PERFUMERIA', 'discount' => 0, 'has_discount' => false, 'active' => true],
            ['name' => 'IMPORTACION', 'discount' => 0, 'has_discount' => false, 'active' => true],
            ['name' => 'EL OSO', 'discount' => 0, 'has_discount' => false, 'active' => true],
            ['name' => 'Ventiladores', 'discount' => 0, 'has_discount' => false, 'active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}