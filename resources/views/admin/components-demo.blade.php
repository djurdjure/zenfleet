@extends('layouts.admin.catalyst')

@section('title', 'Démonstration des Composants - ZenFleet Design System')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                🎨 ZenFleet Design System
            </h1>
            <p class="text-gray-600">
                Composants Tailwind CSS utility-first réutilisables
            </p>
        </div>

        {{-- Buttons --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Buttons</h2>
            
            <div class="space-y-4">
                {{-- Variantes --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Variantes</h3>
                    <div class="flex flex-wrap gap-3">
                        <x-button variant="primary">Primary</x-button>
                        <x-button variant="secondary">Secondary</x-button>
                        <x-button variant="danger">Danger</x-button>
                        <x-button variant="success">Success</x-button>
                        <x-button variant="ghost">Ghost</x-button>
                    </div>
                </div>

                {{-- Tailles --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Tailles</h3>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-button variant="primary" size="sm">Small</x-button>
                        <x-button variant="primary" size="md">Medium</x-button>
                        <x-button variant="primary" size="lg">Large</x-button>
                    </div>
                </div>

                {{-- Avec icônes --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Avec Icônes</h3>
                    <div class="flex flex-wrap gap-3">
                        <x-button variant="primary" icon="plus" iconPosition="left">
                            Nouveau véhicule
                        </x-button>
                        <x-button variant="danger" icon="trash" iconPosition="left" size="sm">
                            Supprimer
                        </x-button>
                        <x-button variant="secondary" icon="pencil" iconPosition="right">
                            Éditer
                        </x-button>
                    </div>
                </div>

                {{-- Disabled --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">État désactivé</h3>
                    <div class="flex flex-wrap gap-3">
                        <x-button variant="primary" disabled>Disabled Primary</x-button>
                        <x-button variant="secondary" disabled>Disabled Secondary</x-button>
                    </div>
                </div>

                {{-- Liens --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Liens stylés comme boutons</h3>
                    <div class="flex flex-wrap gap-3">
                        <x-button href="/admin/vehicles" variant="primary">
                            Voir véhicules
                        </x-button>
                        <x-button href="/admin/dashboard" variant="secondary">
                            Dashboard
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inputs --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Inputs</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Input simple --}}
                <x-input 
                    name="plate" 
                    label="Immatriculation" 
                    placeholder="XX-123-YY"
                />

                {{-- Input avec icône --}}
                <x-input 
                    name="email" 
                    type="email" 
                    label="Email" 
                    icon="envelope"
                    placeholder="nom@exemple.com"
                />

                {{-- Input requis --}}
                <x-input 
                    name="brand" 
                    label="Marque" 
                    placeholder="Toyota"
                    required
                />

                {{-- Input avec erreur --}}
                <x-input 
                    name="phone" 
                    label="Téléphone" 
                    error="Le numéro de téléphone est invalide"
                    value="123"
                />

                {{-- Input avec aide --}}
                <x-input 
                    name="mileage" 
                    type="number" 
                    label="Kilométrage" 
                    helpText="En kilomètres"
                    placeholder="50000"
                />

                {{-- Input désactivé --}}
                <x-input 
                    name="status" 
                    label="Statut" 
                    value="Actif"
                    disabled
                />
            </div>
        </div>

        {{-- Alerts --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Alerts</h2>
            
            <div class="space-y-4">
                <x-alert type="success" title="Succès">
                    Le véhicule a été créé avec succès.
                </x-alert>

                <x-alert type="error" title="Erreur">
                    Une erreur est survenue lors de l'enregistrement.
                </x-alert>

                <x-alert type="warning" title="Attention">
                    Ce véhicule nécessite une maintenance dans 7 jours.
                </x-alert>

                <x-alert type="info">
                    Les données de kilométrage sont mises à jour toutes les heures.
                </x-alert>

                <x-alert type="success" title="Avec bouton fermer" dismissible>
                    Cette alerte peut être fermée par l'utilisateur.
                </x-alert>
            </div>
        </div>

        {{-- Badges --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Badges</h2>
            
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Variantes</h3>
                    <div class="flex flex-wrap gap-3">
                        <x-badge type="success">Actif</x-badge>
                        <x-badge type="error">Hors service</x-badge>
                        <x-badge type="warning">En maintenance</x-badge>
                        <x-badge type="info">Nouveau</x-badge>
                        <x-badge type="primary">Important</x-badge>
                        <x-badge type="gray">Archivé</x-badge>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Tailles</h3>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-badge type="success" size="sm">Small</x-badge>
                        <x-badge type="success" size="md">Medium</x-badge>
                        <x-badge type="success" size="lg">Large</x-badge>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Dans un tableau</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Véhicule</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">AA-123-BB</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge type="success">Actif</x-badge>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">CC-456-DD</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge type="warning">En maintenance</x-badge>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">EE-789-FF</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge type="error">Hors service</x-badge>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Demo --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Modal</h2>
            
            <div class="space-y-3">
                <x-button @click="$dispatch('open-modal', 'demo-modal-sm')" variant="primary">
                    Ouvrir Modal Small
                </x-button>
                <x-button @click="$dispatch('open-modal', 'demo-modal-lg')" variant="secondary">
                    Ouvrir Modal Large
                </x-button>
                <x-button @click="$dispatch('open-modal', 'demo-modal-form')" variant="success">
                    Ouvrir Modal avec Formulaire
                </x-button>
            </div>
        </div>

        {{-- Code Examples --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Exemples de Code</h2>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Button</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-button variant="primary" icon="plus"&gt;
    Nouveau véhicule
&lt;/x-button&gt;</code></pre>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Input</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-input 
    name="plate" 
    label="Immatriculation" 
    icon="truck"
    required
/&gt;</code></pre>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Alert</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-alert type="success" title="Succès" dismissible&gt;
    Opération réussie
&lt;/x-alert&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
<x-modal name="demo-modal-sm" title="Modal Small" maxWidth="sm">
    <p class="text-gray-700">Ceci est un petit modal avec maxWidth="sm"</p>
    <div class="mt-4 flex justify-end">
        <x-button @click="$dispatch('close-modal', 'demo-modal-sm')" variant="secondary">
            Fermer
        </x-button>
    </div>
</x-modal>

<x-modal name="demo-modal-lg" title="Modal Large" maxWidth="2xl">
    <p class="text-gray-700 mb-4">Ceci est un grand modal avec maxWidth="2xl"</p>
    <p class="text-gray-600 text-sm">
        Les modals utilisent Alpine.js pour l'interactivité et Tailwind CSS pour le style.
        Ils sont automatiquement responsive et accessibles (ARIA, focus trap, échap pour fermer).
    </p>
    <div class="mt-6 flex justify-end gap-3">
        <x-button @click="$dispatch('close-modal', 'demo-modal-lg')" variant="secondary">
            Annuler
        </x-button>
        <x-button @click="$dispatch('close-modal', 'demo-modal-lg')" variant="primary">
            Confirmer
        </x-button>
    </div>
</x-modal>

<x-modal name="demo-modal-form" title="Créer un véhicule" maxWidth="lg">
    <form class="space-y-4">
        <x-input 
            name="plate" 
            label="Immatriculation" 
            icon="truck"
            placeholder="XX-123-YY"
            required
        />
        <x-input 
            name="brand" 
            label="Marque" 
            placeholder="Toyota"
            required
        />
        <x-input 
            name="model" 
            label="Modèle" 
            placeholder="Corolla"
        />
        <div class="flex justify-end gap-3 pt-4">
            <x-button @click="$dispatch('close-modal', 'demo-modal-form')" type="button" variant="secondary">
                Annuler
            </x-button>
            <x-button type="submit" variant="primary" icon="check">
                Créer
            </x-button>
        </div>
    </form>
</x-modal>
@endsection
