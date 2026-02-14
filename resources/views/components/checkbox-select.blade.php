@props([
'name' => '',
'label' => null,
'options' => [],
'selected' => [],
'placeholder' => 'Sélectionner...',
'error' => null,
'helpText' => null,
'required' => false,
])

<div x-data="{
    open: false,
    selected: @js($selected),
    options: @js($options),
    
    toggle() {
        this.open = !this.open;
    },
    
    close() {
        this.open = false;
    },
    
    toggleOption(value) {
        const index = this.selected.indexOf(value);
        if (index === -1) {
            this.selected.push(value);
        } else {
            this.selected.splice(index, 1);
        }
        // Dispatch change event manually since we are not using a native select
        this.$dispatch('change', this.selected);
    },
    
    isSelected(value) {
        return this.selected.includes(value);
    },
    
    get displayText() {
        if (this.selected.length === 0) return '{{ $placeholder }}';
        if (this.selected.length === 1) {
            const val = this.selected[0];
            return this.options[val] || val;
        }
        return this.selected.length + ' sélectionnés';
    }
}"
    class="relative"
    @click.outside="close()">

    @if($label)
    <label class="block mb-2 text-sm font-medium text-gray-600">
        {{ $label }}
        @if($required)
        <span class="text-red-600">*</span>
        @endif
    </label>
    @endif

    {{-- Trigger Button --}}
    <button type="button"
        @click="toggle()"
        class="relative w-full cursor-pointer bg-white border border-gray-300 rounded-lg py-2.5 pl-3 pr-10 text-left shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:text-sm"
        :class="{'border-red-300 focus:border-red-500 focus:ring-red-500': {{ $error ? 'true' : 'false' }}}">
        <span class="block truncate" x-text="displayText"></span>
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
            <x-iconify icon="heroicons:chevron-up-down" class="h-5 w-5 text-gray-400" />
        </span>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
        style="display: none;">

        <ul class="p-1 space-y-1">
            @foreach($options as $value => $label)
            <li class="relative cursor-pointer select-none rounded-md py-2 pl-3 pr-9 hover:bg-blue-50 text-gray-900"
                @click="toggleOption('{{ $value }}')">
                <div class="flex items-center">
                    <div class="flex items-center h-5">
                        <input type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            :checked="isSelected('{{ $value }}')"
                            readonly> {{-- Readonly because the LI click handles logic --}}
                    </div>
                    <span class="ml-3 block truncate font-normal"
                        :class="{'font-semibold': isSelected('{{ $value }}')}">
                        {{ $label }}
                    </span>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Hidden Inputs for Form Submission --}}
    <template x-for="value in selected" :key="value">
        <input type="hidden" name="{{ $name }}" :value="value">
    </template>

    {{-- Error Message --}}
    @if($error)
    <p class="mt-2 text-sm text-red-600 flex items-start">
        <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" />
        <span>{{ $error }}</span>
    </p>
    @elseif($helpText)
    <p class="mt-2 text-sm text-gray-500">
        {{ $helpText }}
    </p>
    @endif

    {{-- Validation Client-side Error (Alpine) --}}
    <p x-show="fieldErrors && fieldErrors['{{ str_replace('[]', '', $name) }}'] && touchedFields && touchedFields['{{ str_replace('[]', '', $name) }}']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>Ce champ est obligatoire</span>
    </p>

</div>
