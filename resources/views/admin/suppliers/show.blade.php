@extends('layouts.admin.catalyst')

@section('title', $supplier->company_name . ' - Détails Fournisseur')

@section('content')
{{-- ====================================================================
 🏢 VUE DÉTAILS FOURNISSEUR - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 Design moderne avec:
 ✨ Layout 3 colonnes responsive
 ✨ Cards d'information structurées
 ✨ Badges de statut visuels
 ✨ Actions contextuelles
 ✨ Design cohérent bg-gray-50

 @version 2.0-World-Class-Enterprise
 @since 2025-10-23
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- HEADER AVEC BREADCRUMB --}}
        <div class="mb-6">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                    <x-iconify icon="lucide:home" class="w-4 h-4" />
                    Dashboard
                </a>
                <x-iconify icon="lucide:chevron-right" class="w-3 h-3 text-gray-400" />
                <a href="{{ route('admin.suppliers.index') }}" class="hover:text-blue-600 transition-colors">
                    Fournisseurs
                </a>
                <x-iconify icon="lucide:chevron-right" class="w-3 h-3 text-gray-400" />
                <span class="font-semibold text-gray-900">{{ $supplier->company_name }}</span>
            </nav>

            {{-- En-tête avec Actions --}}
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-iconify icon="lucide:building-2" class="w-8 h-8 text-blue-600" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $supplier->company_name }}</h1>
                        <div class="flex items-center gap-3">
                            {{-- Type --}}
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ \App\Models\Supplier::TYPES[$supplier->supplier_type] ?? $supplier->supplier_type }}
                            </span>
                            
                            {{-- Statuts --}}
                            @if($supplier->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <x-iconify icon="lucide:check-circle" class="w-4 h-4 mr-1" />
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <x-iconify icon="lucide:x-circle" class="w-4 h-4 mr-1" />
                                    Inactif
                                </span>
                            @endif

                            @if($supplier->is_preferred)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <x-iconify icon="lucide:heart" class="w-4 h-4 mr-1 fill-current" />
                                    Préféré
                                </span>
                            @endif

                            @if($supplier->is_certified)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    <x-iconify icon="lucide:badge-check" class="w-4 h-4 mr-1" />
                                    Certifié
                                </span>
                            @endif

                            @if($supplier->blacklisted)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <x-iconify icon="lucide:alert-triangle" class="w-4 h-4 mr-1" />
                                    Liste noire
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    @can('edit suppliers')
                        <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-semibold shadow-sm">
                            <x-iconify icon="lucide:pencil" class="w-5 h-5" />
                            Modifier
                        </a>
                    @endcan
                    @can('delete suppliers')
                        <form action="{{ route('admin.suppliers.destroy', $supplier) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir archiver ce fournisseur ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-semibold shadow-sm">
                                <x-iconify icon="lucide:archive" class="w-5 h-5" />
                                Archiver
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>

        {{-- LAYOUT 3 COLONNES --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLONNE GAUCHE (2/3) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- INFORMATIONS GÉNÉRALES --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600" />
                            Informations Générales
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Raison Sociale --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Raison Sociale</label>
                                <p class="text-base font-semibold text-gray-900">{{ $supplier->company_name }}</p>
                            </div>

                            {{-- Type --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Type Fournisseur</label>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ \App\Models\Supplier::TYPES[$supplier->supplier_type] ?? $supplier->supplier_type }}
                                </p>
                            </div>

                            {{-- Registre Commerce --}}
                            @if($supplier->trade_register)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Registre Commerce</label>
                                    <p class="text-base font-semibold text-gray-900">{{ $supplier->trade_register }}</p>
                                </div>
                            @endif

                            {{-- NIF --}}
                            @if($supplier->nif)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">NIF</label>
                                    <p class="text-base font-semibold text-gray-900">{{ $supplier->nif }}</p>
                                </div>
                            @endif

                            {{-- NIS --}}
                            @if($supplier->nis)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">NIS</label>
                                    <p class="text-base font-semibold text-gray-900">{{ $supplier->nis }}</p>
                                </div>
                            @endif

                            {{-- AI --}}
                            @if($supplier->ai)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Article d'Imposition</label>
                                    <p class="text-base font-semibold text-gray-900">{{ $supplier->ai }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- CONTACT --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="lucide:user" class="w-5 h-5 text-blue-600" />
                            Contact Principal
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nom Complet --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nom Complet</label>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ $supplier->contact_first_name }} {{ $supplier->contact_last_name }}
                                </p>
                            </div>

                            {{-- Téléphone --}}
                            @if($supplier->contact_phone)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Téléphone</label>
                                    <a href="tel:{{ $supplier->contact_phone }}" 
                                       class="text-base font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-2">
                                        <x-iconify icon="lucide:phone" class="w-4 h-4" />
                                        {{ $supplier->contact_phone }}
                                    </a>
                                </div>
                            @endif

                            {{-- Email --}}
                            @if($supplier->contact_email)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                                    <a href="mailto:{{ $supplier->contact_email }}" 
                                       class="text-base font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-2">
                                        <x-iconify icon="lucide:mail" class="w-4 h-4" />
                                        {{ $supplier->contact_email }}
                                    </a>
                                </div>
                            @endif

                            {{-- Téléphone Entreprise --}}
                            @if($supplier->phone)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Téléphone Entreprise</label>
                                    <a href="tel:{{ $supplier->phone }}" 
                                       class="text-base font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-2">
                                        <x-iconify icon="lucide:phone" class="w-4 h-4" />
                                        {{ $supplier->phone }}
                                    </a>
                                </div>
                            @endif

                            {{-- Email Entreprise --}}
                            @if($supplier->email)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Email Entreprise</label>
                                    <a href="mailto:{{ $supplier->email }}" 
                                       class="text-base font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-2">
                                        <x-iconify icon="lucide:mail" class="w-4 h-4" />
                                        {{ $supplier->email }}
                                    </a>
                                </div>
                            @endif

                            {{-- Website --}}
                            @if($supplier->website)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Site Web</label>
                                    <a href="{{ $supplier->website }}" 
                                       target="_blank"
                                       class="text-base font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-2">
                                        <x-iconify icon="lucide:globe" class="w-4 h-4" />
                                        {{ $supplier->website }}
                                        <x-iconify icon="lucide:external-link" class="w-3 h-3" />
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- LOCALISATION --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="lucide:map-pin" class="w-5 h-5 text-blue-600" />
                            Localisation
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Adresse --}}
                            @if($supplier->address)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Adresse</label>
                                    <p class="text-base font-semibold text-gray-900">{{ $supplier->address }}</p>
                                </div>
                            @endif

                            {{-- Wilaya --}}
                            @if($supplier->wilaya)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Wilaya</label>
                                    <p class="text-base font-semibold text-gray-900">
                                        {{ \App\Models\Supplier::WILAYAS[$supplier->wilaya] ?? $supplier->wilaya }}
                                    </p>
                                </div>
                            @endif

                            {{-- Ville --}}
                            @if($supplier->city)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Ville</label>
                                    <p class="text-base font-semibold text-gray-900">{{ $supplier->city }}</p>
                                </div>
                            @endif

                            {{-- Commune --}}
                            @if($supplier->commune)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Commune</label>
                                    <p class="text-base font-semibold text-gray-900">{{ $supplier->commune }}</p>
                                </div>
                            @endif

                            {{-- Code Postal --}}
                            @if($supplier->postal_code)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Code Postal</label>
                                    <p class="text-base font-semibold text-gray-900">{{ $supplier->postal_code }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- NOTES --}}
                @if($supplier->notes)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:file-text" class="w-5 h-5 text-blue-600" />
                                Notes
                            </h2>
                        </div>
                        <div class="p-6">
                            <p class="text-base text-gray-700 whitespace-pre-line">{{ $supplier->notes }}</p>
                        </div>
                    </div>
                @endif

                {{-- RAISON BLACKLIST --}}
                @if($supplier->blacklisted && $supplier->blacklist_reason)
                    <div class="bg-red-50 border border-red-200 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-red-200">
                            <h2 class="text-lg font-bold text-red-900 flex items-center gap-2">
                                <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 text-red-600" />
                                Raison Liste Noire
                            </h2>
                        </div>
                        <div class="p-6">
                            <p class="text-base text-red-800">{{ $supplier->blacklist_reason }}</p>
                        </div>
                    </div>
                @endif

            </div>

            {{-- COLONNE DROITE (1/3) --}}
            <div class="space-y-6">

                {{-- SCORES & RATINGS --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="lucide:star" class="w-5 h-5 text-yellow-500" />
                            Évaluation
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Rating --}}
                        @if($supplier->rating)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Rating Global</label>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <x-iconify icon="lucide:star" 
                                                       class="w-5 h-5 {{ $i <= $supplier->rating ? 'text-yellow-400 fill-current' : 'text-gray-300' }}" />
                                        @endfor
                                    </div>
                                    <span class="text-lg font-bold text-gray-900">{{ number_format($supplier->rating, 1) }}/5</span>
                                </div>
                            </div>
                        @endif

                        {{-- Score Qualité --}}
                        @if($supplier->quality_score)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Score Qualité</label>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $supplier->quality_score }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">{{ number_format($supplier->quality_score, 1) }}%</span>
                                </div>
                            </div>
                        @endif

                        {{-- Score Fiabilité --}}
                        @if($supplier->reliability_score)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Score Fiabilité</label>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $supplier->reliability_score }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">{{ number_format($supplier->reliability_score, 1) }}%</span>
                                </div>
                            </div>
                        @endif

                        {{-- Temps de Réponse --}}
                        @if($supplier->response_time_hours)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Temps de Réponse</label>
                                <p class="text-base font-semibold text-gray-900">{{ $supplier->response_time_hours }}h</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- SPÉCIALITÉS --}}
                @if($supplier->specialties && count($supplier->specialties) > 0)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:wrench" class="w-5 h-5 text-blue-600" />
                                Spécialités
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                @foreach($supplier->specialties as $specialty)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $specialty }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- CERTIFICATIONS --}}
                @if($supplier->certifications && count($supplier->certifications) > 0)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:award" class="w-5 h-5 text-purple-600" />
                                Certifications
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                @foreach($supplier->certifications as $certification)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <x-iconify icon="lucide:badge-check" class="w-3 h-3 mr-1" />
                                        {{ $certification }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ZONES DE SERVICE --}}
                @if($supplier->service_areas && count($supplier->service_areas) > 0)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:map" class="w-5 h-5 text-green-600" />
                                Zones de Service
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                @foreach($supplier->service_areas as $area)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-iconify icon="lucide:map-pin" class="w-3 h-3 mr-1" />
                                        {{ $area }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- INFORMATIONS BANCAIRES --}}
                @if($supplier->bank_name || $supplier->account_number || $supplier->rib)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:landmark" class="w-5 h-5 text-blue-600" />
                                Informations Bancaires
                            </h2>
                        </div>
                        <div class="p-6 space-y-3">
                            @if($supplier->bank_name)
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Banque</label>
                                    <p class="text-sm font-semibold text-gray-900">{{ $supplier->bank_name }}</p>
                                </div>
                            @endif
                            @if($supplier->account_number)
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">N° Compte</label>
                                    <p class="text-sm font-mono text-gray-900">{{ $supplier->account_number }}</p>
                                </div>
                            @endif
                            @if($supplier->rib)
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">RIB</label>
                                    <p class="text-sm font-mono text-gray-900">{{ $supplier->rib }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- MÉTADONNÉES --}}
                <div class="bg-gray-50 rounded-lg border border-gray-200">
                    <div class="p-4 space-y-2 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>Créé le:</span>
                            <span class="font-semibold">{{ $supplier->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Modifié le:</span>
                            <span class="font-semibold">{{ $supplier->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>
@endsection
