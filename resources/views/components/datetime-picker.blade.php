@props([
 'name' => '',
 'id' => '',
 'value' => '',
 'label' => '',
 'required' => false,
])

<div
 x-data="dateTimePicker({
 value: '{{ $value }}',
 name: '{{ $name }}',
 id: '{{ $id }}'
 })"
 x-init="init()"
 class="flex w-full items-center"
>
 <div class="ml-3 flex items-center first:ml-0 flex-1 min-w-0">
 <div class="relative trigger w-full">
 <x-input-label :for="$id . '-date'" :value="$label" />
 <div class="relative">
 <input
 x-ref="datePicker"
 id="{{ $id }}-date"
 type="text"
 class="form-input block w-full h-8 text-md pl-10 pr-4 py-3 text-left"
 placeholder="Select Date"
 />
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
 <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm10 5H4v8h12V7z" clip-rule="evenodd" />
 </svg>
 </div>
 </div>
 <input x-ref="hiddenInput" name="{{ $name }}" id="{{ $id }}" type="hidden" value="{{ $value }}">
 </div>
 </div>
 <div class="ml-3 flex items-center first:ml-0">
 <div class="relative trigger w-full" style="max-width: 175px;">
 <x-input-label for="{{ $id }}-time" value="Time" />
 <div class="relative">
 <input
 x-ref="timePicker"
 id="{{ $id }}-time"
 type="text"
 class="form-input block w-full h-8 text-md pl-10 pr-4 py-3 text-left"
 placeholder="Select Time"
 disabled
 />
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l3 3a1 1 0 001.414-1.414L11 10.586V6z" clip-rule="evenodd" />
 </svg>
 </div>
 </div>
 </div>
 </div>
</div>

@push('scripts')
<script>
 function dateTimePicker(config) {
 return {
 value: config.value,
 name: config.name,
 id: config.id,
 datePicker: null,
 timePicker: null,

 init() {
 const now = new Date();
 const minutes = now.getMinutes();
 const roundedMinutes = Math.round(minutes / 30) * 30;
 now.setMinutes(roundedMinutes);
 now.setSeconds(0);

 this.datePicker = flatpickr(this.$refs.datePicker, {
 dateFormat: 'Y-m-d',
 onChange: (selectedDates, dateStr, instance) => {
 if (selectedDates.length > 0) {
 this.timePicker.set('minDate', dateStr);
 this.timePicker.set('maxDate', dateStr);
 this.$refs.timePicker.disabled = false;
 if (!this.timePicker.selectedDates.length) {
 this.timePicker.setDate(now, false);
 }
 } else {
 this.$refs.timePicker.disabled = true;
 this.timePicker.clear();
 }
 this.updateHiddenInput();
 }
 });

 this.timePicker = flatpickr(this.$refs.timePicker, {
 enableTime: true,
 noCalendar: true,
 dateFormat: 'H:i',
 minuteIncrement: 30,
 time_24hr: true,
 allowInput: true,
 onChange: (selectedDates, dateStr, instance) => {
 this.updateHiddenInput();
 }
 });

 if (this.value) {
 const [date, time] = this.value.split(' ');
 this.datePicker.setDate(date);
 this.timePicker.setDate(time);
 this.$refs.timePicker.disabled = false;
 } else {
 // Set default time when no value is provided
 this.timePicker.setDate(now, false);
 }
 },

 updateHiddenInput() {
 const date = this.$refs.datePicker.value;
 const time = this.$refs.timePicker.value;

 if (date && time) {
 this.$refs.hiddenInput.value = `${date} ${time}`;
 } else {
 this.$refs.hiddenInput.value = '';
 }
 }
 }
 }
</script>
@endpush