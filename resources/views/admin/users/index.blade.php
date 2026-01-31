@extends('layouts.admin.catalyst')
@section('title', 'Gestion des Utilisateurs - ZenFleet')

@section('content')
<div class="py-4 px-4 mx-auto max-w-7xl lg:py-6" x-data="{
    showConfirmModal: false,
    userToDelete: {},
    deleteFormUrl: '',
    searchQuery: '',
    selectedRole: ''
}">
    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
            <x-iconify icon="lucide:users" class="w-6 h-6 text-blue-600" />
            Gestion des Utilisateurs
            <span class="ml-2 text-sm font-normal text-gray-500">
                ({{ $users->total() }})
            </span>
        </h1>

        <div class="flex items-center gap-2">
            @can('create users')
            <a href="{{ route('admin.users.create') }}"
                title="Nouvel Utilisateur"
                class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <x-iconify icon="lucide:user-plus" class="w-5 h-5" />
            </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm" role="alert">
        <div class="flex items-center">
            <x-iconify icon="lucide:check-circle" class="w-5 h-5 text-green-500 mr-3" />
            <p class="font-semibold text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if (session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm" role="alert">
        <div class="flex items-center">
            <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-500 mr-3" />
            <p class="font-semibold text-red-800">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <x-page-analytics-grid columns="4">
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total utilisateurs</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $users->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:users" class="w-6 h-6 text-blue-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Administrateurs</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $users->filter(fn($u) => $u->hasRole('Admin') || $u->hasRole('Super Admin'))->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:shield" class="w-6 h-6 text-purple-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Superviseurs</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $users->filter(fn($u) => $u->hasRole('Superviseur'))->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:user-cog" class="w-6 h-6 text-orange-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Chauffeurs</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $users->filter(fn($u) => $u->hasRole('Chauffeur'))->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:id-card" class="w-6 h-6 text-green-600" />
                </div>
            </div>
        </div>
    </x-page-analytics-grid>

    <x-page-search-bar x-data="{ showFilters: false }">
        <x-slot:search>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                </div>
                <input
                    x-model.debounce.500ms="searchQuery"
                    type="text"
                    placeholder="Nom, email..."
                    class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
        </x-slot:search>

        <x-slot:filters>
            <button
                @click="showFilters = !showFilters"
                type="button"
                title="Filtres"
                class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="showFilters ? 'rotate-180' : ''" />
                <span x-show="searchQuery || selectedRole" x-cloak class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800" x-text="(searchQuery ? 1 : 0) + (selectedRole ? 1 : 0)"></span>
            </button>
        </x-slot:filters>

        <x-slot:actions>
            <a href="{{ route('admin.users.export') }}"
                title="Exporter"
                class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-500" />
            </a>
            @can('create users')
            <a href="{{ route('admin.users.create') }}"
                title="Nouvel Utilisateur"
                class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <x-iconify icon="lucide:plus" class="w-5 h-5" />
            </a>
            @endcan
        </x-slot:actions>

        <x-slot:filtersPanel>
            <x-page-filters-panel columns="2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Rôle</label>
                    <select x-model="selectedRole" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Tous les rôles</option>
                        <option value="Super Admin">Super Admin</option>
                        <option value="Admin">Admin</option>
                        <option value="Superviseur">Superviseur</option>
                        <option value="Gestionnaire Flotte">Gestionnaire Flotte</option>
                        <option value="Chauffeur">Chauffeur</option>
                    </select>
                </div>

                <x-slot:reset>
                    <button x-show="searchQuery || selectedRole" x-cloak @click="searchQuery = ''; selectedRole = ''"
                        class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                        <x-iconify icon="lucide:x" class="w-4 h-4" />
                        Réinitialiser
                    </button>
                </x-slot:reset>
            </x-page-filters-panel>
        </x-slot:filtersPanel>
    </x-page-search-bar>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organisation</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôles</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscrit le</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors"
                        x-show="(searchQuery === '' || '{{ strtolower($user->name . ' ' . $user->email) }}'.includes(searchQuery.toLowerCase())) &&
 (selectedRole === '' || '{{ $user->roles->pluck('name')->implode(',') }}'.includes(selectedRole))">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm mr-3">
                                    {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-900">
                                <x-iconify icon="lucide:mail" class="w-4 h-4 text-gray-400 mr-2 inline" />
                                {{ $user->email }}
                            </p>
                            @if($user->phone)
                            <p class="text-xs text-gray-500 mt-1">
                                <x-iconify icon="lucide:phone" class="w-4 h-4 text-gray-400 mr-2 inline" />
                                {{ $user->phone }}
                            </p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <x-iconify icon="lucide:building" class="w-4 h-4 mr-2 text-gray-500" />
                                {{ $user->organization->name ?? 'N/A' }}
                            </span>
                            @if($user->vehicles_count > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <x-iconify icon="lucide:car" class="w-3 h-3 mr-1" /> {{ $user->vehicles_count }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                @php
                                $roleColors = [
                                    'Super Admin' => 'bg-red-100 text-red-800 border-red-200',
                                    'Admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'Superviseur' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'Gestionnaire Flotte' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'Chauffeur' => 'bg-green-100 text-green-800 border-green-200',
                                ];
                                $colorClass = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $colorClass }}">
                                    {{ $role->name }}
                                </span>
                                @empty
                                <span class="text-xs italic text-gray-400">Aucun rôle</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4 text-gray-400 mr-2 inline" />
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('edit users')
                                    <a href="{{ route('admin.users.permissions', $user) }}"
                                        title="Gérer les Permissions"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-purple-600 hover:bg-purple-50 transition-all duration-200 group">
                                        <x-iconify icon="lucide:lock" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </a>
                                    @endcan

                                    @can('edit users')
                                    <a href="{{ route('admin.users.vehicles.manage', $user) }}"
                                        title="Gérer l'accès aux véhicules"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-green-600 hover:bg-green-50 transition-all duration-200 group">
                                        <x-iconify icon="lucide:car" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </a>
                                    @endcan

                                    @can('edit users')
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        title="Modifier"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group">
                                        <x-iconify icon="lucide:edit-3" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </a>
                                    @endcan

                                    @can('delete users')
                                    @if(auth()->id() !== $user->id)
                                    <button type="button"
                                        @click="showConfirmModal = true; userToDelete = {{ json_encode($user->only(['id', 'name', 'email'])) }}; deleteFormUrl = '{{ route('admin.users.destroy', $user->id) }}'"
                                        title="Supprimer"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200 group">
                                        <x-iconify icon="lucide:trash-2" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </button>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <x-iconify icon="lucide:users" class="w-12 h-12 text-gray-300 mb-4 mx-auto" />
                            <p class="text-gray-500 font-medium">Aucun utilisateur trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <x-pagination-standard :paginator="$users" :records-per-page="request('per_page', 15)" />
    </div>

    <div x-show="showConfirmModal"
        x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" @click="showConfirmModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="relative z-50 inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                @click.away="showConfirmModal = false">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="lucide:trash-2" class="w-6 h-6 text-red-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Suppression définitive</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer définitivement l'utilisateur
                                <span class="font-bold text-gray-900" x-text="userToDelete.name || userToDelete.email"></span>
                                (<span class="font-medium" x-text="userToDelete.email"></span>) ?
                            </p>
                            <div class="mt-3 bg-red-50 border border-red-200 rounded-md p-3">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <x-iconify icon="lucide:alert-triangle" class="h-5 w-5 text-red-400" />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Attention : Action irréversible</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul role="list" class="list-disc pl-5 space-y-1">
                                                <li>Le compte sera supprimé définitivement.</li>
                                                <li>Les accès liés seront révoqués.</li>
                                                <li>Cette action ne peut pas être annulée.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form :action="deleteFormUrl" method="POST" class="sm:ml-3 sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:text-sm">
                            Supprimer
                        </button>
                    </form>
                    <button type="button"
                        @click="showConfirmModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
