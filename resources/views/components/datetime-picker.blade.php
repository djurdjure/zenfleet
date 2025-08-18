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
            <input
                x-ref="datePicker"
                id="{{ $id }}-date"
                type="text"
                class="form-input block w-full h-8 text-md px-4 py-3 text-left"
                placeholder="Select Date"
            />
            <input x-ref="hiddenInput" name="{{ $name }}" id="{{ $id }}" type="hidden" value="{{ $value }}">
        </div>
    </div>
    <div class="ml-3 flex items-center first:ml-0">
        <div class="relative trigger w-full" style="max-width: 175px;">
            <x-input-label for="{{ $id }}-time" value="Time" />
            <input
                x-ref="timePicker"
                id="{{ $id }}-time"
                type="text"
                class="form-input block w-full h-8 text-md px-4 py-3 text-left"
                placeholder="Select Time"
                disabled
            />
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
                this.datePicker = flatpickr(this.$refs.datePicker, {
                    dateFormat: 'Y-m-d',
                    onChange: (selectedDates, dateStr, instance) => {
                        this.timePicker.set('minDate', dateStr);
                        this.timePicker.set('maxDate', dateStr);
                        this.timePicker.clear();
                        this.$refs.timePicker.disabled = false;
                        this.updateHiddenInput();
                    }
                });

                this.timePicker = flatpickr(this.$refs.timePicker, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'H:i',
                    onChange: (selectedDates, dateStr, instance) => {
                        this.updateHiddenInput();
                    }
                });

                if (this.value) {
                    const [date, time] = this.value.split(' ');
                    this.datePicker.setDate(date);
                    this.timePicker.setDate(time);
                    this.$refs.timePicker.disabled = false;
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
