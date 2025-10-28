@props([
    'name' => '',
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => '-- Sélectionner --',
    'required' => false,
    'disabled' => false,
    'error' => null,
    'helpText' => null,
    'icon' => null,
    'emptyMessage' => 'Veuillez sélectionner une option dans la liste', // Message en français
])

@php
    // Générer un ID unique
    $inputId = 'select-' . uniqid();
    
    // Classes conditionnelles pour erreur
    $selectClasses = $error
        ? 'bg-red-50 border-2 border-red-500 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-2.5 pr-10 transition-all duration-200 animate-shake'
        : 'bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 block w-full p-2.5 pr-10 transition-all duration-200';
    
    // Classes pour le label
    $labelClasses = $error
        ? 'block mb-2 text-sm font-medium text-red-700'
        : 'block mb-2 text-sm font-medium text-gray-900';
        
    // Classes pour l'icône dropdown
    $iconClasses = $error
        ? 'text-red-500'
        : 'text-gray-500';
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    @if($label)
        <label for="{{ $inputId }}" class="{{ $labelClasses }}">
            @if($icon)
                <span class="inline-flex items-center">
                    <x-iconify :icon="$icon" class="w-4 h-4 mr-1.5" />
                    {{ $label }}
                </span>
            @else
                {{ $label }}
            @endif
            @if($required)
                <span class="text-red-600 ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative group">
        <select
            name="{{ $name }}"
            id="{{ $inputId }}"
            class="{{ $selectClasses }}"
            @if($required) 
                required 
                oninvalid="this.setCustomValidity('{{ $emptyMessage }}')"
                oninput="this.setCustomValidity('')"
            @endif
            @if($disabled) disabled @endif
            {{ $attributes->except(['class']) }}
        >
            @if($placeholder)
                <option value="" {{ !$value ? 'selected' : '' }}>{{ $placeholder }}</option>
            @endif
            
            @foreach($options as $optionValue => $optionLabel)
                @if(is_array($optionLabel))
                    {{-- Si c'est un groupe d'options --}}
                    <optgroup label="{{ $optionValue }}">
                        @foreach($optionLabel as $subValue => $subLabel)
                            <option value="{{ $subValue }}" {{ old($name, $value) == $subValue ? 'selected' : '' }}>
                                {{ $subLabel }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    {{-- Option simple --}}
                    <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endif
            @endforeach
        </select>

        {{-- Icône dropdown personnalisée --}}
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <x-iconify icon="lucide:chevron-down" class="w-4 h-4 {{ $iconClasses }}" />
        </div>
    </div>

    {{-- Messages d'erreur ou d'aide --}}
    @if($error)
        <div class="mt-2 flex items-start animate-fadeIn">
            <x-iconify icon="lucide:alert-triangle" class="w-4 h-4 text-red-600 mr-1.5 mt-0.5 flex-shrink-0" />
            <div>
                <p class="text-sm text-red-600 font-medium">{{ $error }}</p>
                @if($required && !old($name, $value))
                    <p class="text-xs text-red-500 mt-0.5">Cette information est obligatoire</p>
                @endif
            </div>
        </div>
    @elseif($helpText)
        <p class="mt-2 text-sm text-gray-600 flex items-start">
            <x-iconify icon="lucide:info-circle" class="w-4 h-4 text-gray-400 mr-1.5 mt-0.5 flex-shrink-0" />
            <span>{{ $helpText }}</span>
        </p>
    @endif
</div>

@once
    @push('styles')
    <style>
        /* Animation shake pour les erreurs */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }
        
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
        
        /* Animation fadeIn pour les messages */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Style amélioré pour le select */
        select.border-red-500 {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ef4444' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
        }
        
        /* Supprime l'apparence native du select pour un style uniforme */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
        }
        
        select:focus {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%233b82f6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        }
        
        /* Style pour optgroup */
        optgroup {
            font-weight: 600;
            color: rgb(75 85 99);
        }
        
        option {
            font-weight: 400;
            color: rgb(17 24 39);
        }
        
        /* Hover sur les options (fonctionne sur certains navigateurs) */
        option:hover {
            background-color: rgb(239 246 255);
        }
    </style>
    @endpush
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Améliorer la validation des selects
            document.querySelectorAll('select[required]').forEach(function(select) {
                // Retirer l'erreur quand une option est sélectionnée
                select.addEventListener('change', function() {
                    if (this.value !== '') {
                        // Retirer les classes d'erreur
                        this.classList.remove('border-red-500', 'bg-red-50', 'animate-shake');
                        this.classList.add('border-gray-300', 'bg-white');
                        
                        // Cacher le message d'erreur
                        const errorDiv = this.closest('.relative').parentElement.querySelector('.animate-fadeIn');
                        if (errorDiv) {
                            errorDiv.style.display = 'none';
                        }
                    }
                });
                
                // Ajouter une validation visuelle lors de la soumission
                const form = select.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        if (select.value === '' && select.hasAttribute('required')) {
                            select.classList.add('border-red-500', 'bg-red-50', 'animate-shake');
                            select.classList.remove('border-gray-300', 'bg-white');
                            
                            // Scroll jusqu'au premier champ en erreur
                            if (document.querySelectorAll('.border-red-500')[0] === select) {
                                select.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }
                    });
                }
            });
        });
    </script>
    @endpush
@endonce
