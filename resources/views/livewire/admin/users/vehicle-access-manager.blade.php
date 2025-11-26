<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Gestion des accès véhicules
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Utilisateur : <span class="font-medium text-gray-900">{{ $user->name }}</span> 
                    ({{ $user->roles->pluck('name')->implode(', ') }})
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <x-iconify icon="lucide:arrow-left" class="-ml-1 mr-2 h-5 w-5 text-gray-500" />
                    Retour
                </a>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="p-6 border-b border-gray-200 sm:flex sm:items-center sm:justify-between bg-gray-50">
                <div class="flex-1 min-w-0 flex space-x-4">
                    <div class="max-w-lg w-full lg:max-w-xs">
                        <label for="search" class="sr-only">Rechercher</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-iconify icon="lucide:search" class="h-5 w-5 text-gray-400" />
                            </div>
                            <input wire:model.live.debounce.300ms="search" id="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Rechercher un véhicule..." type="search">
                        </div>
                    </div>
                    
                    <select wire:model.live="filter" class="block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="all">Tous les véhicules</option>
                        <option value="assigned">Assignés uniquement</option>
                        <option value="unassigned">Non assignés</option>
                    </select>
                </div>
                
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button wire:click="grantAll" wire:confirm="Êtes-vous sûr de vouloir accorder l'accès à TOUS les véhicules ?" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <x-iconify icon="lucide:check-circle" class="-ml-0.5 mr-2 h-4 w-4" />
                        Tout accorder
                    </button>
                    <button wire:click="revokeAll" wire:confirm="Êtes-vous sûr de vouloir retirer TOUS les accès manuels ?" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <x-iconify icon="lucide:x-circle" class="-ml-0.5 mr-2 h-4 w-4" />
                        Tout révoquer
                    </button>
                </div>
            </div>

            <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 p-6">
                @forelse($vehicles as $vehicle)
                    @php
                        $userVehicle = $vehicle->users->first();
                        $hasAccess = $userVehicle !== null;
                        $isAuto = $hasAccess && $userVehicle->pivot->access_type === 'auto_driver';
                        $isManual = $hasAccess && $userVehicle->pivot->access_type === 'manual';
                    @endphp
                    <li class="col-span-1 bg-white rounded-lg shadow divide-y divide-gray-200 border border-gray-200 hover:shadow-md transition-shadow duration-200">
                        <div class="w-full flex items-center justify-between p-6 space-x-6">
                            <div class="flex-1 truncate">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-gray-900 text-sm font-medium truncate">{{ $vehicle->registration_plate }}</h3>
                                    @if($hasAccess)
                                        @if($isAuto)
                                            <span class="flex-shrink-0 inline-block px-2 py-0.5 text-green-800 text-xs font-medium bg-green-100 rounded-full">
                                                Chauffeur
                                            </span>
                                        @else
                                            <span class="flex-shrink-0 inline-block px-2 py-0.5 text-blue-800 text-xs font-medium bg-blue-100 rounded-full">
                                                Manuel
                                            </span>
                                        @endif
                                    @else
                                        <span class="flex-shrink-0 inline-block px-2 py-0.5 text-gray-800 text-xs font-medium bg-gray-100 rounded-full">
                                            Aucun accès
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-1 text-gray-500 text-sm truncate">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                                <p class="mt-1 text-gray-400 text-xs truncate">{{ $vehicle->vehicle_name }}</p>
                            </div>
                            <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded-full flex items-center justify-center">
                                <x-iconify icon="lucide:car" class="h-6 w-6 text-gray-500" />
                            </div>
                        </div>
                        <div>
                            <div class="-mt-px flex divide-x divide-gray-200">
                                <div class="w-0 flex-1 flex">
                                    @if($isAuto)
                                        <div class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-500 font-medium border-t border-transparent">
                                            <x-iconify icon="lucide:lock" class="w-5 h-5 text-gray-400 mr-2" />
                                            <span class="ml-3">Géré par affectation</span>
                                        </div>
                                    @else
                                        <button wire:click="toggleAccess({{ $vehicle->id }})" 
                                                class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm font-medium border-t border-transparent rounded-bl-lg hover:text-gray-500 {{ $hasAccess ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }}">
                                            @if($hasAccess)
                                                <x-iconify icon="lucide:user-minus" class="w-5 h-5 mr-2" />
                                                <span class="ml-3">Révoquer</span>
                                            @else
                                                <x-iconify icon="lucide:user-plus" class="w-5 h-5 mr-2" />
                                                <span class="ml-3">Accorder</span>
                                            @endif
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="col-span-full text-center py-12">
                        <x-iconify icon="lucide:inbox" class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun véhicule trouvé</h3>
                        <p class="mt-1 text-sm text-gray-500">Essayez de modifier vos filtres ou votre recherche.</p>
                    </li>
                @endforelse
            </ul>
            
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>
</div>
