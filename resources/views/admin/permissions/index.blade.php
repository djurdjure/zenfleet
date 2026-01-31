@extends('layouts.admin')

@section('title', 'Gestion des Permissions')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <svg class="inline-block w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Gestion des Permissions
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Configurez les permissions et rôles de votre organisation avec une granularité entreprise
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.permissions.matrix') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Matrice des Permissions
            </a>
            <button onclick="exportPermissions()" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg shadow-sm text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exporter
            </button>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Rôles</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_roles'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Permissions</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_permissions'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Utilisateurs</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">Modifications Récentes</p>
                    <p class="text-3xl font-bold mt-2">{{ count($stats['recent_changes']) }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Gestion des Rôles --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Rôles et Permissions
            </h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($roles as $role)
                <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-indigo-500 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                {{ $role->name }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $role->users_count }} utilisateur(s)
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editRolePermissions({{ $role->id }}, '{{ $role->name }}')"
                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Permissions par module --}}
                    <div class="space-y-3">
                        @php
                            $moduleColors = [
                                'fleet' => 'blue',
                                'finance' => 'green',
                                'compliance' => 'amber',
                                'reports' => 'purple',
                                'system' => 'red',
                                'general' => 'gray'
                            ];
                        @endphp

                        @foreach($role->permissions_by_module as $module => $permissions)
                        <div>
                            <div class="flex items-center mb-2">
                                <span class="text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    {{ ucfirst($module) }}
                                </span>
                                <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-{{ $moduleColors[$module] ?? 'gray' }}-100 text-{{ $moduleColors[$module] ?? 'gray' }}-800 rounded-full">
                                    {{ count($permissions) }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($permissions->take(5) as $permission)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-md">
                                    {{ str_replace(['_', '.'], ' ', $permission->name) }}
                                </span>
                                @endforeach
                                @if(count($permissions) > 5)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-md">
                                    +{{ count($permissions) - 5 }} autres
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                Total: {{ $role->permissions->count() }} permissions
                            </span>
                            <button onclick="viewRoleDetails({{ $role->id }})"
                                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Voir détails →
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Section Affectations --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Permissions du Module Affectations
            </h2>
        </div>

        <div class="p-6">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Le module d'affectations dispose de permissions granulaires pour contrôler l'accès aux différentes fonctionnalités.
                            Les permissions sont organisées hiérarchiquement pour faciliter la gestion.
                        </p>
                    </div>
                </div>
            </div>

            @php
                $assignmentPermissions = $allPermissions['fleet']['assignments'] ?? [];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($assignmentPermissions as $permission)
                <div class="flex items-start p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">
                            {{ $permission->display_name ?? str_replace(['_', '.'], ' ', $permission->name) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $permission->description ?? 'Permission: ' . $permission->name }}
                        </p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $permission->name }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Modal de modification des permissions --}}
<div id="permissionModal" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">
                    Modifier les permissions
                </h3>
                <button onclick="closePermissionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="modalContent" class="mt-4">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
            
            <div class="flex justify-end mt-6 space-x-3">
                <button onclick="closePermissionModal()" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button onclick="savePermissions()" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentRoleId = null;

function editRolePermissions(roleId, roleName) {
    currentRoleId = roleId;
    document.getElementById('modalTitle').textContent = `Modifier les permissions - ${roleName}`;
    document.getElementById('permissionModal').classList.remove('hidden');
    
    // Charger les permissions du rôle
    fetch(`/admin/permissions/roles/${roleId}/permissions`)
        .then(response => response.json())
        .then(data => {
            renderPermissionCheckboxes(data);
        });
}

function renderPermissionCheckboxes(data) {
    const content = document.getElementById('modalContent');
    let html = '<div class="space-y-6 max-h-96 overflow-y-auto">';
    
    Object.keys(data.all_permissions).forEach(module => {
        html += `
            <div class="border rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3 capitalize">${module}</h4>
                <div class="space-y-2">
        `;
        
        Object.keys(data.all_permissions[module]).forEach(category => {
            html += `<div class="ml-4">
                        <h5 class="text-sm font-medium text-gray-700 mb-2 capitalize">${category}</h5>
                        <div class="grid grid-cols-2 gap-2 ml-4">`;
            
            data.all_permissions[module][category].forEach(permission => {
                const isChecked = data.permissions.includes(permission.name) ? 'checked' : '';
                html += `
                    <label class="flex items-center space-x-2 text-sm">
                        <input type="checkbox" name="permissions[]" value="${permission.name}" ${isChecked}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span>${permission.display_name || permission.name}</span>
                    </label>
                `;
            });
            
            html += '</div></div>';
        });
        
        html += '</div></div>';
    });
    
    html += '</div>';
    content.innerHTML = html;
}

function savePermissions() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]:checked');
    const permissions = Array.from(checkboxes).map(cb => cb.value);
    
    fetch(`/admin/permissions/roles/${currentRoleId}/permissions`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ permissions })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closePermissionModal();
            location.reload();
        } else {
            alert('Erreur lors de la sauvegarde');
        }
    });
}

function closePermissionModal() {
    document.getElementById('permissionModal').classList.add('hidden');
    currentRoleId = null;
}

function viewRoleDetails(roleId) {
    window.location.href = `/admin/permissions/roles/${roleId}`;
}

function exportPermissions() {
    window.location.href = '/admin/permissions/export?format=csv';
}
</script>
@endpush
