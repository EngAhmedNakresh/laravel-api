<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\AppointmentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\UserResource;
use App\Models\Appointment;
use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    public function stats(): JsonResponse
    {
        return $this->successResponse([
            'users_count' => User::query()->where('role', UserRole::User->value)->count(),
            'admins_count' => User::query()->where('role', UserRole::Admin->value)->count(),
            'doctors_count' => Doctor::query()->count(),
            'appointments_count' => Appointment::query()->count(),
            'pending_appointments' => Appointment::query()->where('status', AppointmentStatus::Pending->value)->count(),
            'confirmed_appointments' => Appointment::query()->where('status', AppointmentStatus::Confirmed->value)->count(),
            'cancelled_appointments' => Appointment::query()->where('status', AppointmentStatus::Cancelled->value)->count(),
            'feedback_average_rating' => round((float) Feedback::query()->avg('rating'), 2),
            'conversations_count' => Conversation::query()->count(),
        ]);
    }

    public function recentAppointments(): JsonResponse
    {
        $appointments = Appointment::query()
            ->with(['user.patient', 'doctor'])
            ->latest()
            ->take(10)
            ->get();

        return $this->successResponse(AppointmentResource::collection($appointments));
    }

    public function recentUsers(): JsonResponse
    {
        $users = User::query()
            ->with('patient')
            ->where('role', UserRole::User->value)
            ->latest()
            ->take(10)
            ->get();

        return $this->successResponse(UserResource::collection($users));
    }

    public function conversations(Request $request): JsonResponse
    {
        $conversations = Conversation::query()
            ->with(['messages', 'user'])
            ->latest()
            ->paginate((int) $request->input('per_page', 10));

        return $this->successResponse(
            $this->paginatedData($conversations, ConversationResource::collection($conversations->items())),
        );
    }
}
