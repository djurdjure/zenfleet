/**
 * 🗓️ ZENFLEET DATEPICKER - Enterprise-Grade Date Selection Component
 * 
 * Provides a robust, reusable datepicker component using Flowbite Datepicker
 * with proper Alpine.data integration for clean Blade templates.
 * 
 * @version 2.0.0 (Flowbite Edition)
 * @author ZenFleet Development Team
 */

/**
 * Alpine.data component for ZenFleet Datepicker
 * Usage: x-data="zenfleetDatepicker" with data-* attributes for configuration
 * 
 * Required data attributes:
 * - data-value: Initial value in YYYY-MM-DD or DD/MM/YYYY format
 * 
 * Optional data attributes:
 * - data-min-date: Minimum selectable date (DD/MM/YYYY format)
 * - data-max-date: Maximum selectable date (DD/MM/YYYY format)
 * - data-format: Display format (default: dd/mm/yyyy)
 */
export function zenfleetDatepickerData() {
    return {
        // Reactive state
        displayValue: '',
        serverDate: null, // Server format (YYYY-MM-DD) for hidden input x-model binding
        picker: null,
        value: null, // Deprecated: use serverDate instead, kept for compatibility
        instance: null, // Flowbite instance

        /**
         * Initialize the datepicker component
         */
        init() {
            // Setup x-model binding support
            if (this.$el.hasAttribute('x-model') || this.$el.hasAttribute('wire:model') || this.$el.hasAttribute('wire:model.live')) {
                // Initialize from the bound model if available
                this.$watch('value', (val) => {
                    this.syncFromModel(val);
                });
            }

            // Read configuration from data attributes
            const container = this.$el;
            // Handle Laravel old() or wire:model initial values which might be YYYY-MM-DD
            const initialValue = container.dataset.value || this.value || '';

            // Initial sync
            if (initialValue) {
                this.syncFromModel(initialValue);
            }

            // Initialize Flowbite Datepicker
            this.$nextTick(() => {
                this.initializePicker(container);
            });
        },

        /**
         * Sync display value from model value (Server YYYY-MM-DD -> Display DD/MM/YYYY)
         */
        syncFromModel(val) {
            if (!val) {
                this.displayValue = '';
                if (this.instance) {
                    this.instance.setDate({ clear: true });
                }
                return;
            }

            // Check if format is YYYY-MM-DD (Server format)
            if (val.match(/^\d{4}-\d{2}-\d{2}$/)) {
                const [year, month, day] = val.split('-');
                // Flowbite/Standard European format
                this.displayValue = `${day}/${month}/${year}`;

                // Update internal picker state if it exists
                if (this.instance) {
                    this.instance.setDate(this.displayValue);
                }
            } else {
                // Assume it might be already formatted or invalid
                this.displayValue = val;
            }
        },

        /**
         * Initialize Flowbite Datepicker
         */
        initializePicker(container) {
            const el = this.$refs.displayInput;
            if (!el) return;

            // Ensure window.Datepicker is available (set in app.js)
            if (!window.Datepicker) {
                console.error('❌ Flowbite Datepicker not found globally. Check app.js imports.');
                return;
            }

            const minDate = container.dataset.minDate || null;
            const maxDate = container.dataset.maxDate || null;
            const format = container.dataset.format || 'dd/mm/yyyy';

            // Initialize Flowbite Datepicker
            this.instance = new window.Datepicker(el, {
                autohide: true,
                format: format,
                language: 'fr',
                orientation: 'bottom left',
                todayBtn: true,
                clearBtn: true,
                todayBtnMode: 1, // Select today on click
                minDate: minDate,
                maxDate: maxDate,
                // Ensure the picker respects our manually set value
                defaultViewDate: this.displayValue || new Date(),
            });

            // 🚫 FORCE LIGHT MODE: Remove 'dark' class from the generated picker
            // Flowbite Datepicker appends the picker to the body or container
            // We need to find it and strip the class
            if (this.instance && this.instance.pickerElement) {
                this.instance.pickerElement.classList.remove('dark');
                // Also force a specific light class if needed by our CSS
                this.instance.pickerElement.classList.add('light-mode-forced');
            }

            // Handle Date Selection Event
            el.addEventListener('changeDate', (e) => {
                // Flowbite event detail usually contains the date object
                const d = e.detail.date;

                if (d && !isNaN(d.getTime())) {
                    // Convert to YYYY-MM-DD for server/Livewire
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    const serverDate = `${year}-${month}-${day}`;

                    this.displayValue = this.instance.getDate('dd/mm/yyyy');
                    this.serverDate = serverDate;
                    this.value = serverDate; // Keep for backward compatibility

                    // IMPORTANT: Dispatch input event for Livewire wire:model
                    this.$dispatch('input', serverDate);
                } else {
                    // Cleared
                    this.displayValue = '';
                    this.serverDate = null;
                    this.value = null; // Keep for backward compatibility
                    this.$dispatch('input', null);
                }
            });

            // Handle manual input changes (blur) to validate strict format
            el.addEventListener('blur', () => {
                const val = el.value;
                if (!val) {
                    this.serverDate = null;
                    this.value = null; // Keep for backward compatibility
                    this.$dispatch('input', null);
                    return;
                }

                // If the user typed something valid, Flowbite usually handles it,
                // but we should sync just in case
                const date = this.instance.getDate();
                if (date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const serverDate = `${year}-${month}-${day}`;

                    if (this.serverDate !== serverDate) {
                        this.serverDate = serverDate;
                        this.value = serverDate; // Keep for backward compatibility
                        this.$dispatch('input', serverDate);
                    }
                }
            });

            // Set initial date if valid
            if (this.displayValue) {
                this.instance.setDate(this.displayValue);
            }
        },

        clear() {
            if (this.instance) {
                this.instance.setDate({ clear: true });
            }
            this.displayValue = '';
            this.serverDate = null;
            this.value = null; // Keep for backward compatibility
            this.$dispatch('input', null);
        }
    };
}

export default zenfleetDatepickerData;
