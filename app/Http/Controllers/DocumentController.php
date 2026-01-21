<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $request->validate([
            'title' => 'required|string|max:255',
            'document_category_id' => 'required|exists:document_categories,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB limit
            'documentable_id' => 'required|integer',
            'documentable_type' => 'required|string',
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

        return Storage::disk('public')->download($document->file_path, $document->title . '.' . $document->file_type);
    }
}
