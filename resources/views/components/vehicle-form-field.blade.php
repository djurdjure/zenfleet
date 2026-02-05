@props([
 'name',
 'label',
 'type' => 'text',
 'value' => '',
 'required' => false,
 'placeholder' => '',
 'options' => [],
 'help' => null,
 'icon' => null
])

@php
 $hasError = $errors->has($name);
 $inputId = 'field_' . $name;
 $errorId = 'error_' . $name;
@endphp

<div class="relative">
 <!-- Label avec pastille d'erreur -->
 <div class="flex items-center justify-between mb-2">
 <label for="{{ $inputId }}" class="block text-sm font-semibold text-gray-700">
 @if($icon)
 <i class="{{ $icon }} text-gray-400 mr-2"></i>
 @endif
 {{ $label }}
 @if($required)
 <span class="text-red-500 ml-1">*</span>
 @endif
 </label>

 @if($hasError)
 <div class="flex items-center gap-1">
 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200 animate-pulse">
 <i class="fas fa-exclamation-circle mr-1"></i>
 Erreur
 </span>
 </div>
 @endif
 </div>

 <!-- Input Field -->
 @if($type === 'select')
 <select
 name="{{ $name }}"
 id="{{ $inputId }}"
 class="w-full px-4 py-3 rounded-xl border-2 transition-all duration-200 focus:outline-none focus:ring-4
 {{ $hasError
 ? 'border-red-300 bg-red-50 text-red-900 placeholder-red-400 focus:border-red-500 focus:ring-red-200'
 : 'border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-100' }}"
 @if($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif>

 <option value="">{{ $placeholder ?: 'Sélectionner une option...' }}</option>
 @foreach($options as $optionValue => $optionLabel)
 <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
 {{ $optionLabel }}
 </option>
 @endforeach
 </select>
 @elseif($type === 'textarea')
 <textarea
 name="{{ $name }}"
 id="{{ $inputId }}"
 rows="4"
 placeholder="{{ $placeholder }}"
 class="w-full px-4 py-3 rounded-xl border-2 transition-all duration-200 focus:outline-none focus:ring-4 resize-none
 {{ $hasError
 ? 'border-red-300 bg-red-50 text-red-900 placeholder-red-400 focus:border-red-500 focus:ring-red-200'
 : 'border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-100' }}"
 @if($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif>{{ old($name, $value) }}</textarea>
 @else
 <input
 type="{{ $type }}"
 name="{{ $name }}"
 id="{{ $inputId }}"
 value="{{ old($name, $value) }}"
 placeholder="{{ $placeholder }}"
 @if($required) required @endif
 class="w-full px-4 py-3 rounded-xl border-2 transition-all duration-200 focus:outline-none focus:ring-4
 {{ $hasError
 ? 'border-red-300 bg-red-50 text-red-900 placeholder-red-400 focus:border-red-500 focus:ring-red-200'
 : 'border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-100' }}"
 @if($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif />
 @endif

 <!-- Error Message avec animation -->
 @if($hasError)
 <div id="{{ $errorId }}" class="mt-2 flex items-start gap-2 text-red-600 animate-fadeIn">
 <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 flex-shrink-0"></i>
 <div class="text-sm">
 @foreach($errors->get($name) as $error)
 <p class="font-medium">{{ $error }}</p>
 @endforeach
 </div>
 </div>
 @endif

 <!-- Help Text -->
 @if($help && !$hasError)
 <p class="mt-2 text-sm text-gray-500 flex items-center gap-1">
 <i class="fas fa-info-circle text-gray-400"></i>
 {{ $help }}
 </p>
 @endif
</div>

<style>
@keyframes fadeIn {
 from { opacity: 0; transform: translateY(-5px); }
 to { opacity: 1; transform: translateY(0); }
}

.animate-fadeIn {
 animation: fadeIn 0.3s ease-out;
}
</style>