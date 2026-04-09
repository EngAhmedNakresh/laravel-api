<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Main Admin',
                'password' => Hash::make('admin12345'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );
    }
}
