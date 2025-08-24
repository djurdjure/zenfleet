@props([
    'name' => '',
    'id' => '',
    'value' => '',
    'label' => '',
    'required' => false,
])

<div x-data="datePicker({ value: '{{ $value }}' })" x-init="init()" class="w-full">
    <x-input-label :for="$id" :value="$label" :required="$required" />
    <div class="relative">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            x-ref="datePicker"
            type="text"
            value="{{ $value }}"
            placeholder="Sélectionnez une date..."
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
        />
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <x-lucide-calendar class="h-5 w-5 text-gray-400" />
        </div>
    </div>
</div>

@pushOnce('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    function datePicker(config) {
        return {
            value: config.value,
            instance: null,
            init() {
                this.instance = flatpickr(this.$refs.datePicker, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'j F Y',
                    defaultDate: this.value,
                });
            }
        }
    }
</script>
@endPushOnce
