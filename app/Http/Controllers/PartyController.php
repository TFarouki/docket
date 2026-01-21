<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;

class PartyController extends Controller
{
    public function index()
    {
        return Inertia::render('Parties/Index', [
            'parties' => Party::query()
                ->when(RequestFacade::input('search'), function ($query, $search) {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('national_id', 'like', "%{$search}%");
                })
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'filters' => RequestFacade::only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Parties/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:client,opponent,other,lead',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Party::create($validated);

        return redirect()->route('parties.index')->with('success', 'Party created successfully.');
    }
    public function edit(Party $party)
    {
        return Inertia::render('Parties/Edit', [
            'party' => $party,
        ]);
    }

    public function update(Request $request, Party $party)
    {
        $validated = $request->validate([
            'type' => 'required|in:client,opponent,other,lead',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $party->update($validated);

        return redirect()->route('parties.index')->with('success', 'Party updated successfully.');
    }

    public function destroy(Party $party)
    {
        $party->delete();

        return redirect()->route('parties.index')->with('success', 'Party deleted successfully.');
    }
}
