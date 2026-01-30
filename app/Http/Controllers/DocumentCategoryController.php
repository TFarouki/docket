<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->can('create documents')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|unique:document_categories,name',
            'type' => 'nullable|string',
        ]);

        $category = DocumentCategory::create([
            'name' => $request->name,
            'type' => $request->type ?? 'general',
        ]);

        return response()->json($category);
    }

    public function index()
    {
        if (!auth()->user()->can('view documents')) {
            abort(403);
        }

        return response()->json(DocumentCategory::orderBy('name')->get());
    }
}
