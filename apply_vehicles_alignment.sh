#!/bin/bash

# Script d'alignement du module véhicules avec le module chauffeurs
# ZenFleet - Enterprise-Grade

cd /home/lynx/projects/zenfleet

echo "🚗 Alignement Module Véhicules - ULTRA PRO"
echo "=========================================="

# Backup du fichier original
echo "📁 Création backup..."
cp resources/views/admin/vehicles/index.blade.php resources/views/admin/vehicles/index.blade.php.before-alignment

# ===== ÉTAPE 1 : Ajouter le toggle Voir Archives / Voir Actifs après la ligne 219 =====
echo "✅ Ajout toggle Voir Archives..."

sed -i '219 a\                    {{-- Toggle Voir Archives / Voir Actifs --}}\
                    @if(request('\''archived'\'') === '\''true'\'')\
                        <a href="{{ route('\''admin.vehicles.index'\'') }}"\
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">\
                            <x-iconify icon="lucide:list" class="w-5 h-5" />\
                            <span class="hidden lg:inline font-medium">Voir Actifs</span>\
                        </a>\
                    @else\
                        <a href="{{ route('\''admin.vehicles.index'\'', ['\''archived'\'' => '\''true'\'']) }}"\
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">\
                            <x-iconify icon="lucide:archive" class="w-5 h-5 text-amber-600" />\
                            <span class="hidden lg:inline font-medium text-gray-700">Voir Archives</span>\
                        </a>\
                    @endif\
' resources/views/admin/vehicles/index.blade.php

echo "✅ Toggle ajouté"

# ===== ÉTAPE 2 : Corriger les fonctions JavaScript CSRF =====
echo "✅ Correction tokens CSRF dans JavaScript..."

# Remplacer la fonction confirmArchive
sed -i '/^function confirmArchive/,/^}/c\
function confirmArchive(vehicleId) {\
    const form = document.createElement('\''form'\'');\
    form.method = '\''POST'\'';\
    form.action = `/admin/vehicles/${vehicleId}/archive`;\
    \
    \/\/ Ajouter le token CSRF correctement\
    const csrfInput = document.createElement('\''input'\'');\
    csrfInput.type = '\''hidden'\'';\
    csrfInput.name = '\''_token'\'';\
    csrfInput.value = '\''{{ csrf_token() }}'\'';\
    form.appendChild(csrfInput);\
    \
    \/\/ Ajouter la méthode PUT\
    const methodInput = document.createElement('\''input'\'');\
    methodInput.type = '\''hidden'\'';\
    methodInput.name = '\''_method'\'';\
    methodInput.value = '\''PUT'\'';\
    form.appendChild(methodInput);\
    \
    document.body.appendChild(form);\
    closeModal();\
    setTimeout(() => form.submit(), 200);\
}' resources/views/admin/vehicles/index.blade.php

# Remplacer la fonction confirmRestore
sed -i '/^function confirmRestore/,/^}/c\
function confirmRestore(vehicleId) {\
    const form = document.createElement('\''form'\'');\
    form.method = '\''POST'\'';\
    form.action = `/admin/vehicles/${vehicleId}/unarchive`;\
    \
    \/\/ Ajouter le token CSRF correctement\
    const csrfInput = document.createElement('\''input'\'');\
    csrfInput.type = '\''hidden'\'';\
    csrfInput.name = '\''_token'\'';\
    csrfInput.value = '\''{{ csrf_token() }}'\'';\
    form.appendChild(csrfInput);\
    \
    \/\/ Ajouter la méthode PUT\
    const methodInput = document.createElement('\''input'\'');\
    methodInput.type = '\''hidden'\'';\
    methodInput.name = '\''_method'\'';\
    methodInput.value = '\''PUT'\'';\
    form.appendChild(methodInput);\
    \
    document.body.appendChild(form);\
    closeModal();\
    setTimeout(() => form.submit(), 200);\
}' resources/views/admin/vehicles/index.blade.php

echo "✅ Tokens CSRF corrigés"

# ===== ÉTAPE 3 : Ajouter la fonction permanentDeleteVehicle =====
echo "✅ Ajout fonction Suppression Définitive..."

cat >> resources/views/admin/vehicles/index.blade.php << 'EOL'

// ═══════════════════════════════════════════════════════════════════════════
// SUPPRESSION DÉFINITIVE VÉHICULE - MODAL ULTRA PRO
// ═══════════════════════════════════════════════════════════════════════════

function permanentDeleteVehicle(vehicleId, plate, brand) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.setAttribute('aria-labelledby', 'modal-title');
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');

    modal.innerHTML = `
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Supprimer définitivement le véhicule
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                <strong class="text-red-600">⚠️ ATTENTION : Cette action est IRRÉVERSIBLE !</strong><br>
                                Toutes les données de ce véhicule seront définitivement supprimées de la base de données.
                            </p>
                            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z"/>
                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-red-900">${plate}</p>
                                        <p class="text-sm text-red-700">${brand}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmPermanentDelete(${vehicleId})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 hover:bg-red-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Supprimer définitivement
                    </button>
                    <button
                        type="button"
                        onclick="closeModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
}

function confirmPermanentDelete(vehicleId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/vehicles/${vehicleId}/force-delete`;
    
    // Ajouter le token CSRF correctement
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Ajouter la méthode DELETE
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    closeModal();
    setTimeout(() => form.submit(), 200);
}
EOL

echo "✅ Fonction Suppression ajoutée"

echo ""
echo "🎉 ALIGNEMENT TERMINÉ !"
echo "✅ Toutes les modifications ont été appliquées"
echo "📁 Backup créé: index.blade.php.before-alignment"
echo ""
echo "Prochaines étapes:"
echo "1. Vérifier le fichier: resources/views/admin/vehicles/index.blade.php"
echo "2. Tester affichage et modales"
echo "3. Nettoyer le cache: php artisan view:clear"
