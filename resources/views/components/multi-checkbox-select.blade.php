@props([
'name' => '',
'label' => null,
'error' => null,
'helpText' => null,
'required' => false,
'disabled' => false,
'options' => [],
'selected' => [], // Doit être un tableau d'IDs/valeurs
'placeholder' => 'Sélectionnez...',
])

@php
$fieldId = 'multi-select-' . $name . '-' . uniqid();

// Logique de pré-sélection : Priorité à old() > $selected
$oldValues = old($name);

if (!is_null($oldValues)) {
// Si des valeurs 'old' existent (retour de validation), on les utilise
$selectedValues = is_array($oldValues) ? $oldValues : [];
} else {
// Sinon on utilise les valeurs passées en prop
$selectedValues = is_array($selected) ? $selected : (is_string($selected) ? json_decode($selected, true) : []);
}

if (is_null($selectedValues)) $selectedValues = [];

// Convertir les clés des options en tableau simple pour Alpine.js
$optionKeys = array_keys($options);
@endphp

<div x-data="{ 
    open: false, 
    selected: @js($selectedValues), 
    options: @js($optionKeys),
    toggle(value) {
        const index = this.selected.indexOf(value);
        if (index === -1) {
            this.selected.push(value);
        } else {
            this.selected.splice(index, 1);
        }
        // Dispatch change event pour la validation Alpine.js externe
        this.$el.dispatchEvent(new CustomEvent('change', { detail: { selected: this.selected } }));
    },
    isSelected(value) {
        return this.selected.includes(value);
    },
    get selectedLabels() {
        if (this.selected.length === 0) {
            return '{{ $placeholder }}';
        }
        // Afficher uniquement les abréviations (valeurs) pour optimiser l'affichage
        return this.selected.join(', ');
    }
}"
    @click.outside="open = false"
    {{ $attributes->merge(['class' => 'relative']) }}>

    @if($label)
    <label for="{{ $fieldId }}" class="block mb-2 text-sm font-medium text-gray-900">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <!-- Bouton d'affichage -->
    <button type="button"
        @click="open = !open"
        :aria-expanded="open"
        aria-haspopup="true"
        class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg shadow-sm px-4 py-2.5 text-left cursor-default focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
        :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': '{{ $error }}' }">
        <span x-text="selectedLabels" class="block truncate" :class="{ 'text-gray-500': selected.length === 0 }"></span>
        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    <!-- Liste des options -->
    <div x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-10 mt-1 w-full rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none max-h-60 overflow-auto"
        style="display: none;">

        <ul class="py-1 text-base ring-1 ring-gray-200 rounded-lg">
            @foreach($options as $value => $label)
            <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('{{ $value }}')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="{{ $name }}[]"
                        value="{{ $value }}"
                        :checked="isSelected('{{ $value }}')"
                        class="hidden"
                        :id="'{{ $fieldId . '-' . $value }}'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('{{ $value }}'), 'bg-white': !isSelected('{{ $value }}') }">
                        <svg x-show="isSelected('{{ $value }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        {{ $label }}
                    </span>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

    <!-- Le champ caché qui envoyait une chaîne de caractères a été supprimé. 
    La soumission est maintenant gérée par les checkboxes cachées avec name="{{ $name }}[]", 
    ce qui assure que le serveur reçoit un tableau comme requis par la validation. -->

    @if($error)
    <p class="mt-2 text-sm text-red-600 flex items-start">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>{{ $error }}</span>
    </p>
    @elseif($helpText)
    <p class="mt-2 text-sm text-gray-500">
        {{ $helpText }}
    </p>
    @endif

    {{-- Erreur dynamique Alpine.js --}}
    <p x-show="fieldErrors && fieldErrors['{{ $name }}'] && touchedFields && touchedFields['{{ $name }}']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span x-text="fieldErrors['{{ $name }}']"></span>
    </p>
</div>