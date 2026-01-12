@props([
'name' => '',
'label' => null,
'error' => null,
'helpText' => null,
'required' => false,
'disabled' => false,
'value' => null,
'minDate' => null,
'maxDate' => null,
'placeholder' => 'Sélectionner une date',
]) @php
$inputId = 'datepicker-' . uniqid();
$rawValue = old($name, $value); // Convert server value to display format (d/m/Y) for Flowbite
$displayValue = '';
if ($rawValue) {
// Handle Y-m-d format from database
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawValue)) {
$date = \Carbon\Carbon::createFromFormat('Y-m-d', $rawValue);
$displayValue = $date->format('d/m/Y');
}
// Handle d/m/Y format from old() flash
elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $rawValue)) {
$displayValue = $rawValue;
}
} // Input classes following Flowbite pattern
$inputClasses = 'block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all duration-200';
$inputClasses .= $error ? ' border-red-500' : ' border-gray-300';
@endphp <div {{ $attributes->merge(['class' => 'w-full']) }} x-data="{ serverDate: '{{ $rawValue }}', displayValue: '{{ $displayValue }}', picker: null, init() { this.$nextTick(() => { const el = this.$refs.displayInput; const component = this; let isOpening = false; // Flag to prevent immediate close if (typeof window.Datepicker === 'undefined') { console.error('❌ ZenFleet: Datepicker not loaded'); return; } // Initialize Flowbite Datepicker this.picker = new window.Datepicker(el, { language: 'fr', format: 'dd/mm/yyyy', autohide: true, todayBtn: true, todayBtnMode: 1, clearBtn: true, weekStart: 1, @if($minDate) minDate: '{{ \Carbon\Carbon::parse($minDate)->format('d/m/Y') }}', @endif @if($maxDate) maxDate: '{{ \Carbon\Carbon::parse($maxDate)->format('d/m/Y') }}', @endif orientation: 'bottom left', }); // ✅ ENTERPRISE-GRADE: Force hide function const forceHidePicker = () => { if (!component.picker || isOpening) return; const pickerEl = component.picker.picker?.element; if (pickerEl) { pickerEl.style.display = 'none'; pickerEl.classList.remove('active', 'block'); pickerEl.classList.add('hidden'); if (component.picker.picker) { component.picker.picker.active = false; } } }; // ✅ ENTERPRISE-GRADE: Force show function (reset display) const ensurePickerVisible = () => { const pickerEl = component.picker.picker?.element; if (pickerEl) { pickerEl.style.display = ''; pickerEl.classList.remove('hidden'); } }; // Set initial date if value exists if (this.displayValue) { this.picker.setDate(this.displayValue); el.value = this.displayValue; } // ✅ Listen for show event to reset display and set flag el.addEventListener('show', () => { isOpening = true; ensurePickerVisible(); setTimeout(() => { isOpening = false; }, 100); }); // Handle date change - force close on selection el.addEventListener('changeDate', (e) => { if (e.detail.date) { const d = e.detail.date; const year = d.getFullYear(); const month = String(d.getMonth() + 1).padStart(2, '0'); const day = String(d.getDate()).padStart(2, '0'); component.serverDate = `${year}-${month}-${day}`; component.displayValue = `${day}/${month}/${year}`; component.$dispatch('input', component.serverDate); // Force hide after selection setTimeout(forceHidePicker, 10); } else { component.serverDate = ''; component.displayValue = ''; component.$dispatch('input', ''); } }); // ✅ ENTERPRISE-GRADE: Click outside handler document.addEventListener('mousedown', (e) => { if (!component.picker || isOpening) return; const pickerEl = component.picker.picker?.element; if (!pickerEl) return; // Only check active class for visibility (more reliable) const isVisible = pickerEl.classList.contains('active'); if (!isVisible) return; // Check if click is outside both input and picker if (!pickerEl.contains(e.target) && !el.contains(e.target)) { forceHidePicker(); } }); // Handle manual clear el.addEventListener('input', (e) => { if (!el.value.trim()) { component.serverDate = ''; component.displayValue = ''; component.$dispatch('input', ''); } }); }); } }" wire:ignore> @if($label) <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-gray-900"> {{ $label }} @if($required) <span class="text-red-500 ml-0.5">*</span> @endif </label> @endif <div class="relative"> {{-- Calendar Icon --}} <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10"> <svg class="w-4 h-4 {{ $error ? 'text-red-500' : 'text-gray-500' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"> <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" /> </svg> </div> {{-- Display Input (User sees this) --}} <input x-ref="displayInput" type="text" id="{{ $inputId }}" class="{{ $inputClasses }}" placeholder="{{ $placeholder }}" x-model="displayValue" @if($disabled) disabled @endif @if($required) required @endif autocomplete="off" readonly> {{-- Hidden Input (Server receives this in Y-m-d format) --}} <input type="hidden" name="{{ $name }}" x-model="serverDate"> </div> @if($error) <p class="mt-2 text-sm text-red-600 flex items-center gap-1"> <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"> <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /> </svg> {{ $error }} </p> @elseif($helpText) <p class="mt-1 text-xs text-gray-500">{{ $helpText }}</p> @endif
</div>