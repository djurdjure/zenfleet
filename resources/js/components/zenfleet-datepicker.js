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
        skipChangeEvent: false,
        skipBlurSync: false,

        bindPickerGuard(pickerEl) {
            if (!pickerEl || pickerEl._zenfleetGuardBound) return;

            const markInteraction = () => {
                this.skipBlurSync = true;
                window.setTimeout(() => {
                    this.skipBlurSync = false;
                }, 0);
            };

            pickerEl.addEventListener('mousedown', markInteraction, { capture: true });
            pickerEl.addEventListener('touchstart', markInteraction, { capture: true, passive: true });
            pickerEl._zenfleetGuardBound = true;
        },

        formatDisplayDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        },

        formatServerDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${year}-${month}-${day}`;
        },

        buildDate(year, month, day) {
            if (month < 1 || month > 12 || day < 1 || day > 31) return null;
            const date = new Date(year, month - 1, day);
            if (
                date.getFullYear() !== year ||
                date.getMonth() !== month - 1 ||
                date.getDate() !== day
            ) {
                return null;
            }
            return date;
        },

        parseInputDate(value) {
            const input = String(value || '').trim();
            if (!input) return null;

            const frMatch = input.match(/^(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{4})$/);
            if (frMatch) {
                const day = Number(frMatch[1]);
                const month = Number(frMatch[2]);
                const year = Number(frMatch[3]);
                return this.buildDate(year, month, day);
            }

            const isoMatch = input.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (isoMatch) {
                const year = Number(isoMatch[1]);
                const month = Number(isoMatch[2]);
                const day = Number(isoMatch[3]);
                return this.buildDate(year, month, day);
            }

            return null;
        },

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
                defaultViewDate: this.parseInputDate(this.displayValue) || new Date(),
            });

            // 🚫 FORCE LIGHT MODE: Remove 'dark' class from the generated picker
            // Flowbite Datepicker appends the picker to the body or container
            // We need to find it and strip the class
            const pickerEl = this.instance?.picker?.element;
            if (pickerEl) {
                pickerEl.classList.remove('dark');
                // Also force a specific light class if needed by our CSS
                pickerEl.classList.add('light-mode-forced');
                this.bindPickerGuard(pickerEl);
            }

            // Handle Date Selection Event
            el.addEventListener('changeDate', (e) => {
                if (this.skipChangeEvent) {
                    this.skipChangeEvent = false;
                    return;
                }
                // Flowbite event detail usually contains the date object
                const d = e.detail.date;

                if (d && !isNaN(d.getTime())) {
                    const serverDate = this.formatServerDate(d);

                    this.displayValue = this.formatDisplayDate(d);
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
                if (this.skipBlurSync) {
                    return;
                }
                const val = el.value;
                if (!val) {
                    this.serverDate = null;
                    this.value = null; // Keep for backward compatibility
                    this.$dispatch('input', null);
                    return;
                }

                const parsed = this.parseInputDate(val);
                if (!parsed) {
                    this.serverDate = null;
                    this.value = null; // Keep for backward compatibility
                    this.$dispatch('input', null);
                    return;
                }

                const serverDate = this.formatServerDate(parsed);
                if (this.serverDate !== serverDate) {
                    this.displayValue = this.formatDisplayDate(parsed);
                    this.serverDate = serverDate;
                    this.value = serverDate; // Keep for backward compatibility
                    this.$dispatch('input', serverDate);
                }

                if (this.instance) {
                    this.skipChangeEvent = true;
                    this.instance.setDate(parsed, { autohide: false });
                    setTimeout(() => {
                        this.skipChangeEvent = false;
                    }, 0);
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
