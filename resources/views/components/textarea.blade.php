@props([
 'name' => '',
 'label' => null,
 'placeholder' => null,
 'error' => null,
 'helpText' => null,
 'required' => false,
 'disabled' => false,
 'value' => null,
 'rows' => 4,
])

@php
 $baseKey = preg_replace('/\[\]$/', '', $name);
 $lookupKey = trim(preg_replace('/\[(.*?)\]/', '.$1', $baseKey), '.');
 $resolvedError = $error ?: ((isset($errors) && $lookupKey !== '') ? ($errors->first($lookupKey) ?: $errors->first($lookupKey . '.0')) : null);
 $component = new \App\View\Components\Textarea($name, $label, $placeholder, $resolvedError, $helpText, $required, $disabled, $value, $rows);
 $classes = $component->getClasses();
 $textareaId = $component->getId();
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
 @if($label)
 <label for="{{ $textareaId }}" class="block mb-2 text-sm font-medium text-gray-600">
 {{ $label }}
 @if($required)
 <span class="text-red-600">*</span>
 @endif
 </label>
 @endif

 <textarea
 name="{{ $name }}"
 id="{{ $textareaId }}"
 rows="{{ $rows }}"
 class="{{ $classes }}"
 placeholder="{{ $placeholder }}"
 aria-invalid="{{ $resolvedError ? 'true' : 'false' }}"
 @if($required) required @endif
 @if($disabled) disabled @endif
 {{ $attributes->except(['class']) }}
 >{{ old($lookupKey !== '' ? $lookupKey : $name, $value) }}</textarea>

 @if($resolvedError)
 <p class="mt-2 text-sm text-red-600 flex items-start font-medium">
 <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
 <span>{{ $resolvedError }}</span>
 </p>
 @elseif($helpText)
 <p class="mt-2 text-xs text-gray-600">
 {{ $helpText }}
 </p>
 @endif
</div>
