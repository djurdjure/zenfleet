<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Document::class, 'document');
    }

    public function index()
    {
        $organization_id = Auth::user()->organization_id;
        $documents = Document::with(['category', 'uploader'])
            ->where('organization_id', $organization_id)
            ->latest()
            ->paginate(20);
            
        return view('admin.documents.index', compact('documents'));
    }

    public function create()
    {
        $organization_id = Auth::user()->organization_id;
        $categories = DocumentCategory::where('organization_id', $organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.documents.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_category_id' => 'required|exists:document_categories,id',
            'description' => 'nullable|string|max:1000',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240', // Max 10MB
        ]);

        $organization = Auth::user()->organization;
        $file = $request->file('document_file');
        
        $categoryName = DocumentCategory::find($request->document_category_id)->name;
        $pathDirectory = sprintf('documents/%d/%s/%s/%s',
            $organization->id,
            Str::slug($categoryName),
            date('Y'),
            date('m')
        );

        $path = $file->store($pathDirectory, 's3');

        Document::create([
            'organization_id' => $organization->id,
            'user_id' => Auth::id(),
            'document_category_id' => $request->document_category_id,
            'description' => $request->description,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size_in_bytes' => $file->getSize(),
        ]);

        return redirect()->route('admin.documents.index')->with('success', 'Document importé avec succès.');
    }
    
    public function show(Document $document)
    {
        return view('admin.documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $organization_id = Auth::user()->organization_id;
        $categories = DocumentCategory::where('organization_id', $organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.documents.edit', compact('document', 'categories'));
    }

    public function update(Request $request, Document $document)
    {
        $request->validate([
            'document_category_id' => 'required|exists:document_categories,id',
            'description' => 'nullable|string|max:1000',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
        ]);

        $document->update($request->all());

        return redirect()->route('admin.documents.index')->with('success', 'Document mis à jour avec succès.');
    }

    public function destroy(Document $document)
    {
        if ($document->file_path && Storage::disk('s3')->exists($document->file_path)) {
            Storage::disk('s3')->delete($document->file_path);
        }
        
        $document->delete();

        return redirect()->route('admin.documents.index')->with('success', 'Document supprimé avec succès.');
    }
}