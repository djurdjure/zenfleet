@props([
 'name',
 'label',
 'type' => 'text',
 'value' => '',
 'required' => false,
 'placeholder' => '',
 'help' => '',
 'icon' => '',
 'options' => [],
 'rows' => 3
])

<div class="zenfleet-form-group">
 <label for="{{ $name }}" class="zenfleet-label {{ $required ? 'zenfleet-label-required' : '' }}">
 @if($icon)
 <i class="fas {{ $icon }} mr-2 text-gray-400"></i>
 @endif
 {{ $label }}
 </label>

 @if($help)
 <p class="text-xs text-gray-500 mt-1">{{ $help }}</p>
 @endif

 <div class="relative">
 @switch($type)
 @case('select')
 <select name="{{ $name }}" id="{{ $name }}" {{ $required ? 'required' : '' }}
 class="zenfleet-select zenfleet-fade-in">
 @if($placeholder)
 <option value="">{{ $placeholder }}</option>
 @endif
 @foreach($options as $value => $optionLabel)
 <option value="{{ $value }}" {{ old($name, $value) == $value ? 'selected' : '' }}>
 {{ $optionLabel }}
 </option>
 @endforeach
 </select>
 @break

 @case('textarea')
 <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}"
 {{ $required ? 'required' : '' }}
 placeholder="{{ $placeholder }}"
 class="zenfleet-input zenfleet-fade-in resize-none">{{ old($name, $value) }}</textarea>
 @break

 @case('file')
 <div class="relative">
 <input type="file" name="{{ $name }}" id="{{ $name }}"
 {{ $required ? 'required' : '' }}
 class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
 accept="{{ $attributes->get('accept', '') }}">
 <div class="zenfleet-input text-center py-8 border-2 border-dashed border-gray-300 hover:border-gray-400 transition-colors zenfleet-fade-in">
 <div class="space-y-2">
 <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
 <div class="text-sm text-gray-600">
 <span class="font-semibold text-blue-600">Cliquez pour sélectionner</span>
 ou glissez-déposez un fichier
 </div>
 <div class="text-xs text-gray-500">
 {{ $attributes->get('accept-text', 'PDF, JPG, PNG jusqu\'à 2MB') }}
 </div>
 </div>
 </div>
 </div>
 @break

 @default
 <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
 value="{{ old($name, $value) }}"
 {{ $required ? 'required' : '' }}
 placeholder="{{ $placeholder }}"
 class="zenfleet-input zenfleet-fade-in">
 @endswitch

 @if($icon && $type !== 'file')
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <i class="fas {{ $icon }} text-gray-400"></i>
 </div>
 @endif
 </div>

 @error($name)
 <div class="zenfleet-alert-error mt-2 zenfleet-scale-in">
 <i class="fas fa-exclamation-circle text-red-500"></i>
 <span>{{ $message }}</span>
 </div>
 @enderror
</div>