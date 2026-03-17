<?php

namespace Database\Seeders;

use App\Models\BranchToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BranchTokenSeeder extends Seeder
{
    public function run(): void
    {
        BranchToken::create([
            'branch_id' => 1,
            'token' => Str::random(64),
            'name' => 'Token Casa Ceja Carranza',
            'active' => true,
        ]);
    }
}