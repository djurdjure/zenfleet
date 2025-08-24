<?php

// app/Http/Controllers/Admin/DocumentCategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DocumentCategory::class, 'document_category');
    }

    protected function resourceAbilityMap()
    {
        // Override the default map to use our custom permission
        return [
            'index' => 'manage',
            'create' => 'manage',
            'store' => 'manage',
            'edit' => 'manage',
            'update' => 'manage',
            'destroy' => 'manage',
        ];
    }

    protected function resourceMethodsWithoutModels()
    {
        return ['index', 'create', 'store'];
    }

    public function authorize($ability, $arguments = [])
    {
        // Custom authorization logic to use a single permission for the entire resource
        return parent::authorize('manage document_categories');
    }

    public function index()
    {
        $organization_id = Auth::user()->organization_id;
        $categories = DocumentCategory::where('organization_id', $organization_id)
            ->withCount('documents')
            ->paginate(15);
        return view('admin.document_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.document_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Auth::user()->organization->documentCategories()->create($validated);

        return redirect()->route('admin.document-categories.index')->with('success', 'Catégorie de document créée avec succès.');
    }

    public function edit(DocumentCategory $documentCategory)
    {
        return view('admin.document_categories.edit', compact('documentCategory'));
    }

    public function update(Request $request, DocumentCategory $documentCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $documentCategory->update($validated);

        return redirect()->route('admin.document-categories.index')->with('success', 'Catégorie de document mise à jour avec succès.');
    }

    public function destroy(DocumentCategory $documentCategory)
    {
        if ($documentCategory->documents()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie contenant des documents.');
        }

        $documentCategory->delete();
        return redirect()->route('admin.document-categories.index')->with('success', 'Catégorie de document supprimée avec succès.');
    }
}