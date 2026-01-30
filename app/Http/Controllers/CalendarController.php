<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Hearing;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $search = $request->input('search');
        $view = $request->input('view', 'month');
        $dateParam = $request->input('date'); // Used for week view to know which week to show
        
        $baseDate = $dateParam ? Carbon::parse($dateParam) : Carbon::create($year, $month, 1);

        if ($view === 'week') {
            $startDate = $baseDate->copy()->startOfWeek();
            $endDate = $baseDate->copy()->endOfWeek();
        } else {
            $startDate = $baseDate->copy()->startOfMonth();
            $endDate = $baseDate->copy()->endOfMonth();
        }

        $appointments = Appointment::with(['party:id,full_name', 'assignee:id,name'])
            ->whereBetween('start_time', [$startDate, $endDate])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('party', function ($subQ) use ($search) {
                            $subQ->where('full_name', 'like', "%{$search}%");
                        });
                });
            })
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->title,
                    'start' => $appointment->start_time,
                    'end' => $appointment->end_time,
                    'type' => 'appointment',
                    'status' => $appointment->status,
                    'party' => $appointment->party?->full_name,
                    'assignee' => $appointment->assignee?->name,
                    'color' => 'blue',
                    'route' => route('appointments.edit', $appointment->id),
                ];
            });

        $hearings = Hearing::with(['courtCase.matter.party:id,full_name'])
            ->whereBetween('date_time', [$startDate, $endDate])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('courtCase.matter', function ($sub) use ($search) {
                        $sub->where('title', 'like', "%{$search}%")
                            ->orWhereHas('party', function ($p) use ($search) {
                                $p->where('full_name', 'like', "%{$search}%");
                            });
                    });
                });
            })
            ->get()
            ->map(function ($hearing) {
                return [
                    'id' => 'h' . $hearing->id,
                    'title' => 'Hearing: ' . ($hearing->courtCase?->matter?->title ?? 'Session'),
                    'start' => $hearing->date_time,
                    'end' => null,
                    'type' => 'hearing',
                    'party' => $hearing->courtCase?->matter?->party?->full_name,
                    'color' => 'red',
                    'route' => route('matters.show', $hearing->courtCase?->matter_id ?? 0),
                ];
            });

        $events = $appointments->concat($hearings);

        return Inertia::render('Calendar/Index', [
            'events' => $events,
            'initialMonth' => (int)$baseDate->month,
            'initialYear' => (int)$baseDate->year,
            'initialDate' => $baseDate->toDateString(),
            'view' => $view,
            'filters' => ['search' => $search],
        ]);
    }
}
