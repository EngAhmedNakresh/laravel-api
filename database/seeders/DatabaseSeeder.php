<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            DoctorSeeder::class,
            ServiceSeeder::class,
            DepartmentSeeder::class,
        ]);

        User::factory(5)->create([
            'role' => UserRole::User,
        ]);
    }
}
