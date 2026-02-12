# ⚡ ZenFleet - Patterns Alpine.js

**Version:** 1.0.0
**Date:** 18 Octobre 2025
**Framework:** Alpine.js 3.x

---

## 📖 Table des Matières

1. [Introduction](#introduction)
2. [Installation et Configuration](#installation-et-configuration)
3. [Patterns de Base](#patterns-de-base)
4. [Composants Interactifs](#composants-interactifs)
5. [Modals et Overlays](#modals-et-overlays)
6. [Formulaires Dynamiques](#formulaires-dynamiques)
7. [Dropdowns et Menus](#dropdowns-et-menus)
8. [Tabs et Accordions](#tabs-et-accordions)
9. [Notifications et Toasts](#notifications-et-toasts)
10. [Patterns Avancés](#patterns-avancés)
11. [Bonnes Pratiques](#bonnes-pratiques)

---

## Introduction

### Qu'est-ce qu'Alpine.js ?

**Alpine.js** est un framework JavaScript minimal qui apporte de l'interactivité aux interfaces utilisateur. Il est souvent décrit comme "le Tailwind du JavaScript" car il permet d'ajouter du comportement directement dans le HTML.

**Avantages pour ZenFleet:**
- ✅ Léger (~15KB gzipped)
- ✅ Syntaxe déclarative dans HTML
- ✅ Pas de build step nécessaire
- ✅ Compatible avec Livewire
- ✅ Facile à apprendre
- ✅ Parfait pour des interactions simples

**Quand utiliser Alpine.js vs Livewire:**

| Cas d'Usage | Alpine.js | Livewire |
|-------------|-----------|----------|
| Toggle visibility | ✅ | ❌ |
| Modals | ✅ | ❌ |
| Dropdowns | ✅ | ❌ |
| Tabs | ✅ | ❌ |
| Validation formulaire (client) | ✅ | ❌ |
| CRUD opérations | ❌ | ✅ |
| Filtres/Search (serveur) | ❌ | ✅ |
| Tableaux paginés | ❌ | ✅ |
| État partagé complexe | ❌ | ✅ |

---

## Installation et Configuration

### Ajout dans Layout

**Fichier:** `resources/views/layouts/admin/catalyst.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- ... -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    <!-- ... -->

    @stack('scripts')

    <!-- Alpine.js est déjà inclus via Vite -->
</body>
</html>
```

**Fichier:** `resources/js/app.js`

```javascript
import './bootstrap';
import Alpine from 'alpinejs';

// Configuration Alpine
window.Alpine = Alpine;

// Démarrer Alpine
Alpine.start();
```

---

### Vérification Installation

**Console navigateur:**

```javascript
console.log(Alpine); // Should log Alpine object
```

**Test Simple:**

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>
```

---

## Patterns de Base

### x-data - État Local

**Définir l'état d'un composant:**

```blade
<div x-data="{ count: 0 }">
    <button @click="count++">Increment</button>
    <span x-text="count"></span>
</div>
```

**Avec méthodes:**

```blade
<div x-data="{
    count: 0,
    increment() {
        this.count++;
    },
    decrement() {
        this.count--;
    }
}">
    <button @click="decrement()">-</button>
    <span x-text="count"></span>
    <button @click="increment()">+</button>
</div>
```

---

### x-show - Visibilité

**Toggle simple:**

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open" class="mt-4">
        Contenu affiché conditionnellement
    </div>
</div>
```

**Avec transition:**

```blade
<div x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90">
    Contenu animé
</div>
```

---

### @click - Événements

**Click simple:**

```blade
<button @click="alert('Clicked!')">
    Click me
</button>
```

**Avec état:**

```blade
<div x-data="{ message: '' }">
    <button @click="message = 'Hello World'">
        Show Message
    </button>
    <p x-text="message"></p>
</div>
```

**Prevent default:**

```blade
<form @submit.prevent="submitForm()">
    <button type="submit">Submit</button>
</form>
```

---

### x-text et x-html

**x-text (sécurisé):**

```blade
<div x-data="{ name: 'John' }">
    <p>Hello, <span x-text="name"></span>!</p>
</div>
```

**x-html (dangereux - éviter si possible):**

```blade
<div x-data="{ html: '<strong>Bold</strong>' }">
    <div x-html="html"></div>
</div>
```

---

### x-bind - Attributs Dynamiques

**Bind class:**

```blade
<div x-data="{ active: false }">
    <button @click="active = !active"
            :class="active ? 'bg-blue-600' : 'bg-gray-600'">
        Toggle
    </button>
</div>
```

**Bind disabled:**

```blade
<div x-data="{ loading: false }">
    <button @click="loading = true; setTimeout(() => loading = false, 2000)"
            :disabled="loading">
        <span x-text="loading ? 'Loading...' : 'Submit'"></span>
    </button>
</div>
```

---

### x-model - Two-way Binding

**Input text:**

```blade
<div x-data="{ search: '' }">
    <input type="text" x-model="search" placeholder="Search...">
    <p>Searching for: <span x-text="search"></span></p>
</div>
```

**Checkbox:**

```blade
<div x-data="{ agreed: false }">
    <label>
        <input type="checkbox" x-model="agreed">
        I agree to terms
    </label>
    <button :disabled="!agreed">Submit</button>
</div>
```

**Select:**

```blade
<div x-data="{ selected: '' }">
    <select x-model="selected">
        <option value="">Select...</option>
        <option value="option1">Option 1</option>
        <option value="option2">Option 2</option>
    </select>
    <p x-show="selected">You selected: <span x-text="selected"></span></p>
</div>
```

---

## Composants Interactifs

### Toggle Switch

```blade
<div x-data="{ enabled: false }" class="flex items-center space-x-3">
    <button
        @click="enabled = !enabled"
        :class="enabled ? 'bg-blue-600' : 'bg-gray-300'"
        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
    >
        <span
            :class="enabled ? 'translate-x-6' : 'translate-x-1'"
            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
        ></span>
    </button>
    <span class="text-sm text-gray-700 dark:text-gray-300">
        <span x-text="enabled ? 'Enabled' : 'Disabled'"></span>
    </span>
</div>
```

---

### Accordion

```blade
<div x-data="{ openItem: null }">
    {{-- Item 1 --}}
    <div class="border-b border-gray-200 dark:border-gray-700">
        <button
            @click="openItem = openItem === 1 ? null : 1"
            class="w-full flex items-center justify-between p-4 text-left"
        >
            <span class="font-medium text-gray-900 dark:text-white">
                Item 1
            </span>
            <svg
                :class="openItem === 1 ? 'rotate-180' : ''"
                class="w-5 h-5 transition-transform"
                fill="currentColor"
                viewBox="0 0 20 20"
            >
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
        <div x-show="openItem === 1" x-collapse>
            <div class="p-4 bg-gray-50 dark:bg-gray-800">
                Content 1
            </div>
        </div>
    </div>

    {{-- Item 2 --}}
    <div class="border-b border-gray-200 dark:border-gray-700">
        <button
            @click="openItem = openItem === 2 ? null : 2"
            class="w-full flex items-center justify-between p-4 text-left"
        >
            <span class="font-medium text-gray-900 dark:text-white">
                Item 2
            </span>
            <svg
                :class="openItem === 2 ? 'rotate-180' : ''"
                class="w-5 h-5 transition-transform"
                fill="currentColor"
                viewBox="0 0 20 20"
            >
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
        <div x-show="openItem === 2" x-collapse>
            <div class="p-4 bg-gray-50 dark:bg-gray-800">
                Content 2
            </div>
        </div>
    </div>
</div>
```

---

### Tabs

```blade
<div x-data="{ activeTab: 'tab1' }">
    {{-- Tab Buttons --}}
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-4">
            <button
                @click="activeTab = 'tab1'"
                :class="activeTab === 'tab1'
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-2 px-4 border-b-2 font-medium text-sm transition-colors"
            >
                Tab 1
            </button>
            <button
                @click="activeTab = 'tab2'"
                :class="activeTab === 'tab2'
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-2 px-4 border-b-2 font-medium text-sm transition-colors"
            >
                Tab 2
            </button>
            <button
                @click="activeTab = 'tab3'"
                :class="activeTab === 'tab3'
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-2 px-4 border-b-2 font-medium text-sm transition-colors"
            >
                Tab 3
            </button>
        </nav>
    </div>

    {{-- Tab Panels --}}
    <div class="mt-4">
        <div x-show="activeTab === 'tab1'">
            <p>Content for Tab 1</p>
        </div>
        <div x-show="activeTab === 'tab2'">
            <p>Content for Tab 2</p>
        </div>
        <div x-show="activeTab === 'tab3'">
            <p>Content for Tab 3</p>
        </div>
    </div>
</div>
```

---

## Modals et Overlays

### Modal de Base

**Composant Modal ZenFleet:**

```blade
{{-- resources/views/components/modal.blade.php --}}
@props(['name', 'title' => null, 'maxWidth' => 'lg'])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '4xl' => 'max-w-4xl',
    '6xl' => 'max-w-6xl',
    default => 'max-w-lg',
};
@endphp

<div
    x-data="{ show: false }"
    @open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    @close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    @keydown.escape.window="if (show) show = false"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80"
    ></div>

    {{-- Modal Panel --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            @click.away="show = false"
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl {{ $maxWidthClass }} w-full"
        >
            {{-- Header --}}
            @if($title)
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>
                <button
                    @click="show = false"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            @endif

            {{-- Body --}}
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
```

---

### Utilisation Modal

**Ouvrir Modal:**

```blade
<x-button @click="$dispatch('open-modal', 'confirm-delete')">
    Supprimer
</x-button>
```

**Définir Modal:**

```blade
<x-modal name="confirm-delete" title="Confirmer la suppression" maxWidth="sm">
    <p class="text-gray-700 dark:text-gray-300">
        Êtes-vous sûr de vouloir supprimer cet élément ?
    </p>

    <div class="mt-6 flex justify-end gap-3">
        <x-button @click="$dispatch('close-modal', 'confirm-delete')" variant="secondary">
            Annuler
        </x-button>
        <x-button @click="$dispatch('close-modal', 'confirm-delete'); deleteItem()" variant="danger">
            Supprimer
        </x-button>
    </div>
</x-modal>
```

---

### Modal avec Formulaire

```blade
<x-modal name="create-vehicle" title="Ajouter un véhicule" maxWidth="2xl">
    <form @submit.prevent="submitForm" class="space-y-4">
        <x-input name="plate" label="Immatriculation" required />
        <x-input name="brand" label="Marque" required />
        <x-input name="model" label="Modèle" />

        <div class="flex justify-end gap-3 pt-4">
            <x-button @click="$dispatch('close-modal', 'create-vehicle')" type="button" variant="secondary">
                Annuler
            </x-button>
            <x-button type="submit" variant="primary" icon="check">
                Enregistrer
            </x-button>
        </div>
    </form>
</x-modal>
```

---

## Formulaires Dynamiques

### Validation Client-Side

```blade
<div x-data="{
    email: '',
    password: '',
    errors: {},
    validate() {
        this.errors = {};

        if (!this.email) {
            this.errors.email = 'Email requis';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
            this.errors.email = 'Email invalide';
        }

        if (!this.password) {
            this.errors.password = 'Mot de passe requis';
        } else if (this.password.length < 8) {
            this.errors.password = 'Minimum 8 caractères';
        }

        return Object.keys(this.errors).length === 0;
    },
    submit() {
        if (this.validate()) {
            // Submit form
            console.log('Form valid!');
        }
    }
}">
    <form @submit.prevent="submit" class="space-y-4">
        <div>
            <x-input
                type="email"
                name="email"
                label="Email"
                x-model="email"
                @blur="validate()"
            />
            <p x-show="errors.email" x-text="errors.email" class="mt-2 text-sm text-red-600"></p>
        </div>

        <div>
            <x-input
                type="password"
                name="password"
                label="Mot de passe"
                x-model="password"
                @blur="validate()"
            />
            <p x-show="errors.password" x-text="errors.password" class="mt-2 text-sm text-red-600"></p>
        </div>

        <x-button type="submit" variant="primary">
            Se connecter
        </x-button>
    </form>
</div>
```

---

### Champs Dynamiques (Add/Remove)

```blade
<div x-data="{
    fields: [{ id: 1, value: '' }],
    nextId: 2,
    addField() {
        this.fields.push({ id: this.nextId++, value: '' });
    },
    removeField(id) {
        this.fields = this.fields.filter(f => f.id !== id);
    }
}">
    <template x-for="field in fields" :key="field.id">
        <div class="flex items-center gap-3 mb-3">
            <input
                type="text"
                x-model="field.value"
                class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5"
                placeholder="Entrez une valeur"
            />
            <button
                @click="removeField(field.id)"
                type="button"
                :disabled="fields.length === 1"
                class="text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </template>

    <x-button @click="addField()" variant="secondary" icon="plus" type="button">
        Ajouter un champ
    </x-button>
</div>
```

---

## Dropdowns et Menus

### Dropdown Simple

```blade
<div x-data="{ open: false }" @click.away="open = false" class="relative">
    <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
        <span>Options</span>
        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 transform scale-90"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90"
        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10"
        style="display: none;"
    >
        <a href="#" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
            Option 1
        </a>
        <a href="#" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
            Option 2
        </a>
        <a href="#" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
            Option 3
        </a>
    </div>
</div>
```

---

## Notifications et Toasts

### Toast Simple

```blade
<div
    x-data="{
        show: false,
        message: '',
        type: 'success',
        showToast(msg, t = 'success') {
            this.message = msg;
            this.type = t;
            this.show = true;
            setTimeout(() => this.show = false, 3000);
        }
    }"
    @show-toast.window="showToast($event.detail.message, $event.detail.type)"
>
    {{-- Toast Container --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-4 right-4 z-50 max-w-sm"
        style="display: none;"
    >
        <div
            :class="{
                'bg-green-50 border-green-500 text-green-800': type === 'success',
                'bg-red-50 border-red-500 text-red-800': type === 'error',
                'bg-blue-50 border-blue-500 text-blue-800': type === 'info'
            }"
            class="flex items-center gap-3 p-4 border-l-4 rounded-lg shadow-lg"
        >
            <span x-text="message" class="flex-1"></span>
            <button @click="show = false" class="text-current hover:opacity-75">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
```

**Déclencher Toast:**

```blade
<button @click="$dispatch('show-toast', { message: 'Véhicule créé avec succès!', type: 'success' })">
    Créer Véhicule
</button>
```

---

## Patterns Avancés

### Dark Mode Toggle

```blade
<div x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    toggle() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        document.documentElement.classList.toggle('dark', this.darkMode);
    }
}" x-init="document.documentElement.classList.toggle('dark', darkMode)">
    <button @click="toggle()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
        <svg x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
        <svg x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
        </svg>
    </button>
</div>
```

---

### Infinite Scroll

```blade
<div
    x-data="{
        page: 1,
        loading: false,
        items: [],
        hasMore: true,
        async loadMore() {
            if (this.loading || !this.hasMore) return;

            this.loading = true;
            const response = await fetch(`/api/items?page=${this.page}`);
            const data = await response.json();

            this.items = [...this.items, ...data.items];
            this.hasMore = data.hasMore;
            this.page++;
            this.loading = false;
        }
    }"
    x-init="loadMore()"
    @scroll.window="
        if ((window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 500)) {
            loadMore();
        }
    "
>
    <template x-for="item in items" :key="item.id">
        <div class="p-4 border-b">
            <span x-text="item.name"></span>
        </div>
    </template>

    <div x-show="loading" class="p-4 text-center">
        Chargement...
    </div>

    <div x-show="!hasMore && !loading" class="p-4 text-center text-gray-500">
        Fin de la liste
    </div>
</div>
```

---

### Search avec Debounce

```blade
<div x-data="{
    search: '',
    results: [],
    loading: false,
    searchTimeout: null,
    async performSearch() {
        if (!this.search) {
            this.results = [];
            return;
        }

        this.loading = true;
        const response = await fetch(`/api/search?q=${encodeURIComponent(this.search)}`);
        this.results = await response.json();
        this.loading = false;
    }
}" x-init="$watch('search', value => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => performSearch(), 300);
})">
    <div class="relative">
        <input
            type="text"
            x-model="search"
            placeholder="Rechercher..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        />

        <div x-show="loading" class="absolute right-3 top-3">
            <svg class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div x-show="results.length > 0" class="absolute w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
            <template x-for="result in results" :key="result.id">
                <a :href="result.url" class="block px-4 py-2 hover:bg-gray-100">
                    <span x-text="result.name"></span>
                </a>
            </template>
        </div>
    </div>
</div>
```

---

## Bonnes Pratiques

### 1. Utiliser x-cloak pour Éviter FOUC

```blade
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ open: false }" x-cloak>
    <div x-show="open">
        Contenu qui n'apparaît pas avant Alpine.js
    </div>
</div>
```

---

### 2. Éviter la Duplication de Code

**❌ Mauvais:**

```blade
<button @click="count++" class="...">+</button>
<button @click="count++" class="...">+</button>
<button @click="count++" class="...">+</button>
```

**✅ Bon:**

```blade
<div x-data="{ increment() { this.count++ } }">
    <button @click="increment()" class="...">+</button>
    <button @click="increment()" class="...">+</button>
    <button @click="increment()" class="...">+</button>
</div>
```

---

### 3. Utiliser Alpine.store pour État Global

```javascript
// Dans app.js
Alpine.store('app', {
    darkMode: localStorage.getItem('darkMode') === 'true',

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        document.documentElement.classList.toggle('dark', this.darkMode);
    }
});
```

```blade
<div x-data>
    <button @click="$store.app.toggleDarkMode()">
        Toggle Dark Mode
    </button>
    <span x-text="$store.app.darkMode ? 'Dark' : 'Light'"></span>
</div>
```

---

### 4. Nettoyer les Event Listeners

```blade
<div x-data="{ handleResize() { console.log('Resize'); } }"
     @resize.window="handleResize()"
     x-init="console.log('Initialized')"
     x-destroy="console.log('Destroyed')">
    Content
</div>
```

---

### 5. Éviter les Fonctions Complexes dans x-data

**❌ Mauvais:**

```blade
<div x-data="{
    async loadData() {
        const response = await fetch('/api/data');
        const data = await response.json();
        this.items = data.map(item => ({ ...item, selected: false }));
        this.loading = false;
    }
}">
```

**✅ Bon:**

```javascript
// Créer un composant Alpine réutilisable
Alpine.data('dataLoader', () => ({
    items: [],
    loading: false,

    async loadData() {
        this.loading = true;
        const response = await fetch('/api/data');
        const data = await response.json();
        this.items = data.map(item => ({ ...item, selected: false }));
        this.loading = false;
    }
}));
```

```blade
<div x-data="dataLoader" x-init="loadData()">
    <!-- ... -->
</div>
```

---

**Maintenu par:** ZenFleet Development Team
**Dernière mise à jour:** 18 Octobre 2025
