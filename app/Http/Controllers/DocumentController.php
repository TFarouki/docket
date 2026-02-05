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

        // Sentinel Fix: Check access to the parent entity
        $authorized = false;

        switch ($document->documentable_type) {
            case 'App\Models\User':
                // User can view their own documents or if they have manage users permission
                if (auth()->id() === $document->documentable_id || auth()->user()->can('manage users')) {
                    $authorized = true;
                }
                break;
            case 'App\Models\Matter':
            case 'App\Models\CourtCase':
            case 'App\Models\Hearing':
                if (auth()->user()->can('view matters')) {
                    $authorized = true;
                }
                break;
            case 'App\Models\Party':
                if (auth()->user()->can('view parties')) {
                    $authorized = true;
                }
                break;
        }

        if (!$authorized) {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->title . '.' . $document->file_type);
    }
}
