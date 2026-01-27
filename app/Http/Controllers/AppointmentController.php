<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request as RequestFacade;

class AppointmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Appointments/Index', [
            'appointments' => Appointment::query()
                ->with('party:id,full_name')
                ->when(RequestFacade::input('search'), function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhereHas('party', function ($subQ) use ($search) {
                                $subQ->where('full_name', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest('start_time')
                ->paginate(10)
                ->withQueryString(),
            'filters' => RequestFacade::only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Appointments/Create', [
            'parties' => Party::orderBy('full_name')->get(['id', 'full_name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'party_id' => 'nullable|exists:parties,id',
            'assigned_to' => 'nullable|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
            'notes' => 'nullable|string',
        ]);

        Appointment::create($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment scheduled successfully.');
    }

    public function edit(Appointment $appointment)
    {
        return Inertia::render('Appointments/Edit', [
            'appointment' => $appointment,
            'parties' => Party::orderBy('full_name')->get(['id', 'full_name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'party_id' => 'nullable|exists:parties,id',
            'assigned_to' => 'nullable|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully.');
    }
}
