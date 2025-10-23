#!/usr/bin/env python3
"""
Alignement du module Véhicules avec le module Chauffeurs
ZenFleet - Enterprise-Grade
"""

import re
import shutil
from pathlib import Path

# Chemins
VEHICLES_INDEX = Path("/home/lynx/projects/zenfleet/resources/views/admin/vehicles/index.blade.php")
BACKUP = VEHICLES_INDEX.with_suffix('.blade.php.before-alignment')

def backup_file():
    """Créer une sauvegarde du fichier original"""
    shutil.copy(VEHICLES_INDEX, BACKUP)
    print(f"✅ Backup créé: {BACKUP}")

def read_file():
    """Lire le contenu du fichier"""
    with open(VEHICLES_INDEX, 'r', encoding='utf-8') as f:
        return f.read()

def write_file(content):
    """Écrire le contenu dans le fichier"""
    with open(VEHICLES_INDEX, 'w', encoding='utf-8') as f:
        f.write(content)

def add_toggle_button(content):
    """Ajouter le bouton toggle Voir Archives / Voir Actifs"""
    
    toggle_code = '''                    {{-- Toggle Voir Archives / Voir Actifs --}}
                    @if(request('archived') === 'true')
                        <a href="{{ route('admin.vehicles.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:list" class="w-5 h-5" />
                            <span class="hidden lg:inline font-medium">Voir Actifs</span>
                        </a>
                    @else
                        <a href="{{ route('admin.vehicles.index', ['archived' => 'true']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:archive" class="w-5 h-5 text-amber-600" />
                            <span class="hidden lg:inline font-medium text-gray-700">Voir Archives</span>
                        </a>
                    @endif

'''
    
    # Trouver la ligne avec "Boutons d'actions" et ajouter le toggle juste après la div flex
    pattern = r"({{-- Boutons d'actions --}}\s*<div class=\"flex items-center gap-2\">\s*)"
    replacement = r"\1" + toggle_code
    
    content = re.sub(pattern, replacement, content, count=1)
    print("✅ Toggle 'Voir Archives' ajouté")
    return content

def fix_csrf_tokens(content):
    """Corriger les tokens CSRF dans les fonctions JavaScript"""
    
    # Fonction confirmArchive
    old_confirm_archive = r'''function confirmArchive\(vehicleId\) \{[^}]+form\.innerHTML = `[^`]+`;\s*document\.body\.appendChild\(form\);[^}]+\}'''
    
    new_confirm_archive = '''function confirmArchive(vehicleId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/vehicles/${vehicleId}/archive`;
    
    // Ajouter le token CSRF correctement
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Ajouter la méthode PUT
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    closeModal();
    setTimeout(() => form.submit(), 200);
}'''
    
    content = re.sub(old_confirm_archive, new_confirm_archive, content, flags=re.DOTALL)
    print("✅ Token CSRF corrigé dans confirmArchive()")
    
    # Fonction confirmRestore
    old_confirm_restore = r'''function confirmRestore\(vehicleId\) \{[^}]+form\.innerHTML = `[^`]+`;\s*document\.body\.appendChild\(form\);[^}]+\}'''
    
    new_confirm_restore = '''function confirmRestore(vehicleId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/vehicles/${vehicleId}/unarchive`;
    
    // Ajouter le token CSRF correctement
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Ajouter la méthode PUT
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    closeModal();
    setTimeout(() => form.submit(), 200);
}'''
    
    content = re.sub(old_confirm_restore, new_confirm_restore, content, flags=re.DOTALL)
    print("✅ Token CSRF corrigé dans confirmRestore()")
    
    return content

def add_permanent_delete_function(content):
    """Ajouter la fonction de suppression définitive"""
    
    permanent_delete = '''

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
'''
    
    # Ajouter avant la balise </script> finale
    content = re.sub(r'</script>\s*@endpush', permanent_delete + '\n</script>\n@endpush', content, count=1)
    print("✅ Fonction permanentDeleteVehicle() ajoutée")
    return content

def fix_restore_modal_buttons(content):
    """Ajouter les boutons Restaurer/Annuler à la modale de restauration"""
    
    # Trouver la modale restoreVehicle et ajouter les boutons avant la fermeture des divs
    pattern = r'(<p class="text-sm text-blue-700">\$\{brand\}</p>\s*</div>\s*</div>\s*</div>\s*</div>\s*</div>\s*</div>\s*</div>)\s*</div>\s*</div>\s*`;\s*document\.body\.appendChild\(modal\);'
    
    buttons = r'''\1
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmRestore(${vehicleId})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 hover:bg-green-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Restaurer
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

    document.body.appendChild(modal);'''
    
    content = re.sub(pattern, buttons, content, flags=re.DOTALL)
    print("✅ Boutons ajoutés à la modale de restauration")
    return content

def main():
    print("🚗 ALIGNEMENT MODULE VÉHICULES - ULTRA PRO")
    print("=" * 50)
    print()
    
    # Backup
    backup_file()
    
    # Lire le fichier
    content = read_file()
    
    # Appliquer les modifications
    print("\n📝 Application des modifications...")
    content = add_toggle_button(content)
    content = fix_csrf_tokens(content)
    content = add_permanent_delete_function(content)
    content = fix_restore_modal_buttons(content)
    
    # Écrire le fichier modifié
    write_file(content)
    
    print("\n🎉 ALIGNEMENT TERMINÉ !")
    print("=" * 50)
    print(f"✅ Fichier modifié: {VEHICLES_INDEX}")
    print(f"✅ Backup créé: {BACKUP}")
    print("\nProchaines étapes:")
    print("1. Modifier manuellement les actions du tableau (ligne ~481-506)")
    print("2. Vérifier le rendu visuel")
    print("3. Tester les modales")
    print("4. Nettoyer le cache: docker-compose exec php php artisan view:clear")

if __name__ == '__main__':
    main()
