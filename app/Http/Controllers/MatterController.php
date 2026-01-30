<?php

namespace App\Http\Controllers;

use App\Models\Matter;
use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request as RequestFacade;

class MatterController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('view matters'), 403);

        return Inertia::render('Matters/Index', [
            'matters' => Matter::with(['party:id,full_name', 'responsibleLawyer:id,name'])
                ->when(RequestFacade::input('search'), function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('reference_number', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(10)
                ->withQueryString()
                ->through(function ($matter) {
                    if ($matter->responsibleLawyer) {
                        $matter->responsibleLawyer->makeHidden('profile_photo_url');
                    }
                    return $matter;
                }),
            'filters' => RequestFacade::only(['search']),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->can('create matters'), 403);

        return Inertia::render('Matters/Create', [
            'clients' => Party::orderBy('full_name')->get(['id', 'full_name']),
            'lawyers' => User::orderBy('name')->get(['id', 'name'])->makeHidden(['profile_photo_url']), // Ideally filter by role 'lawyer'/'associate'
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('create matters'), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'party_id' => 'required|exists:parties,id',
            'responsible_lawyer_id' => 'nullable|exists:users,id',
            'reference_number' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:2000|max:'.(date('Y')+1),
            'type' => 'required|in:litigation,procedure,consultation',
            'case_type' => 'nullable|string|max:255',
            'court_name' => 'nullable|string|max:255',
            'status' => 'required|in:open,closed,pending,archived',
            'agreed_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Matter::create($validated);


        return redirect()->route('matters.index')->with('success', 'Matter created successfully.');
    }
    public function show(Matter $matter)
    {
        abort_unless(auth()->user()->can('view matters'), 403);

        // Optimize eager loading: select only necessary columns and hide appended attributes
        $matter->load([
            'party:id,full_name',
            'responsibleLawyer:id,name',
            'courtCases:id,matter_id,court_name,case_number,judge_name,opponent_lawyer,current_stage',
            'courtCases.hearings:id,court_case_id,date_time,procedure_result'
        ]);

        if ($matter->responsibleLawyer) {
            $matter->responsibleLawyer->makeHidden('profile_photo_url');
        }

        return Inertia::render('Matters/Show', [
            'matter' => $matter,
        ]);
    }
}
