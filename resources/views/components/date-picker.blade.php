@props([
'name' => '',
'label' => null,
'error' => null,
'required' => false,
'disabled' => false,
'placeholder' => 'JJ/MM/AAAA',
])

<div
    x-data="zenfleetDatepicker"
    x-modelable="value"
    {{ $attributes->whereStartsWith('wire:model') }}
    class="w-full relative">
    @if($label)
    <label class="block mb-1 text-xs font-medium text-gray-700">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
    @endif

    <div class="relative">
        <input
            x-ref="displayInput"
            type="text"
            placeholder="{{ $placeholder }}"
            @if($disabled) disabled @endif
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs shadow-sm bg-gray-50 hover:border-gray-400 transition-colors {{ $error ? 'border-red-500 bg-red-50' : '' }}" />

        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <x-iconify icon="lucide:calendar" class="w-4 h-4 text-gray-400" />
        </div>

        {{-- Clear Icon --}}
        <div
            class="absolute inset-y-0 right-0 pr-2 flex items-center cursor-pointer"
            x-show="displayValue"
            @click="clear()"
            style="display: none;">
            <x-iconify icon="lucide:x" class="w-4 h-4 text-gray-400 hover:text-gray-600" />
        </div>
    </div>

    @if($error)
    <p class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>