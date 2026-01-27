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
        if (!auth()->user()->can('view parties')) {
            abort(403);
        }

        return Inertia::render('Parties/Index', [
            'parties' => Party::query()
                ->when(RequestFacade::input('search'), function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('national_id', 'like', "%{$search}%");
                    });
                })
                ->when(RequestFacade::input('type'), function ($query, $type) {
                    $query->where('type', $type);
                })
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'filters' => RequestFacade::only(['search', 'type']),
        ]);
    }

    public function create()
    {
        if (!auth()->user()->can('create parties')) {
            abort(403);
        }

        return Inertia::render('Parties/Create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create parties')) {
            abort(403);
        }

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
        if (!auth()->user()->can('edit parties')) {
            abort(403);
        }

        return Inertia::render('Parties/Edit', [
            'party' => $party,
        ]);
    }

    public function update(Request $request, Party $party)
    {
        if (!auth()->user()->can('edit parties')) {
            abort(403);
        }

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
        if (!auth()->user()->can('delete parties')) {
            abort(403);
        }

        $party->delete();

        return redirect()->route('parties.index')->with('success', 'Party deleted successfully.');
    }
}
