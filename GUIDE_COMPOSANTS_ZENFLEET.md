# 📘 Guide des Composants ZenFleet Design System

**Version:** 1.0
**Date:** 18 Octobre 2025
**Page de référence:** [http://localhost/admin/components-demo](http://localhost/admin/components-demo)

---

## 🎯 Règle d'Or

**TOUJOURS utiliser les composants Blade `<x-*>` au lieu du HTML natif.**

❌ **INTERDIT:** `<input>`, `<select>`, `<textarea>`, `<button>` HTML natifs
✅ **OBLIGATOIRE:** `<x-input>`, `<x-tom-select>`, `<x-textarea>`, `<x-button>`

---

## 📦 Composants Disponibles

### 1. Input (Champs de saisie)

```blade
<x-input
    name="registration_plate"
    label="Immatriculation"
    icon="identification"
    placeholder="Ex: 16-12345-23"
    :value="old('registration_plate')"
    required
    :error="$errors->first('registration_plate')"
    helpText="Numéro d'immatriculation officiel"
    type="text"
    min="0"
    max="100"
/>
```

**Props disponibles:**
- `name` (required) - Nom du champ
- `label` - Label affiché au-dessus
- `icon` - Icône Heroicons (sans préfixe heroicons:)
- `placeholder` - Texte indicatif
- `value` - Valeur par défaut
- `type` - text, email, number, password, etc.
- `required` - Champ obligatoire (astérisque rouge)
- `disabled` - Champ désactivé
- `error` - Message d'erreur (avec icône rouge)
- `helpText` - Texte d'aide en gris
- `min`, `max`, `step` - Pour type="number"

**Icônes contextuelles courantes:**
- Véhicules: `identification`, `truck`, `finger-print`
- Personnes: `user`, `phone`, `envelope`
- Dates: `calendar`, `clock`
- Argent: `currency-dollar`, `banknotes`
- Technique: `wrench-screwdriver`, `bolt`, `cog-6-tooth`

---

### 2. Select (Liste déroulante simple)

```blade
<x-select
    name="status"
    label="Statut"
    :options="[
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'maintenance' => 'En maintenance'
    ]"
    :selected="old('status')"
    required
    :error="$errors->first('status')"
    helpText="Sélectionnez le statut du véhicule"
/>
```

**Props disponibles:**
- `name` (required)
- `label`
- `options` (array) - Format `['value' => 'Label']`
- `selected` - Valeur sélectionnée par défaut
- `required`
- `disabled`
- `error`
- `helpText`

**⚠️ Quand utiliser:**
- Listes courtes (<10 options)
- Pas de recherche nécessaire
- Options fixes et simples

---

### 3. TomSelect (Liste déroulante avec recherche) ⭐

```blade
{{-- TomSelect simple --}}
<x-tom-select
    name="vehicle_type_id"
    label="Type de Véhicule"
    :options="$vehicleTypes->pluck('name', 'id')->toArray()"
    :selected="old('vehicle_type_id')"
    placeholder="Sélectionnez un type..."
    required
    :error="$errors->first('vehicle_type_id')"
    helpText="Type de véhicule (léger, utilitaire, etc.)"
/>

{{-- TomSelect multiple (tags) --}}
<x-tom-select
    name="users"
    label="Utilisateurs Autorisés"
    :options="$users->mapWithKeys(fn($user) => [$user->id => $user->name . ' (' . $user->email . ')'])->toArray()"
    :selected="old('users', [])"
    placeholder="Rechercher des utilisateurs..."
    :multiple="true"
    :clearable="true"
    :error="$errors->first('users')"
    helpText="Sélectionnez plusieurs utilisateurs"
/>
```

**Props disponibles:**
- `name` (required) - Ajoutera automatiquement `[]` si multiple
- `label`
- `options` (array) - Format `['value' => 'Label']`
- `selected` - Valeur ou array pour multiple
- `placeholder`
- `multiple` (boolean) - Active la multi-sélection
- `clearable` (boolean) - Bouton pour effacer (default: true)
- `required`
- `disabled`
- `error`
- `helpText`

**✅ Quand utiliser:**
- Listes longues (>10 options)
- Recherche nécessaire
- Multi-sélection (tags)
- Relations Eloquent (pluck)

**Fonctionnalités automatiques:**
- Recherche fuzzy en temps réel
- Clear button (X pour effacer)
- Remove button sur tags
- Dark mode complet
- "Aucun résultat trouvé" en français
- Tri alphabétique automatique
- maxOptions: 100

**❌ NE JAMAIS faire:**
```blade
<!-- INTERDIT -->
<select x-ref="vehicle_type" class="tomselect">...</select>
<script>
    new TomSelect(this.$refs.vehicle_type, {...}); // ❌
</script>

<!-- CORRECT -->
<x-tom-select name="vehicle_type" :options="..." /> <!-- ✅ -->
```

---

### 4. Textarea (Zone de texte multi-lignes)

```blade
<x-textarea
    name="notes"
    label="Notes"
    rows="4"
    placeholder="Informations complémentaires..."
    :value="old('notes')"
    required
    :error="$errors->first('notes')"
    helpText="Maximum 500 caractères"
/>
```

**Props disponibles:**
- `name` (required)
- `label`
- `rows` - Nombre de lignes (default: 3)
- `placeholder`
- `value`
- `required`
- `disabled`
- `error`
- `helpText`

---

### 5. Datepicker (Sélecteur de date) 📅

```blade
<x-datepicker
    name="acquisition_date"
    label="Date d'acquisition"
    :value="old('acquisition_date')"
    format="d/m/Y"
    :minDate="date('Y-m-d')"
    :maxDate="date('Y-m-d', strtotime('+1 year'))"
    placeholder="Choisir une date"
    required
    :error="$errors->first('acquisition_date')"
    helpText="Date d'achat du véhicule"
/>
```

**Props disponibles:**
- `name` (required)
- `label`
- `value` - Format YYYY-MM-DD
- `format` - Format d'affichage (default: d/m/Y)
- `minDate` - Date minimum
- `maxDate` - Date maximum
- `placeholder`
- `required`
- `disabled`
- `error`
- `helpText`

**Formats courants:**
- `d/m/Y` - 18/10/2025 (recommandé pour l'Algérie)
- `Y-m-d` - 2025-10-18 (ISO 8601)
- `d-m-Y` - 18-10-2025

**Fonctionnalités automatiques:**
- Flatpickr initialisé
- Calendrier popup
- Navigation mois/année
- Support clavier
- Dark mode
- Locale française

---

### 6. TimePicker (Sélecteur d'heure) 🕐

```blade
<x-time-picker
    name="departure_time"
    label="Heure de départ"
    :value="old('departure_time')"
    placeholder="HH:MM"
    format="H:i"
    required
    :error="$errors->first('departure_time')"
    helpText="Format 24 heures"
/>
```

**Props disponibles:**
- `name` (required)
- `label`
- `value` - Format HH:MM
- `format` - Format d'affichage (default: H:i)
- `placeholder`
- `required`
- `disabled`
- `error`
- `helpText`

**Formats:**
- `H:i` - 14:30 (24 heures - recommandé)
- `h:i K` - 02:30 PM (12 heures)

---

### 7. Button (Boutons)

```blade
{{-- Bouton principal --}}
<x-button
    type="submit"
    variant="primary"
    icon="check-circle"
    size="md"
>
    Enregistrer
</x-button>

{{-- Bouton secondaire --}}
<x-button
    type="button"
    variant="secondary"
    icon="arrow-left"
    @click="currentStep--"
>
    Précédent
</x-button>

{{-- Lien stylé comme bouton --}}
<x-button
    href="{{ route('admin.vehicles.index') }}"
    variant="ghost"
>
    Annuler
</x-button>

{{-- Bouton désactivé --}}
<x-button
    variant="primary"
    disabled
>
    Chargement...
</x-button>
```

**Props disponibles:**
- `type` - submit, button, reset (default: button)
- `variant` - primary, secondary, danger, success, ghost
- `icon` - Icône Heroicons (sans préfixe)
- `iconPosition` - left, right (default: left)
- `size` - sm, md, lg (default: md)
- `href` - Rend un lien `<a>` au lieu de `<button>`
- `disabled`

**Variantes:**
- `primary` - Bleu, actions principales (Enregistrer, Créer)
- `secondary` - Gris, actions secondaires (Annuler, Retour)
- `danger` - Rouge, actions destructives (Supprimer)
- `success` - Vert, confirmations (Valider, Approuver)
- `ghost` - Transparent, liens discrets

**Icônes courantes:**
- Actions: `check-circle`, `plus`, `pencil`, `trash`, `x-mark`
- Navigation: `arrow-left`, `arrow-right`, `arrow-up-tray`
- Opérations: `document-text`, `eye`, `cog-6-tooth`

---

### 8. Alert (Messages d'alerte)

```blade
<x-alert
    type="success"
    title="Succès"
    dismissible
>
    Le véhicule a été enregistré avec succès!
</x-alert>

<x-alert
    type="error"
    title="Erreur"
>
    Une erreur est survenue lors de l'enregistrement.
</x-alert>

<x-alert
    type="warning"
    title="Attention"
>
    Ce véhicule est en maintenance depuis 30 jours.
</x-alert>

<x-alert
    type="info"
    title="Information"
>
    N'oubliez pas de mettre à jour le kilométrage.
</x-alert>
```

**Props disponibles:**
- `type` (required) - success, error, warning, info
- `title` - Titre en gras
- `dismissible` (boolean) - Bouton de fermeture X

---

### 9. Badge (Étiquettes de statut)

```blade
<x-badge type="success">Actif</x-badge>
<x-badge type="danger">Inactif</x-badge>
<x-badge type="warning">En attente</x-badge>
<x-badge type="gray">Brouillon</x-badge>
```

**Types:**
- `success` - Vert (Actif, Validé, Approuvé)
- `danger` - Rouge (Inactif, Rejeté, Supprimé)
- `warning` - Orange (En attente, Attention)
- `info` - Bleu (Information)
- `gray` - Gris (Neutre, Brouillon)

---

### 10. Modal (Fenêtres modales)

```blade
<x-modal
    name="confirm-delete"
    title="Confirmer la suppression"
    size="md"
>
    <p>Êtes-vous sûr de vouloir supprimer ce véhicule?</p>
    <p class="text-sm text-gray-600">Cette action est irréversible.</p>

    <x-slot name="footer">
        <x-button variant="secondary" @click="show = false">
            Annuler
        </x-button>
        <x-button variant="danger" type="submit">
            Supprimer
        </x-button>
    </x-slot>
</x-modal>
```

**Props disponibles:**
- `name` (required) - Identifiant unique
- `title` - Titre de la modale
- `size` - sm, md, lg, xl (default: md)

---

## 🎨 Structure de Page Standard

```blade
@extends('layouts.admin.catalyst')

@section('title', 'Titre de la Page')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header avec icône --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <x-iconify icon="heroicons:truck" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Titre Principal</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Description contextuelle</p>
            </div>
        </div>
    </div>

    {{-- Contenu principal --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
            <x-iconify icon="heroicons:document-text" class="w-6 h-6 inline-block mr-2" />
            Sous-titre
        </h2>

        <form method="POST" action="{{ route('...') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Vos composants ici --}}
            </div>

            {{-- Actions Footer --}}
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                <a href="{{ route('...') }}"
                   class="text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    Annuler
                </a>
                <x-button variant="primary" type="submit" icon="check">
                    Enregistrer
                </x-button>
            </div>
        </form>
    </div>
</div>
@endsection
```

---

## 🎯 Grid Responsive

```blade
{{-- 1 colonne mobile, 2 colonnes desktop --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-input ... />
    <x-input ... />
</div>

{{-- 1 colonne mobile, 2 colonnes tablet, 3 colonnes desktop --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <x-input ... />
    <x-input ... />
    <x-input ... />
</div>

{{-- Champ qui prend toute la largeur --}}
<div class="md:col-span-2">
    <x-textarea ... />
</div>
```

---

## 🌙 Dark Mode

**TOUT est géré automatiquement!**

Les composants incluent déjà toutes les classes `dark:`:
- `dark:bg-gray-800` - Fonds
- `dark:border-gray-700` - Bordures
- `dark:text-white` - Textes
- `dark:placeholder-gray-400` - Placeholders

❌ **Ne pas ajouter manuellement** de classes dark: sur les composants.

---

## 🔥 Exemples Complets

### Formulaire Simple

```blade
<form method="POST" action="{{ route('admin.drivers.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-input
            name="first_name"
            label="Prénom"
            icon="user"
            placeholder="Jean"
            :value="old('first_name')"
            required
            :error="$errors->first('first_name')"
        />

        <x-input
            name="last_name"
            label="Nom"
            icon="user"
            placeholder="Dupont"
            :value="old('last_name')"
            required
            :error="$errors->first('last_name')"
        />

        <x-input
            type="email"
            name="email"
            label="Email"
            icon="envelope"
            placeholder="jean.dupont@exemple.com"
            :value="old('email')"
            required
            :error="$errors->first('email')"
        />

        <x-input
            type="tel"
            name="phone"
            label="Téléphone"
            icon="phone"
            placeholder="+213 XX XX XX XX"
            :value="old('phone')"
            required
            :error="$errors->first('phone')"
        />

        <x-datepicker
            name="hire_date"
            label="Date d'embauche"
            :value="old('hire_date')"
            format="d/m/Y"
            required
            :error="$errors->first('hire_date')"
        />

        <x-tom-select
            name="license_type"
            label="Type de permis"
            :options="['B' => 'Permis B', 'C' => 'Permis C', 'D' => 'Permis D']"
            :selected="old('license_type')"
            placeholder="Sélectionnez un permis..."
            required
            :error="$errors->first('license_type')"
        />
    </div>

    <div class="flex justify-end gap-3 pt-6 border-t">
        <x-button href="{{ route('admin.drivers.index') }}" variant="secondary">
            Annuler
        </x-button>
        <x-button type="submit" variant="primary" icon="check">
            Enregistrer
        </x-button>
    </div>
</form>
```

---

## ❌ Erreurs Courantes à Éviter

### 1. Utiliser HTML natif

```blade
<!-- ❌ MAUVAIS -->
<input type="text" name="brand" class="bg-gray-50 border...">
<select name="type">...</select>
<textarea name="notes"></textarea>
<button type="submit">Enregistrer</button>

<!-- ✅ BON -->
<x-input name="brand" label="Marque" />
<x-tom-select name="type" :options="..." />
<x-textarea name="notes" label="Notes" />
<x-button type="submit" variant="primary">Enregistrer</x-button>
```

### 2. Initialiser TomSelect manuellement

```blade
<!-- ❌ MAUVAIS -->
<select x-ref="vehicle_type" class="tomselect">...</select>
<script>
    new TomSelect(this.$refs.vehicle_type, {
        create: false,
        placeholder: '...'
    });
</script>

<!-- ✅ BON -->
<x-tom-select
    name="vehicle_type"
    :options="$types"
    placeholder="..."
/>
```

### 3. Oublier le format de date

```blade
<!-- ❌ MAUVAIS - Format US par défaut -->
<x-datepicker name="date" />

<!-- ✅ BON - Format français -->
<x-datepicker name="date" format="d/m/Y" />
```

### 4. Utiliser :value au lieu de :selected pour select

```blade
<!-- ❌ MAUVAIS -->
<x-tom-select :value="old('type')" />

<!-- ✅ BON -->
<x-tom-select :selected="old('type')" />
```

### 5. Oublier les erreurs de validation

```blade
<!-- ❌ MAUVAIS - Pas de feedback utilisateur -->
<x-input name="brand" />

<!-- ✅ BON -->
<x-input
    name="brand"
    :error="$errors->first('brand')"
/>
```

---

## 🚀 Checklist Migration Formulaire

- [ ] Layout: `@extends('layouts.admin.catalyst')`
- [ ] Header avec icône et description
- [ ] Card avec `bg-white dark:bg-gray-800 rounded-lg shadow-sm border`
- [ ] Tous les `<input>` remplacés par `<x-input>`
- [ ] Tous les `<select>` remplacés par `<x-tom-select>` ou `<x-select>`
- [ ] Tous les `<textarea>` remplacés par `<x-textarea>`
- [ ] Tous les `<button>` remplacés par `<x-button>`
- [ ] Dates avec `<x-datepicker format="d/m/Y">`
- [ ] Heures avec `<x-time-picker format="H:i">`
- [ ] Icônes contextuelles sur tous les inputs
- [ ] Gestion erreurs `:error="$errors->first('...')"`
- [ ] HelpText sur champs complexes
- [ ] Grid responsive `grid-cols-1 md:grid-cols-2`
- [ ] Footer avec bordure-top et actions alignées à droite
- [ ] Pas de JavaScript TomSelect manuel
- [ ] Pas de classes dark: manuelles

---

## 📚 Ressources

- **Page démo:** [http://localhost/admin/components-demo](http://localhost/admin/components-demo)
- **Exemple complet:** `resources/views/admin/vehicles/create.blade.php`
- **Composants:** `resources/views/components/`
- **Iconify:** [https://icon-sets.iconify.design/heroicons/](https://icon-sets.iconify.design/heroicons/)
- **Tailwind:** [https://tailwindcss.com/docs](https://tailwindcss.com/docs)

---

**✅ Ce guide garantit une cohérence visuelle et fonctionnelle 100% sur toute l'application ZenFleet.**
