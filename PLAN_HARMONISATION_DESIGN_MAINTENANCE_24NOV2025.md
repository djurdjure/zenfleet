# 🎨 PLAN D'HARMONISATION DESIGN MAINTENANCE → AFFECTATION
## Architecture Enterprise-Grade - 24 Novembre 2025

---

## 🎯 OBJECTIF

Harmoniser **complètement** le design de la page "Nouvelle Opération de Maintenance" avec la page "Nouvelle Affectation" en copiant exactement la structure HTML et les classes Tailwind natives.

---

## 🔍 DIAGNOSTIC DÉTAILLÉ

### Problème Identifié

**Page Affectation (référence)** ✅
```html
<!-- Label avec icône -->
<label class="block text-sm font-medium text-gray-700 mb-2">
    <div class="flex items-center gap-2">
        <x-iconify icon="heroicons:truck" class="w-4 h-4 text-gray-500" />
        Véhicule
        <span class="text-red-500">*</span>
    </div>
</label>

<!-- SlimSelect -->
<select class="slimselect-vehicle w-full" required>

<!-- Input avec icône intégrée -->
<div class="relative">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="w-5 h-5 text-gray-400">...</svg>
    </div>
    <input class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 
                  transition-colors duration-200 border-gray-300 focus:ring-2 
                  focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10">
</div>
```

**Page Maintenance (actuelle)** ❌
```html
<!-- Label abstrait -->
<label class="form-label required">
    Véhicule
</label>

<!-- SlimSelect avec classe abstraite -->
<select class="form-select slimselect-vehicle" required>

<!-- Input abstrait sans icône -->
<input class="form-input">
```

### Classes CSS Abstraites à Supprimer

| Classe Abstraite | Remplacement Tailwind Natif |
|------------------|----------------------------|
| `form-section-primary` | `x-card` component avec `bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-200` |
| `form-section` | Structure card blanche standard |
| `form-label` | `block text-sm font-medium text-gray-700 mb-2` |
| `form-input` | `bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400` |
| `form-select` | Supprimer (utiliser seulement `slimselect-* w-full`) |
| `form-textarea` | `bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5` |
| `form-group` | `div` simple (pas de classe) |
| `btn-primary` | `inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm` |
| `btn-secondary` | `inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm` |

---

## 📋 PLAN D'ACTION DÉTAILLÉ

### Phase 1 : Section "Informations Principales" ✅ EN COURS

**Structure cible :**
```blade
<x-card class="bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-200">
    <div class="space-y-6">
        <div class="pb-4 border-b border-blue-200">
            <h2 class="text-lg font-semibold text-blue-900 mb-1 flex items-center gap-2">
                <x-iconify icon="heroicons:wrench" class="w-5 h-5 text-blue-600" />
                Informations Principales
            </h2>
            <p class="text-sm text-blue-700">Description...</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Champs ici -->
        </div>
    </div>
</x-card>
```

**Actions :**
- ✅ Remplacer `form-section-primary` par `x-card` avec classes natives
- ✅ Ajouter icône `heroicons:truck` dans label Véhicule
- ✅ Ajouter icône `heroicons:cog` dans label Type de Maintenance
- ⏳ Ajouter icône `heroicons:building-storefront` dans label Fournisseur
- ⏳ Supprimer toutes les classes `form-label`, `form-select`, `form-group`
- ⏳ Ajouter messages d'erreur avec icône `heroicons:exclamation-circle`

---

### Phase 2 : Section "Dates et Planification"

**Structure cible :**
```blade
<x-card>
    <div class="space-y-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-1 flex items-center gap-2">
                <x-iconify icon="heroicons:calendar-days" class="w-5 h-5 text-blue-600" />
                Dates et Planification
            </h2>
            <p class="text-sm text-gray-600">Description...</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Champs avec icônes -->
        </div>
    </div>
</x-card>
```

**Actions :**
- Remplacer `form-section` par `x-card` standard
- Ajouter icônes dans les labels des dates
- Uniformiser les messages d'aide

---

### Phase 3 : Section "Détails Opérationnels"

**Actions :**
- Même structure que Phase 2
- Icônes : `heroicons:clock`, `heroicons:currency-dollar`

---

### Phase 4 : Section "Description et Notes"

**Actions :**
- Textarea avec classes Tailwind natives
- Compteur de caractères en bas à droite
- Messages d'aide uniformisés

---

### Phase 5 : Footer avec Boutons

**Structure cible :**
```blade
<div class="flex items-center justify-end gap-3 pt-4">
    <a href="..."
       class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 rounded-lg 
              text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
        <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
        <span>Annuler</span>
    </a>

    <button type="submit"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-medium 
                   text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-sm">
        <x-iconify icon="heroicons:check-circle" class="w-5 h-5" />
        <span>Créer l'opération</span>
    </button>
</div>
```

---

## 🛠️ MODIFICATIONS TECHNIQUES

### Fichiers à Modifier

1. ✅ `resources/views/livewire/maintenance/maintenance-operation-create.blade.php` (777 lignes)
   - Refactorisation complète requise
   - Remplacement de toutes les classes abstraites

2. ⚠️ `resources/css/components/form-components.css`
   - **OPTION A** : Supprimer complètement (recommandé)
   - **OPTION B** : Garder pour d'autres pages (si utilisé ailleurs)

3. ✅ `resources/css/app.css`
   - Supprimer l'import de `form-components.css` si Option A

---

## 📊 ESTIMATION DE TRAVAIL

| Phase | Lignes à Modifier | Complexité | Temps Estimé |
|-------|------------------|------------|--------------|
| Phase 1 : Section 1 | ~150 lignes | Moyenne | 15 min |
| Phase 2 : Section 2 | ~100 lignes | Faible | 10 min |
| Phase 3 : Section 3 | ~80 lignes | Faible | 10 min |
| Phase 4 : Section 4 | ~60 lignes | Faible | 5 min |
| Phase 5 : Footer | ~40 lignes | Faible | 5 min |
| **TOTAL** | **~430 lignes** | **Moyenne** | **45 min** |

---

## ✅ CHECKLIST DE VALIDATION

### Design Visuel
- [ ] Fond bleu clair sur Section "Informations Principales"
- [ ] Icônes présentes dans tous les labels
- [ ] SlimSelect avec hauteur 42px (identique à Affectation)
- [ ] Messages d'aide en `text-xs text-gray-500`
- [ ] Messages d'erreur en rouge avec icône
- [ ] Boutons avec design identique (couleurs, ombres, transitions)

### Structure HTML
- [ ] Aucune classe `form-*` abstraite restante
- [ ] Toutes les classes Tailwind natives
- [ ] Structure `<x-card>` pour les sections
- [ ] Grid `md:grid-cols-2` pour les champs en colonnes

### Fonctionnalités
- [ ] SlimSelect fonctionne correctement
- [ ] Flatpickr fonctionne correctement
- [ ] Validation temps réel Livewire
- [ ] Auto-complétion kilométrage
- [ ] Soumission formulaire

---

## 🚀 COMMANDE DE TEST FINALE

```bash
# Vider tous les caches
docker-compose exec php php artisan view:clear
docker-compose exec php php artisan cache:clear

# Recompiler les assets
npm run build

# Hard refresh navigateur
# Windows/Linux : Ctrl + Shift + R
# macOS : Cmd + Shift + R
```

---

## 📝 NOTES IMPORTANTES

1. **NE PAS utiliser de classes abstraites** - Toutes les classes doivent être Tailwind natives
2. **Copier exactement** la structure de assignment-form.blade.php
3. **Ajouter des icônes** dans tous les labels (comme Affectation)
4. **Garder la logique Livewire** intacte (wire:model, wire:ignore, etc.)
5. **Tester après chaque phase** pour éviter les régressions

---

**Status** : 🔄 En cours - Phase 1 démarrée  
**Prochaine étape** : Compléter Phase 1 (Section "Informations Principales")  
**Objectif final** : Design 100% identique à la page Affectation
