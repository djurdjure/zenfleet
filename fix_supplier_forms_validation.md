# INSTRUCTIONS: TRANSFORMER LES COMPOSANTS EN HTML NATIF

## Exemple 1: x-input => HTML Native

**AVANT (ne fonctionne PAS):**
```blade
<x-input
    name="contact_phone"
    label="Téléphone"
    icon="phone"
    placeholder="Ex: 0561234567"
    :value="old('contact_phone')"
    required
    :error="$errors->first('contact_phone')"
/>
```

**APRÈS (fonctionne avec Alpine.js):**
```blade
<div class="" @blur="validateField('contact_phone', $event.target.value)">
    <label for="contact_phone" class="block mb-2 text-sm font-medium text-gray-900">
        Téléphone <span class="text-red-600">*</span>
    </label>
    
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <x-iconify icon="heroicons:phone" class="w-5 h-5 text-gray-400" />
        </div>
        
        <input
            type="tel"
            name="contact_phone"
            id="contact_phone"
            required
            placeholder="Ex: 0561234567"
            value="{{ old('contact_phone') }}"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10"
            x-bind:class="(fieldErrors && fieldErrors['contact_phone'] && touchedFields && touchedFields['contact_phone']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            @blur="validateField('contact_phone', $event.target.value)"
        />
    </div>

    <p x-show="fieldErrors && fieldErrors['contact_phone'] && touchedFields && touchedFields['contact_phone']"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 transform -translate-y-1"
       x-transition:enter-end="opacity-100 transform translate-y-0"
       class="mt-2 text-sm text-red-600 flex items-start font-medium"
       style="display: none;">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>Ce champ est obligatoire</span>
    </p>
</div>
```

## Exemple 2: x-tom-select => HTML Native

**AVANT:**
```blade
<x-tom-select
    name="supplier_type"
    label="Type de Fournisseur"
    :options="App\Models\Supplier::getSupplierTypes()"
    :selected="old('supplier_type')"
    required
    :error="$errors->first('supplier_type')"
/>
```

**APRÈS:**
```blade
<div class="" @change="validateField('supplier_type', $event.target.value)">
    <label for="supplier_type" class="block mb-2 text-sm font-medium text-gray-900">
        Type de Fournisseur <span class="text-red-600">*</span>
    </label>
    
    <select
        name="supplier_type"
        id="supplier_type"
        required
        class="tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
        @change="validateField('supplier_type', $event.target.value)"
    >
        <option value="">Sélectionnez un type...</option>
        @foreach(App\Models\Supplier::getSupplierTypes() as $key => $label)
            <option value="{{ $key }}" {{ old('supplier_type') == $key ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>

    <p x-show="fieldErrors && fieldErrors['supplier_type'] && touchedFields && touchedFields['supplier_type']"
       class="mt-2 text-sm text-red-600 flex items-start"
       style="display: none;">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>Ce champ est obligatoire</span>
    </p>
</div>
```

---

## LISTE COMPLÈTE DES TRANSFORMATIONS NÉCESSAIRES

### create.blade.php (22 champs à transformer)

1. supplier_type
2. supplier_category_id
3. trade_register
4. nif
5. nis
6. ai
7. contact_first_name
8. contact_last_name
9. contact_phone
10. contact_email
11. phone
12. email
13. website
14. address (textarea - ajouter x-bind:class)
15. wilaya
16. city
17. commune
18. postal_code
19. rating
20. quality_score
21. reliability_score
22. notes (textarea simple OK)

### edit.blade.php (MÊMES 22 champs + values pré-remplies)

---

**Tâche:** Droid va maintenant transformer TOUS ces champs un par un.
