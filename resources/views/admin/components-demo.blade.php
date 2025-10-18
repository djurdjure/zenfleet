@extends('layouts.admin.catalyst')

@section('title', 'Démonstration des Composants - ZenFleet Design System')

@section('content')
<section class="bg-white dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-7xl lg:py-16">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                🎨 ZenFleet Design System
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Composants Flowbite-inspired avec Tailwind CSS et support dark mode
            </p>
        </div>

        {{-- Buttons --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Buttons</h2>
            
            <div class="space-y-4">
                {{-- Variantes --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Variantes</h3>
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
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Tailles</h3>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-button variant="primary" size="sm">Small</x-button>
                        <x-button variant="primary" size="md">Medium</x-button>
                        <x-button variant="primary" size="lg">Large</x-button>
                    </div>
                </div>

                {{-- Avec icônes --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Avec Icônes</h3>
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
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">État désactivé</h3>
                    <div class="flex flex-wrap gap-3">
                        <x-button variant="primary" disabled>Disabled Primary</x-button>
                        <x-button variant="secondary" disabled>Disabled Secondary</x-button>
                    </div>
                </div>

                {{-- Liens --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Liens stylés comme boutons</h3>
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

        {{-- Form Elements --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Form Elements (Flowbite-inspired)</h2>

            <div class="space-y-8">
                {{-- Inputs --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Inputs</h3>
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

                {{-- Select --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Select</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Select simple --}}
                        <x-select
                            name="vehicle_type"
                            label="Type de véhicule"
                            :options="[
                                '' => 'Sélectionner un type',
                                'sedan' => 'Berline',
                                'suv' => 'SUV',
                                'truck' => 'Camion',
                                'van' => 'Fourgon'
                            ]"
                        />

                        {{-- Select requis --}}
                        <x-select
                            name="fuel_type"
                            label="Type de carburant"
                            :options="[
                                'diesel' => 'Diesel',
                                'gasoline' => 'Essence',
                                'electric' => 'Électrique',
                                'hybrid' => 'Hybride'
                            ]"
                            selected="diesel"
                            required
                        />

                        {{-- Select avec erreur --}}
                        <x-select
                            name="status_select"
                            label="Statut du véhicule"
                            :options="[
                                'active' => 'Actif',
                                'maintenance' => 'En maintenance',
                                'inactive' => 'Inactif'
                            ]"
                            error="Veuillez sélectionner un statut"
                        />

                        {{-- Select avec aide --}}
                        <x-select
                            name="driver"
                            label="Chauffeur assigné"
                            :options="[
                                '' => 'Sélectionner un chauffeur',
                                '1' => 'Jean Dupont',
                                '2' => 'Marie Martin',
                                '3' => 'Pierre Dubois'
                            ]"
                            helpText="Sélectionnez le chauffeur principal"
                        />
                    </div>
                </div>

                {{-- Textarea --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Textarea</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Textarea simple --}}
                        <x-textarea
                            name="description"
                            label="Description"
                            placeholder="Décrivez le véhicule..."
                            rows="4"
                        />

                        {{-- Textarea requis --}}
                        <x-textarea
                            name="notes"
                            label="Notes de maintenance"
                            placeholder="Entrez les notes..."
                            rows="4"
                            required
                        />

                        {{-- Textarea avec erreur --}}
                        <x-textarea
                            name="comments"
                            label="Commentaires"
                            value="Trop court"
                            error="Le commentaire doit contenir au moins 20 caractères"
                            rows="4"
                        />

                        {{-- Textarea avec aide --}}
                        <x-textarea
                            name="observations"
                            label="Observations"
                            placeholder="Observations générales..."
                            helpText="Maximum 500 caractères"
                            rows="4"
                        />
                    </div>
                </div>

                {{-- TomSelect --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">TomSelect (Liste déroulante avec recherche)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- TomSelect simple --}}
                        <x-tom-select
                            name="vehicle_tomselect"
                            label="Véhicule (avec recherche)"
                            :options="[
                                '1' => 'AA-123-BB - Toyota Corolla',
                                '2' => 'CC-456-DD - Renault Master',
                                '3' => 'EE-789-FF - Peugeot Partner',
                                '4' => 'GG-012-HH - Citroën Berlingo',
                                '5' => 'II-345-JJ - Ford Transit',
                                '6' => 'KK-678-LL - Mercedes Sprinter',
                                '7' => 'MM-901-NN - Volkswagen Caddy',
                                '8' => 'OO-234-PP - Fiat Ducato'
                            ]"
                            placeholder="Rechercher un véhicule..."
                        />

                        {{-- TomSelect requis --}}
                        <x-tom-select
                            name="driver_tomselect"
                            label="Chauffeur assigné"
                            :options="[
                                '1' => 'Jean Dupont',
                                '2' => 'Marie Martin',
                                '3' => 'Pierre Dubois',
                                '4' => 'Sophie Bernard',
                                '5' => 'Luc Petit'
                            ]"
                            placeholder="Sélectionner un chauffeur..."
                            required
                        />

                        {{-- TomSelect avec erreur --}}
                        <x-tom-select
                            name="fuel_type_tomselect"
                            label="Type de carburant"
                            :options="[
                                'diesel' => 'Diesel',
                                'gasoline' => 'Essence',
                                'electric' => 'Électrique',
                                'hybrid' => 'Hybride'
                            ]"
                            error="Ce champ est requis"
                        />

                        {{-- TomSelect avec aide --}}
                        <x-tom-select
                            name="maintenance_type"
                            label="Type de maintenance"
                            :options="[
                                'preventive' => 'Préventive',
                                'corrective' => 'Corrective',
                                'emergency' => 'Urgence',
                                'inspection' => 'Inspection'
                            ]"
                            helpText="Sélectionnez le type de maintenance à effectuer"
                        />
                    </div>
                </div>

                {{-- Datepicker & TimePicker --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Datepicker & TimePicker</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Datepicker simple --}}
                        <x-datepicker
                            name="maintenance_date"
                            label="Date de maintenance"
                            placeholder="Choisir une date"
                        />

                        {{-- Datepicker requis --}}
                        <x-datepicker
                            name="assignment_date"
                            label="Date d'affectation"
                            placeholder="JJ/MM/AAAA"
                            required
                        />

                        {{-- Datepicker avec contraintes --}}
                        <x-datepicker
                            name="start_date"
                            label="Date de début"
                            :minDate="date('Y-m-d')"
                            placeholder="Sélectionner..."
                            helpText="La date ne peut pas être dans le passé"
                        />

                        {{-- Datepicker avec erreur --}}
                        <x-datepicker
                            name="end_date"
                            label="Date de fin"
                            error="La date de fin doit être après la date de début"
                        />

                        {{-- TimePicker simple --}}
                        <x-time-picker
                            name="departure_time"
                            label="Heure de départ"
                            placeholder="HH:MM"
                        />

                        {{-- TimePicker requis --}}
                        <x-time-picker
                            name="arrival_time"
                            label="Heure d'arrivée"
                            placeholder="00:00"
                            required
                        />

                        {{-- TimePicker avec valeur --}}
                        <x-time-picker
                            name="scheduled_time"
                            label="Heure planifiée"
                            value="09:00"
                        />

                        {{-- TimePicker avec aide --}}
                        <x-time-picker
                            name="maintenance_time"
                            label="Heure de maintenance"
                            helpText="Format 24 heures (HH:MM)"
                        />
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Alerts</h2>
            
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Badges</h2>

            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Variantes</h3>
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
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Tailles</h3>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-badge type="success" size="sm">Small</x-badge>
                        <x-badge type="success" size="md">Medium</x-badge>
                        <x-badge type="success" size="lg">Large</x-badge>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Exemple de tableau (style Kilométrage)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Véhicule
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kilométrage
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">AA-123-BB</div>
                                                <div class="text-sm text-gray-500">Toyota Corolla</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        85,432 km
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex flex-col">
                                            <span class="font-medium">18/10/2025</span>
                                            <span class="text-gray-500">14:30</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button class="text-blue-600 hover:text-blue-900 font-medium">Voir</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">CC-456-DD</div>
                                                <div class="text-sm text-gray-500">Renault Master</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        120,567 km
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex flex-col">
                                            <span class="font-medium">17/10/2025</span>
                                            <span class="text-gray-500">09:15</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            En maintenance
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button class="text-blue-600 hover:text-blue-900 font-medium">Voir</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">EE-789-FF</div>
                                                <div class="text-sm text-gray-500">Peugeot Partner</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        95,280 km
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex flex-col">
                                            <span class="font-medium">16/10/2025</span>
                                            <span class="text-gray-500">16:45</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Hors service
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button class="text-blue-600 hover:text-blue-900 font-medium">Voir</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tables --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Tableaux (Style Kilométrage)</h2>

            <div class="space-y-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Tableau avec icônes et badges</h3>
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                            <div class="flex items-center space-x-1">
                                                <span>Véhicule</span>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kilométrage
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date & Heure
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Auteur
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Méthode
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">AA-123-BB</div>
                                                    <div class="text-sm text-gray-500">Toyota Corolla</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            85,432 km
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex flex-col">
                                                <span class="font-medium">18/10/2025</span>
                                                <span class="text-gray-500">14:30</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                    <span class="text-blue-600 font-semibold text-xs">JD</span>
                                                </div>
                                                <span class="font-medium">Jean Dupont</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Manuel
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <button class="text-blue-600 hover:text-blue-900 font-medium">Détails</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">CC-456-DD</div>
                                                    <div class="text-sm text-gray-500">Renault Master</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            120,567 km
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex flex-col">
                                                <span class="font-medium">17/10/2025</span>
                                                <span class="text-gray-500">09:15</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="text-gray-400 italic">Système</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Automatique
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <button class="text-blue-600 hover:text-blue-900 font-medium">Détails</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Demo --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Modal</h2>
            
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Exemples de Code</h2>

            <div class="space-y-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Button</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-button variant="primary" icon="plus"&gt;
    Nouveau véhicule
&lt;/x-button&gt;</code></pre>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Input</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-input
    name="plate"
    label="Immatriculation"
    icon="truck"
    required
/&gt;</code></pre>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Select</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-select
    name="type"
    label="Type de véhicule"
    :options="['sedan' => 'Berline', 'suv' => 'SUV']"
    required
/&gt;</code></pre>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Textarea</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-textarea
    name="description"
    label="Description"
    rows="4"
/&gt;</code></pre>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">TomSelect (Recherche avancée)</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-tom-select
    name="vehicle"
    label="Véhicule"
    :options="['1' => 'AA-123-BB', '2' => 'CC-456-DD']"
    placeholder="Rechercher..."
    required
/&gt;</code></pre>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Datepicker</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-datepicker
    name="date"
    label="Date de maintenance"
    placeholder="JJ/MM/AAAA"
    required
/&gt;</code></pre>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">TimePicker</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-time-picker
    name="time"
    label="Heure"
    placeholder="HH:MM"
    required
/&gt;</code></pre>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Alert</h3>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;x-alert type="success" title="Succès" dismissible&gt;
    Opération réussie
&lt;/x-alert&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>
</section>

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
