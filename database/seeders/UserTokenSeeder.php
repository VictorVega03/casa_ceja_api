<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserTokenSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            UserToken::firstOrCreate(
                ['user_id' => $user->id],
                ['token'   => hash('sha256', Str::random(60))]
            );
        }
    }
}