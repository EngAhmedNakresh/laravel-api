<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 50);
        $perPage = max(1, min(500, $perPage));

        $users = User::query()
            ->with('patient')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));

                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);

        return $this->successResponse(
            $this->paginatedData($users, UserResource::collection($users->items())),
        );
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(array_map(fn (UserRole $role) => $role->value, UserRole::cases()))],
        ]);

        $nextRole = UserRole::from($validated['role']);

        if ($user->id === $request->user()->id && $nextRole === UserRole::User) {
            return $this->errorResponse('You cannot remove admin role from your own account.', 422);
        }

        if ($user->role === UserRole::Admin && $nextRole === UserRole::User) {
            $adminCount = User::query()->where('role', UserRole::Admin->value)->count();
            if ($adminCount <= 1) {
                return $this->errorResponse('Cannot demote the last remaining admin.', 422);
            }
        }

        if ($user->role !== $nextRole) {
            $user->update(['role' => $nextRole]);
        }

        return $this->successResponse(
            new UserResource($user->fresh()->load('patient')),
            'User role updated successfully.',
        );
    }

    public function updateRoleByEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', Rule::in(array_map(fn (UserRole $role) => $role->value, UserRole::cases()))],
        ]);

        $user = User::query()->whereRaw('LOWER(email) = ?', [strtolower($validated['email'])])->first();

        if (! $user) {
            return $this->errorResponse('User not found.', 404);
        }

        $request->merge(['role' => $validated['role']]);

        return $this->updateRole($request, $user);
    }
}