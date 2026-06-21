<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CitizenRequest;
use App\Models\Office;
use App\Support\LaravelRequest as Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class AppointmentController extends Controller
{
    // Office staff: list appointments for their office
    public function officeIndex()
    {
        $user  = Auth::user();
        $query = Appointment::with(['user', 'citizenRequest.service'])->latest('scheduled_at');

        if ($user->role === 'office' && $user->office_id) {
            $query->where('office_id', $user->office_id);
        }

        $appointments = $query->get();
        $offices      = Office::orderBy('name')->get();
        return View::make('office.appointments.index', compact('appointments', 'offices'));
    }

    // Office staff: create appointment
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'citizen_request_id' => ['nullable', 'exists:citizen_requests,id'],
            'title'              => ['required', 'string', 'max:255'],
            'scheduled_at'       => ['required', 'date', 'after:now'],
            'duration_minutes'   => ['nullable', 'integer', 'min:15', 'max:480'],
            'notes'              => ['nullable', 'string', 'max:1000'],
            'user_id'            => ['required', 'exists:users,id'],
        ];

        if ($user->role !== 'office') {
            $rules['office_id'] = ['required', 'exists:offices,id'];
        }

        $request->validate($rules);

        $officeId = $user->role === 'office' ? $user->office_id : $request->input('office_id');

        $appointment = Appointment::create([
            'office_id'          => $officeId,
            'user_id'            => $request->input('user_id'),
            'citizen_request_id' => $request->input('citizen_request_id'),
            'title'              => $request->input('title'),
            'scheduled_at'       => $request->input('scheduled_at'),
            'duration_minutes'   => $request->input('duration_minutes', 30),
            'notes'              => $request->input('notes'),
            'status'             => 'pending',
        ]);

        try {
            $appointment->user->notify(new \App\Notifications\AppointmentScheduled($appointment->fresh()->load('office')));
        } catch (\Exception $e) {}

        return Redirect::back()->with('success', 'Appointment scheduled.');
    }

    // Office staff: update appointment status
    public function updateStatus(Appointment $appointment, Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'office' && $user->office_id && $appointment->office_id !== $user->office_id) {
            abort(403);
        }

        $request->validate(['status' => ['required', 'in:pending,confirmed,cancelled,completed']]);
        $newStatus = $request->input('status');
        $appointment->update(['status' => $newStatus]);

        // Notify the citizen on every meaningful status change
        if (in_array($newStatus, ['confirmed', 'completed', 'cancelled'])) {
            try {
                $appointment->user->notify(new \App\Notifications\AppointmentConfirmed($appointment->fresh(), $newStatus));
            } catch (\Exception $e) {}
        }

        return Redirect::back()->with('success', 'Appointment updated.');
    }

    // Office staff: delete appointment
    public function destroy(Appointment $appointment)
    {
        $user = Auth::user();
        if ($user->role === 'office' && $user->office_id && $appointment->office_id !== $user->office_id) {
            abort(403);
        }
        $appointment->delete();
        return Redirect::back()->with('success', 'Appointment deleted.');
    }

    // Citizen: list their appointments
    public function citizenIndex()
    {
        $appointments = Appointment::with(['office', 'citizenRequest.service'])
            ->where('user_id', Auth::id())
            ->latest('scheduled_at')
            ->get();

        return View::make('appointments.index', compact('appointments'));
    }

    // Citizen: confirm appointment
    public function citizenConfirm(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) abort(403);
        $appointment->update(['status' => 'confirmed']);
        return Redirect::back()->with('success', 'Appointment confirmed.');
    }

    // Citizen: cancel a