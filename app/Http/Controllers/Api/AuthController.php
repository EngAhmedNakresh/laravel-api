<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'role' => UserRole::User,
        ]);

        if ($request->filled('phone') || $request->filled('address')) {
            $user->patient()->create([
                'phone' => $request->input('phone', ''),
                'address' => $request->input('address'),
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => new UserResource($user->load('patient')),
        ], 'Registered successfully.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return $this->errorResponse('Invalid credentials.', 401);
        }

        $token = $user->createToken($request->input('device_name', 'auth-token'))->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => new UserResource($user->load('patient')),
        ], 'Logged in successfully.');
    }

    public function logout(): JsonResponse
    {
        request()->user()?->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Logged out successfully.');
    }

    public function profile(): JsonResponse
    {
        return $this->successResponse(new UserResource(request()->user()->load('patient')));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        if (array_key_exists('name', $data)) {
            $user->name = $data['name'];
            $user->save();
        }

        if (array_key_exists('phone', $data) || array_key_exists('address', $data)) {
            $patient = $user->patient;

            if (! $patient) {
                $patient = $user->patient()->create([
                    'phone' => (string) ($data['phone'] ?? ''),
                    'address' => $data['address'] ?? null,
                ]);
            } else {
                $patient->update([
                    'phone' => array_key_exists('phone', $data) ? (string) ($data['phone'] ?? '') : $patient->phone,
                    'address' => array_key_exists('address', $data) ? ($data['address'] ?? null) : $patient->address,
                ]);
            }
        }

        return $this->successResponse(new UserResource($user->fresh()->load('patient')), 'Profile updated successfully.');
    }
}