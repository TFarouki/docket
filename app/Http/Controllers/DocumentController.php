<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('create documents')) {
            abort(403);
        }

        $allowedModels = [
            'App\Models\User',
            'App\Models\Matter',
            'App\Models\Party',
            'App\Models\CourtCase',
            'App\Models\Hearing',
        ];

        $request->validate([
            'title' => 'required|string|max:255',
            'document_category_id' => 'required|exists:document_categories,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB limit
            'documentable_type' => ['required', 'string', Rule::in($allowedModels)],
            'documentable_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request, $allowedModels) {
                    $type = $request->input('documentable_type');
                    if (!in_array($type, $allowedModels)) {
                        return; // Type validation handles this
                    }
                    if (!class_exists($type)) {
                         $fail("The document type class does not exist.");
                         return;
                    }
                    if (!$type::where('id', $value)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                },
            ],
            'description' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents/' . strtolower(class_basename($request->documentable_type)), 'public');

        Document::create([
            'title' => $request->title,
            'document_category_id' => $request->document_category_id,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'description' => $request->description,
            'uploader_id' => $request->user()->id,
            'documentable_id' => $request->documentable_id,
            'documentable_type' => $request->documentable_type,
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Document $document)
    {
        if (!auth()->user()->can('delete documents')) {
            abort(403);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Download the document.
     */
    public function download(Document $document)
    {
        if (!auth()->user()->can('view documents')) {
            abort(403);
        }

        $parent = $document->documentable;

        // Security check: Ensure user has access to the parent resource
        if ($parent instanceof \App\Models\User) {
            if (auth()->id() !== $parent->id && !auth()->user()->can('manage users')) {
                abort(403, 'Unauthorized access to user documents.');
            }
        } elseif ($parent instanceof \App\Models\Matter) {
            if (!auth()->user()->can('view matters')) {
                abort(403, 'Unauthorized access to matter documents.');
            }
        } elseif ($parent instanceof \App\Models\Party) {
            if (!auth()->user()->can('view parties')) {
                abort(403, 'Unauthorized access to party documents.');
            }
        } elseif ($parent instanceof \App\Models\CourtCase || $parent instanceof \App\Models\Hearing) {
            // These belong to matters, so checking 'view matters' is appropriate
            if (!auth()->user()->can('view matters')) {
                abort(403, 'Unauthorized access to case documents.');
            }
        } else {
            // Default deny for any unhandled documentable types
            abort(403, 'Unauthorized access.');
        }

        return Storage::disk('public')->download($document->file_path, $document->title . '.' . $document->file_type);
    }
}
