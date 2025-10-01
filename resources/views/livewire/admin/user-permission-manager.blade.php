<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Gestion des Permissions
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Utilisateur: <span class="font-semibold">{{ $user->name }}</span>
                    ({{ $user->email }})
                </p>
            </div>
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
    </div>

    @if($errors->has('general'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 dark:bg-red-900/20">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 dark:text-red-200">{{ $errors->first('general') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <!-- Section Rôle -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Rôle Principal
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Sélectionnez le rôle principal de l'utilisateur
                </p>

                <div class="mt-4">
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rôle
                    </label>
                    <select wire:model.live="selectedRole"
                            id="role"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Sélectionnez un rôle</option>
                        @foreach($availableRoles as $role)
                            <option value="{{ $role->id }}">
                                {{ $role->name }}
                                ({{ $role->permissions->count() }} permissions)
                            </option>
                        @endforeach
                    </select>
                    @error('selectedRole')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section Permissions Personnalisées -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Permissions Personnalisées
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Personnaliser les permissions individuelles pour cet utilisateur
                        </p>
                    </div>
                    <button type="button"
                            wire:click="toggleCustomPermissions"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        @if($useCustomPermissions)
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Désactiver
                        @else
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Activer
                        @endif
                    </button>
                </div>

                @if($useCustomPermissions)
                    <div class="mt-6 space-y-6">
                        @foreach($permissionsByCategory as $category => $permissions)
                            @if($permissions->count() > 0)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $category }}
                                            <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                                ({{ $permissions->count() }} permissions)
                                            </span>
                                        </h4>
                                        <div class="flex space-x-2">
                                            <button type="button"
                                                    wire:click="selectAllInCategory('{{ $category }}')"
                                                    class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300">
                                                Tout sélectionner
                                            </button>
                                            <button type="button"
                                                    wire:click="deselectAllInCategory('{{ $category }}')"
                                                    class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300">
                                                Tout désélectionner
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($permissions as $permission)
                                            <label class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-md cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                <input type="checkbox"
                                                       wire:model="customPermissions"
                                                       value="{{ $permission->id }}"
                                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                                    {{ $permission->name }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    <strong>Total sélectionné:</strong> {{ count($customPermissions) }} permission(s)
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            L'utilisateur utilisera les permissions du rôle sélectionné.
                            Activez les permissions personnalisées pour ajuster individuellement.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="inline-flex items-center">
                        <svg class="w-5 h-5 mr-1.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Les modifications seront appliquées immédiatement
                    </span>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.users.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        Annuler
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
