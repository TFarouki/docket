<?php

namespace App\Http\Controllers;

use App\Models\CourtCase;
use App\Models\Matter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourtCaseController extends Controller
{
    public function create(Request $request)
    {
        $matterId = $request->input('matter_id');
        $matter = Matter::findOrFail($matterId);

        return Inertia::render('CourtCases/Create', [
            'matter' => $matter,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matter_id' => 'required|exists:matters,id',
            'court_name' => 'required|string|max:255',
            'case_number' => 'required|string|max:255',
            'judge_name' => 'nullable|string|max:255',
            'opponent_name' => 'nullable|string|max:255',
            'opponent_lawyer' => 'nullable|string|max:255',
            'current_stage' => 'nullable|string|max:255',
        ]);

        CourtCase::create($validated);

        return redirect()->route('matters.show', $validated['matter_id'])->with('success', 'Court Case added successfully.');
    }
}
