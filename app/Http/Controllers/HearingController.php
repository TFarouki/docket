<?php

namespace App\Http\Controllers;

use App\Models\Hearing;
use App\Models\CourtCase;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HearingController extends Controller
{
    public function create(Request $request)
    {
        $courtCaseId = $request->input('court_case_id');
        $courtCase = CourtCase::with('matter')->findOrFail($courtCaseId);

        return Inertia::render('Hearings/Create', [
            'courtCase' => $courtCase,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_case_id' => 'required|exists:court_cases,id',
            'date_time' => 'required|date',
            'procedure_result' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Hearing::create($validated);

        // Redirect back to the Matter page
        $courtCase = CourtCase::findOrFail($validated['court_case_id']);
        return redirect()->route('matters.show', $courtCase->matter_id)->with('success', 'Hearing added successfully.');
    }
}
