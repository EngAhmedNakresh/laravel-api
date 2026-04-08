<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:make-admin {email} {--name=} {--password=}', function () {
    $email = mb_strtolower(trim((string) $this->argument('email')));
    $name = $this->option('name');
    $password = $this->option('password');

    if ($email === '') {
        $this->error('Email is required.');

        return self::FAILURE;
    }

    $user = User::withTrashed()->where('email', $email)->first();
    $created = false;
    $generatedPassword = null;

    if (! $user) {
        $generatedPassword = $password ?: Str::random(16);

        $user = User::create([
            'name' => $name ?: 'Admin User',
            'email' => $email,
            'password' => $generatedPassword,
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        $created = true;
    } else {
        if ($user->trashed()) {
            $user->restore();
        }

        $user->forceFill([
            'role' => UserRole::Admin,
            'email_verified_at' => $user->email_verified_at ?: now(),
        ]);

        if ($name) {
            $user->name = $name;
        }

        if ($password) {
            $user->password = $password;
        }

        $user->save();
    }

    $this->newLine();
    $this->info($created ? 'Admin user created successfully.' : 'User promoted to admin successfully.');
    $this->line("Email: {$user->email}");
    $this->line("Role: {$user->role->value}");

    if ($generatedPassword) {
        $this->warn("Generated password: {$generatedPassword}");
        $this->warn('Save this password now. It will not be shown again.');
    }

    return self::SUCCESS;
})->purpose('Promote an existing user to admin or create one by email');
