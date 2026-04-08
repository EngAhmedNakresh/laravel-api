<?php

namespace App\Http\Controllers\Api;

use App\Enums\AppointmentStatus;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Notifications\AppointmentStatusNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $allowedSorts = ['created_at', 'date', 'time', 'status'];
        $sortBy = in_array($request->input('sort_by'), $allowedSorts, true) ? $request->input('sort_by') : 'date';
        $sortDir = $request->input('sort_dir') === 'desc' ? 'desc' : 'asc';

        $appointments = Appointment::query()
            ->with(['user.patient', 'doctor'])
            ->when(! $user->isAdmin(), fn ($query) => $query->where('user_id', $user->id))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('date', $request->input('date')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('doctor_id'), fn ($query) => $query->where('doctor_id', $request->integer('doctor_id')))
            ->orderBy($sortBy, $sortDir)
            ->paginate((int) $request->input('per_page', 10));

        return $this->successResponse(
            $this->paginatedData($appointments, AppointmentResource::collection($appointments->items())),
        );
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $data['status'] = $user->isAdmin() ? ($data['status'] ?? AppointmentStatus::Pending->value) : AppointmentStatus::Pending->value;

        if ($this->hasBookingConflict($data['doctor_id'], $data['date'], $data['time'])) {
            return $this->errorResponse('This time slot is already booked.', 422);
        }

        $appointment = Appointment::create($data)->load(['user.patient', 'doctor']);
        $appointment->user->notify(new AppointmentStatusNotification($appointment, 'created'));

        return $this->successResponse(new AppointmentResource($appointment), 'Appointment created successfully.', 201);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        return $this->successResponse(new AppointmentResource($appointment->load(['user.patient', 'doctor'])));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);

        $data = $request->validated();
        $user = $request->user();

        if (! $user->isAdmin()) {
            unset($data['doctor_id']);

            if (isset($data['status']) && $data['status'] !== AppointmentStatus::Cancelled->value) {
                return $this->errorResponse('You can only cancel your appointment.', 422);
            }
        }

        $doctorId = $data['doctor_id'] ?? $appointment->doctor_id;
        $date = $data['date'] ?? $appointment->date?->format('Y-m-d');
        $time = $data['time'] ?? $appointment->time?->format('H:i');
        $status = $data['status'] ?? ($appointment->status?->value ?? $appointment->status);

        if ($status !== AppointmentStatus::Cancelled->value && $this->hasBookingConflict($doctorId, $date, $time, $appointment->id)) {
            return $this->errorResponse('This time slot is already booked.', 422);
        }

        $appointment->update($data);
        $appointment->load(['user.patient', 'doctor']);
        $appointment->user->notify(new AppointmentStatusNotification($appointment, 'updated'));

        return $this->successResponse(new AppointmentResource($appointment), 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $this->authorize('delete', $appointment);

        $appointment->delete();

        return $this->successResponse(null, 'Appointment deleted successfully.');
    }

    private function hasBookingConflict(int $doctorId, string $date, string $time, ?int $ignoreId = null): bool
    {
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->whereTime('time', $time)
            ->where('status', '!=', AppointmentStatus::Cancelled->value)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
