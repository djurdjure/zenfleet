@props([
 'name' => '',
 'label' => null,
 'error' => null,
 'helpText' => null,
 'required' => false,
 'disabled' => false,
 'options' => [],
 'selected' => null,
])

@php
 $component = new \App\View\Components\Select($name, $label, $error, $helpText, $required, $disabled, $options, $selected);
 $classes = $component->getClasses();
 $selectId = $component->getId();
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
 @if($label)
 <label for="{{ $selectId }}" class="block mb-2 text-sm font-medium text-gray-900 ">
 {{ $label }}
 @if($required)
 <span class="text-red-500">*</span>
 @endif
 </label>
 @endif

 <select
 name="{{ $name }}"
 id="{{ $selectId }}"
 class="{{ $classes }}"
 @if($required) required @endif
 @if($disabled) disabled @endif
 {{ $attributes->except(['class']) }}
 >
 {{-- ✨ ENTERPRISE FIX: Slot pour options custom OU options depuis prop --}}
 @if($slot->isNotEmpty())
 {{-- Options passées via le slot (méthode recommandée) --}}
 {{ $slot }}
 @else
 {{-- Options passées via la prop $options (méthode alternative) --}}
 @if(!$required && !isset($options['']))
 <option value="">Sélectionner...</option>
 @endif

 @foreach($options as $value => $label)
 <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
 {{ $label }}
 </option>
 @endforeach
 @endif
 </select>

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
</div>
