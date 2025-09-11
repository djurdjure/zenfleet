@extends('layouts.admin.app')

@section('title', 'Modifier ' . $organization->name)

@push('styles')
{{-- Réutiliser les mêmes styles que create.blade.php --}}
<style>
.form-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    overflow: hidden;
}

.section-header {
    background: #f8fafc;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.section-body {
    padding: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.required-asterisk {
    color: #ef4444;
}

.logo-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: border-color 0.3s ease;
    cursor: pointer;
}

.logo-upload-area:hover {
    border-color: #3b82f6;
    background-color: #f8fafc;
}

.current-logo {
    max-width: 150px;
    max-height: 100px;
    border-radius: 8px;
    margin-bottom: 1rem;
    border: 2px solid #e5e7eb;
}
</style>
@endpush

@section('content')
<div class="organization-edit">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            
            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-edit text-warning me-3"></i>
                        Modifier {{ $organization->name }}
                    </h1>
                    <p class="text-muted mb-0">
                        Modifiez les informations de l'organisation
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.organizations.show', $organization) }}" 
                       class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>Voir détails
                    </a>
                    <a href="{{ route('admin.organizations.index') }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.organizations.update', $organization) }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Informations générales --}}
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Informations Générales
                        </h4>
                    </div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        Nom de l'organisation <span class="required-asterisk">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $organization->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="legal_name" class="form-label">
                                        Raison sociale <span class="required-asterisk">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('legal_name') is-invalid @enderror" 
                                           id="legal_name" 
                                           name="legal_name" 
                                           value="{{ old('legal_name', $organization->legal_name) }}" 
                                           required>
                                    @error('legal_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="organization_type" class="form-label">
                                        Type d'organisation <span class="required-asterisk">*</span>
                                    </label>
                                    <select class="form-select @error('organization_type') is-invalid @enderror" 
                                            id="organization_type" 
                                            name="organization_type" 
                                            required>
                                        <option value="">Sélectionnez un type</option>
                                        @foreach($organizationTypes as $value => $label)
                                            <option value="{{ $value }}" 
                                                    {{ old('organization_type', $organization->organization_type) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('organization_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">
                                        Statut <span class="required-asterisk">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="active" {{ old('status', $organization->status) === 'active' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactive" {{ old('status', $organization->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                        <option value="suspended" {{ old('status', $organization->status) === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Description de l'organisation...">{{ old('description', $organization->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Logo actuel et nouveau --}}
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">
                            <i class="fas fa-image text-primary me-2"></i>
                            Logo de l'organisation
                        </h4>
                    </div>
                    <div class="section-body">
                        @if($organization->logo_path && Storage::disk('public')->exists($organization->logo_path))
                            <div class="mb-3">
                                <label class="form-label">Logo actuel :</label>
                                <div>
                                    <img src="{{ Storage::disk('public')->url($organization->logo_path) }}" 
                                         alt="Logo actuel" 
                                         class="current-logo">
                                </div>
                            </div>
                        @endif
                        
                        <div class="logo-upload-area" onclick="document.getElementById('logo').click();">
                            <div id="logo-preview" class="d-none">
                                <img id="preview-image" class="current-logo" alt="Aperçu du nouveau logo">
                            </div>
                            <div id="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-3"></i>
                                <h6>{{ $organization->logo_path ? 'Changer le logo' : 'Ajouter un logo' }}</h6>
                                <p class="text-muted">
                                    Formats acceptés: JPG, PNG, SVG (max. 2MB)
                                </p>
                            </div>
                        </div>
                        <input type="file" 
                               class="d-none @error('logo') is-invalid @enderror" 
                               id="logo" 
                               name="logo" 
                               accept="image/*">
                        @error('logo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Informations de contact --}}
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            Informations de Contact
                        </h4>
                    </div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        Email principal <span class="required-asterisk">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $organization->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $organization->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="website" class="form-label">Site web</label>
                            <input type="url" 
                                   class="form-control @error('website') is-invalid @enderror" 
                                   id="website" 
                                   name="website" 
                                   value="{{ old('website', $organization->website) }}"
                                   placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('admin.organizations.show', $organization) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Même logique que create.blade.php pour l'upload de logo
    const logoInput = document.getElementById('logo');
    const uploadArea = document.querySelector('.logo-upload-area');
    const previewDiv = document.getElementById('logo-preview');
    const previewImage = document.getElementById('preview-image');
    const placeholder = document.getElementById('upload-placeholder');

    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewDiv.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // Validation temps réel
    const form = document.querySelector('form');
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (!this.value) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
});
</script>
@endpush
