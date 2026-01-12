/**
 * 🗓️ ZENFLEET DATEPICKER - Enterprise-Grade Date Selection Component
 * 
 * Provides a robust, reusable datepicker component using Flowbite Datepicker
 * with proper Alpine.data integration for clean Blade templates.
 * 
 * @version 1.0.0
 * @author ZenFleet Development Team
 */

/**
 * Alpine.data component for ZenFleet Datepicker
 * Usage: x-data="zenfleetDatepicker" with data-* attributes for configuration
 * 
 * Required data attributes:
 * - data-value: Initial value in YYYY-MM-DD or DD/MM/YYYY format
 * - data-display-value: Initial display value in DD/MM/YYYY format
 * 
 * Optional data attributes:
 * - data-min-date: Minimum selectable date (DD/MM/YYYY format)
 * - data-max-date: Maximum selectable date (DD/MM/YYYY format)
 */
export function zenfleetDatepickerData() {
    return {
        // Reactive state
        serverDate: '',
        displayValue: '',
        picker: null,

        /**
         * Initialize the datepicker component
         */
        init() {
            // Read configuration from data attributes
            const container = this.$el;
            this.serverDate = container.dataset.value || '';
            this.displayValue = container.dataset.displayValue || '';

            this.$nextTick(() => {
                this.initializePicker(container);
            });
        },

        /**
         * Initialize Flowbite Datepicker with enterprise-grade configuration
         * @param {HTMLElement} container - The component container element
         */
        initializePicker(container) {
            const el = this.$refs.displayInput;
            if (!el) {
                console.error('❌ ZenFleet Datepicker: displayInput ref not found');
                return;
            }

            const component = this;
            let isOpening = false;

            // Check if Flowbite Datepicker is available
            if (typeof window.Datepicker === 'undefined') {
                console.error('❌ ZenFleet: Flowbite Datepicker not loaded');
                return;
            }

            // Build datepicker options
            const options = {
                language: 'fr',
                format: 'dd/mm/yyyy',
                autohide: true,
                todayBtn: true,
                todayBtnMode: 1,
                clearBtn: true,
                weekStart: 1,
                orientation: 'bottom left',
            };

            // Add minDate if specified
            const minDate = container.dataset.minDate;
            if (minDate) {
                options.minDate = minDate;
            }

            // Add maxDate if specified
            const maxDate = container.dataset.maxDate;
            if (maxDate) {
                options.maxDate = maxDate;
            }

            // Initialize Flowbite Datepicker
            this.picker = new window.Datepicker(el, options);

            // ✅ ENTERPRISE-GRADE: Force hide function
            const forceHidePicker = () => {
                if (!component.picker || isOpening) return;
                const pickerEl = component.picker.picker?.element;
                if (pickerEl) {
                    pickerEl.style.display = 'none';
                    pickerEl.classList.remove('active', 'block');
                    pickerEl.classList.add('hidden');
                    if (component.picker.picker) {
                        component.picker.picker.active = false;
                    }
                }
            };

            // ✅ ENTERPRISE-GRADE: Force show function (reset display)
            const ensurePickerVisible = () => {
                const pickerEl = component.picker.picker?.element;
                if (pickerEl) {
                    pickerEl.style.display = '';
                    pickerEl.classList.remove('hidden');
                }
            };

            // Set initial date if value exists
            if (this.displayValue) {
                this.picker.setDate(this.displayValue);
                el.value = this.displayValue;
            }

            // ✅ Listen for show event to reset display and set flag
            el.addEventListener('show', () => {
                isOpening = true;
                ensurePickerVisible();
                setTimeout(() => {
                    isOpening = false;
                }, 100);
            });

            // Handle date change - force close on selection
            el.addEventListener('changeDate', (e) => {
                if (e.detail.date) {
                    const d = e.detail.date;
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');

                    component.serverDate = `${year}-${month}-${day}`;
                    component.displayValue = `${day}/${month}/${year}`;
                    component.$dispatch('input', component.serverDate);

                    // Force hide after selection
                    setTimeout(forceHidePicker, 10);
                } else {
                    component.serverDate = '';
                    component.displayValue = '';
                    component.$dispatch('input', '');
                }
            });

            // ✅ ENTERPRISE-GRADE: Click outside handler
            const clickOutsideHandler = (e) => {
                if (!component.picker || isOpening) return;
                const pickerEl = component.picker.picker?.element;
                if (!pickerEl) return;

                // Only check active class for visibility (more reliable)
                const isVisible = pickerEl.classList.contains('active');
                if (!isVisible) return;

                // Check if click is outside both input and picker
                if (!pickerEl.contains(e.target) && !el.contains(e.target)) {
                    forceHidePicker();
                }
            };

            document.addEventListener('mousedown', clickOutsideHandler);

            // Handle manual clear
            el.addEventListener('input', () => {
                if (!el.value.trim()) {
                    component.serverDate = '';
                    component.displayValue = '';
                    component.$dispatch('input', '');
                }
            });

            // ✅ Cleanup on destroy
            this.$cleanup = () => {
                document.removeEventListener('mousedown', clickOutsideHandler);
                if (this.picker && typeof this.picker.destroy === 'function') {
                    this.picker.destroy();
                }
            };
        },

        /**
         * Programmatically set the date
         * @param {string} dateStr - Date in YYYY-MM-DD format
         */
        setDate(dateStr) {
            if (!dateStr) {
                this.serverDate = '';
                this.displayValue = '';
                if (this.picker) {
                    this.picker.setDate({ clear: true });
                }
                return;
            }

            // Parse YYYY-MM-DD format
            const match = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (match) {
                const [, year, month, day] = match;
                this.serverDate = dateStr;
                this.displayValue = `${day}/${month}/${year}`;

                if (this.picker) {
                    this.picker.setDate(this.displayValue);
                }
            }
        },

        /**
         * Clear the datepicker
         */
        clear() {
            this.serverDate = '';
            this.displayValue = '';
            if (this.picker) {
                this.picker.setDate({ clear: true });
            }
            this.$dispatch('input', '');
        }
    };
}

export default zenfleetDatepickerData;
