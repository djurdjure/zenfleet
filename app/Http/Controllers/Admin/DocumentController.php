<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Document::class, 'document');
    }

    public function index()
    {
        // Route vers le nouveau composant Livewire DocumentManagerIndex
        // Le composant gère la pagination, recherche Full-Text, et filtres avancés
        return view('admin.documents.index-livewire');
    }

    public function create()
    {
        $organization_id = Auth::user()->organization_id;

        $categories = DocumentCategory::where('organization_id', $organization_id)
            ->orWhere('is_default', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $vehicles = Vehicle::where('organization_id', $organization_id)->get(['id', 'brand', 'model', 'registration_plate']);
        $drivers = User::where('organization_id', $organization_id)->has('driver')->get(['id', 'first_name', 'last_name']);
        $suppliers = Supplier::where('organization_id', $organization_id)->get(['id', 'name']);

        return view('admin.documents.create', compact('categories', 'vehicles', 'drivers', 'suppliers'));
    }

    private function validateAndPrepareData(Request $request, ?Document $document = null)
    {
        $rules = [
            'document_category_id' => 'required|exists:document_categories,id',
            'description' => 'nullable|string|max:1000',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'linked_vehicles' => 'nullable|array',
            'linked_vehicles.*' => 'exists:vehicles,id',
            'linked_drivers' => 'nullable|array',
            'linked_drivers.*' => 'exists:users,id',
            'linked_suppliers' => 'nullable|array',
            'linked_suppliers.*' => 'exists:suppliers,id',
            'extra_metadata' => 'nullable|array',
        ];

        if (!$document) { // a new document requires a file
            $rules['document_file'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240';
        }

        $validatedData = $request->validate($rules);
        
        $selectedCategory = DocumentCategory::find($request->input('document_category_id'));

        if ($selectedCategory && $selectedCategory->meta_schema) {
            $metaSchema = $selectedCategory->meta_schema; // Already cast to array
            $extraMetadataRules = [];

            if (isset($metaSchema['fields']) && is_array($metaSchema['fields'])) {
                foreach ($metaSchema['fields'] as $field) {
                    $fieldName = 'extra_metadata.' . $field['name'];
                    $fieldRules = [];

                    if (isset($field['required']) && $field['required']) {
                        $fieldRules[] = 'required';
                    } else {
                        $fieldRules[] = 'nullable';
                    }

                    switch ($field['type']) {
                        case 'string':
                        case 'textarea':
                            $fieldRules[] = 'string';
                            break;
                        case 'number':
                            $fieldRules[] = 'numeric';
                            break;
                        case 'date':
                            $fieldRules[] = 'date';
                            break;
                        case 'multiselect':
                            $fieldRules[] = 'array';
                            if (isset($field['options']) && is_array($field['options'])) {
                                // This creates a rule like 'in:A,B,C' for each item in the array
                                $extraMetadataRules[$fieldName . '.*'] = 'in:' . implode(',', $field['options']);
                            }
                            break;
                        case 'entity_select':
                            $fieldRules[] = 'integer';
                            if (isset($field['entity'])) {
                                $tableName = Str::plural($field['entity']);
                                $fieldRules[] = 'exists:' . $tableName . ',id';
                            }
                            break;
                    }
                    $extraMetadataRules[$fieldName] = implode('|', $fieldRules);
                }
            }
            // Validate the extra metadata
            $validatedExtraMetadata = Validator::make($request->input(), $extraMetadataRules)->validate();
            $validatedData['extra_metadata'] = $validatedExtraMetadata['extra_metadata'] ?? [];
        }
        
        return $validatedData;
    }


    public function store(Request $request)
    {
        $validatedData = $this->validateAndPrepareData($request);

        $organization = Auth::user()->organization;
        $file = $request->file('document_file');
        
        $category = DocumentCategory::find($validatedData['document_category_id']);
        $pathDirectory = sprintf('documents/%d/%s/%s/%s',
            $organization->id,
            Str::slug($category->name),
            date('Y'),
            date('m')
        );
        $path = $file->store($pathDirectory, 's3');

        $documentData = array_merge($validatedData, [
            'organization_id' => $organization->id,
            'user_id' => Auth::id(),
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size_in_bytes' => $file->getSize(),
        ]);
        
        $document = Document::create($documentData);

        $document->vehicles()->sync($request->input('linked_vehicles', []));
        $document->users()->sync($request->input('linked_drivers', []));
        $document->suppliers()->sync($request->input('linked_suppliers', []));

        return redirect()->route('admin.documents.index')->with('success', 'Document importé avec succès.');
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);
        return view('admin.documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $organization_id = Auth::user()->organization_id;

        $categories = DocumentCategory::where('organization_id', $organization_id)
            ->orWhere('is_default', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $vehicles = Vehicle::where('organization_id', $organization_id)->get(['id', 'brand', 'model', 'registration_plate']);
        $drivers = User::where('organization_id', $organization_id)->has('driver')->get(['id', 'first_name', 'last_name']);
        $suppliers = Supplier::where('organization_id', $organization_id)->get(['id', 'name']);

        $document->load('vehicles', 'users', 'suppliers');

        return view('admin.documents.edit', compact('document', 'categories', 'vehicles', 'drivers', 'suppliers'));
    }

    public function update(Request $request, Document $document)
    {
        $validatedData = $this->validateAndPrepareData($request, $document);

        $document->update($validatedData);
        
        $document->vehicles()->sync($request->input('linked_vehicles', []));
        $document->users()->sync($request->input('linked_drivers', []));
        $document->suppliers()->sync($request->input('linked_suppliers', []));

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