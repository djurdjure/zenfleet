// Form steppers for admin create/edit flows (vehicles/drivers).
// Kept in the bundle to work with Livewire navigate (no per-page script execution).

const readInitialStep = (root, fallback = 1) => {
    if (!root) return fallback;
    const input = root.querySelector('input[name="current_step"]');
    const value = input ? parseInt(input.value, 10) : fallback;
    return Number.isFinite(value) ? value : fallback;
};

const getServerErrors = () => window.zenfleetErrors || { hasErrors: false, keys: [] };
const getDriverErrors = () => window.zenfleetDriverErrors || null;

const escapeSelector = (value) => {
    if (window.CSS && typeof window.CSS.escape === 'function') {
        return window.CSS.escape(value);
    }
    return String(value).replace(/"/g, '\\"');
};

const resolveFieldUi = (root, fieldName) => {
    if (!root || !fieldName) {
        return { field: null, visual: null, messageContainer: null, errorKey: '' };
    }

    const normalizedName = fieldName.endsWith('[]') ? fieldName : fieldName.replace(/\[\]$/, '');
    const escaped = escapeSelector(normalizedName);

    let field = root.querySelector(`[name="${escaped}"]`);
    if (!field) {
        field = root.querySelector(`[name="${escaped}[]"]`);
    }
    if (!field) {
        field = root.querySelector(`[name^="${escaped}["]`);
    }

    if (!field) {
        return { field: null, visual: null, messageContainer: null, errorKey: normalizedName };
    }

    let visual = field;
    let messageContainer = field.parentElement;

    if (field.classList && field.classList.contains('slimselect-field')) {
        visual = field.parentElement ? field.parentElement.querySelector('.ss-main') : field;
        messageContainer = field.parentElement;
    } else if (field.type === 'hidden') {
        const displayInput = field.parentElement ? field.parentElement.querySelector('input[type="text"]') : null;
        visual = displayInput || field;
        messageContainer = visual.parentElement ? visual.parentElement.parentElement || visual.parentElement : field.parentElement;
    } else if (field.type === 'checkbox' && typeof field.name === 'string' && field.name.endsWith('[]')) {
        const alpineContainer = field.closest('[x-data]');
        const triggerButton = alpineContainer ? alpineContainer.querySelector('button[type="button"]') : null;
        visual = triggerButton || field;
        messageContainer = alpineContainer || field.parentElement;
    }

    return {
        field,
        visual,
        messageContainer,
        errorKey: normalizedName
    };
};

const setFieldErrorState = (root, fieldName, message) => {
    const ui = resolveFieldUi(root, fieldName);
    if (!ui.visual) return;

    ui.visual.classList.add('zenfleet-invalid');
    ui.visual.setAttribute('aria-invalid', 'true');

    if (ui.field && ui.field !== ui.visual) {
        ui.field.setAttribute('aria-invalid', 'true');
    }

    if (!message || !ui.messageContainer) return;

    let errorNode = ui.messageContainer.querySelector(`[data-unified-error-for="${ui.errorKey}"]`);
    if (!errorNode) {
        errorNode = document.createElement('p');
        errorNode.className = 'field-error mt-1.5 text-sm text-red-600 flex items-start gap-1.5';
        errorNode.dataset.unifiedErrorFor = ui.errorKey;
        errorNode.innerHTML = '<svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><span></span>';
        ui.messageContainer.appendChild(errorNode);
    }

    const text = errorNode.querySelector('span');
    if (text) {
        text.textContent = message;
    }
};

const clearFieldErrorState = (root, fieldName) => {
    const ui = resolveFieldUi(root, fieldName);
    if (!ui.visual) return;

    ui.visual.classList.remove('zenfleet-invalid');
    ui.visual.removeAttribute('aria-invalid');

    if (ui.field) {
        ui.field.removeAttribute('aria-invalid');
    }

    if (!ui.messageContainer) return;

    const errorNode = ui.messageContainer.querySelector(`[data-unified-error-for="${ui.errorKey}"]`);
    if (errorNode) {
        errorNode.remove();
    }
};

export function vehicleFormValidationCreate() {
    return {
        currentStep: 1,
        steps: [
            {
                label: 'Identification',
                icon: 'file-text',
                validated: false,
                touched: false,
                requiredFields: ['registration_plate', 'brand']
            },
            {
                label: 'Caractéristiques',
                icon: 'settings',
                validated: false,
                touched: false,
                requiredFields: ['fuel_type_id']
            },
            {
                label: 'Acquisition',
                icon: 'receipt',
                validated: false,
                touched: false,
                requiredFields: []
            }
        ],
        fieldErrors: {},
        touchedFields: {},
        stepValidationFields: {
            1: ['registration_plate', 'vin', 'brand', 'model'],
            2: ['vehicle_type_id', 'fuel_type_id', 'transmission_type_id'],
            3: ['acquisition_date', 'status_id']
        },

        init() {
            this.currentStep = readInitialStep(this.$root, 1);
            const errors = getServerErrors();
            if (errors.hasErrors) {
                this.markStepsWithErrors();
                errors.keys.forEach(field => {
                    this.touchedFields[field] = true;
                });
            }
        },

        markStepsWithErrors() {
            const fieldToStepMap = {
                registration_plate: 0,
                vin: 0,
                brand: 0,
                model: 0,
                color: 0,
                vehicle_type_id: 1,
                fuel_type_id: 1,
                transmission_type_id: 1,
                manufacturing_year: 1,
                seats: 1,
                power_hp: 1,
                engine_displacement_cc: 1,
                acquisition_date: 2,
                purchase_price: 2,
                current_value: 2,
                initial_mileage: 2,
                status_id: 2,
                notes: 2
            };

            const errors = getServerErrors();
            errors.keys.forEach(field => {
                const stepIndex = fieldToStepMap[field];
                if (stepIndex !== undefined) {
                    this.steps[stepIndex].touched = true;
                    this.steps[stepIndex].validated = false;
                }
            });
        },

        validateField(fieldName, value) {
            this.touchedFields[fieldName] = true;
            const message = this.getFieldErrorMessage(fieldName, value);

            if (message) {
                this.fieldErrors[fieldName] = message;
                setFieldErrorState(this.$root, fieldName, message);
                return false;
            }

            this.clearFieldError(fieldName);
            return true;
        },

        getFieldValue(fieldName) {
            const ui = resolveFieldUi(this.$root, fieldName);
            if (!ui.field) return '';
            if (ui.field.type === 'checkbox' && ui.field.name.endsWith('[]')) {
                const escaped = escapeSelector(fieldName.replace(/\[\]$/, ''));
                return Array.from(this.$root.querySelectorAll(`[name="${escaped}[]"]:checked`)).map((checkbox) => checkbox.value);
            }
            return ui.field.value;
        },

        getFieldErrorMessage(fieldName, value) {
            const trimmed = typeof value === 'string' ? value.trim() : value;

            if (fieldName === 'registration_plate') {
                if (!trimmed) return "L'immatriculation est obligatoire";
                if (trimmed.length > 50) return "L'immatriculation ne doit pas dépasser 50 caractères";
            }

            if (fieldName === 'brand') {
                if (!trimmed) return 'La marque est obligatoire';
                if (trimmed.length > 100) return 'La marque ne doit pas dépasser 100 caractères';
            }

            if (fieldName === 'model' && trimmed && trimmed.length > 100) {
                return 'Le modèle ne doit pas dépasser 100 caractères';
            }

            if (fieldName === 'vin' && trimmed && trimmed.length !== 17) {
                return 'Le VIN doit contenir exactement 17 caractères';
            }

            if (fieldName === 'fuel_type_id' && (!trimmed || trimmed === '0')) {
                return 'Le type de carburant est obligatoire';
            }

            return '';
        },

        validateCurrentStep() {
            const stepIndex = this.currentStep - 1;
            const step = this.steps[stepIndex];
            step.touched = true;

            let allValid = true;
            const fields = this.stepValidationFields[this.currentStep] || step.requiredFields;
            fields.forEach(fieldName => {
                const value = this.getFieldValue(fieldName);
                const isValid = this.validateField(fieldName, value);
                if (!isValid) {
                    allValid = false;
                }
            });

            step.validated = allValid;
            return allValid;
        },

        nextStep() {
            const isValid = this.validateCurrentStep();
            if (!isValid) {
                this.$dispatch('toast', {
                    type: 'error',
                    message: 'Veuillez remplir tous les champs obligatoires avant de continuer'
                });
                this.highlightInvalidFields();
                return;
            }

            if (this.currentStep < 3) {
                this.currentStep++;
            }
        },

        previousStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },

        highlightInvalidFields() {
            const stepIndex = this.currentStep - 1;
            const step = this.steps[stepIndex];

            const fields = this.stepValidationFields[this.currentStep] || step.requiredFields;
            fields.forEach(fieldName => {
                const value = this.getFieldValue(fieldName);
                const message = this.getFieldErrorMessage(fieldName, value);
                if (!message) {
                    return;
                }

                this.touchedFields[fieldName] = true;
                this.fieldErrors[fieldName] = message;
                setFieldErrorState(this.$root, fieldName, message);

                const ui = resolveFieldUi(this.$root, fieldName);
                if (ui.visual) {
                    ui.visual.classList.add('animate-shake');
                    setTimeout(() => {
                        ui.visual.classList.remove('animate-shake');
                    }, 500);
                }
            });
        },

        clearFieldError(fieldName) {
            delete this.fieldErrors[fieldName];
            clearFieldErrorState(this.$root, fieldName);
        },

        onSubmit(e) {
            let allValid = true;

            this.steps.forEach((step, index) => {
                const tempCurrent = this.currentStep;
                this.currentStep = index + 1;
                const isValid = this.validateCurrentStep();
                this.currentStep = tempCurrent;

                if (!isValid) {
                    allValid = false;
                }
            });

            if (!allValid) {
                e.preventDefault();
                const firstInvalidStep = this.steps.findIndex(s => s.touched && !s.validated);
                if (firstInvalidStep !== -1) {
                    this.currentStep = firstInvalidStep + 1;
                }

                this.$dispatch('toast', {
                    type: 'error',
                    message: 'Veuillez corriger les erreurs avant d\'enregistrer'
                });

                return false;
            }

            return true;
        }
    };
}

export function vehicleFormValidationEdit() {
    return {
        currentStep: 1,
        steps: [
            {
                label: 'Identification',
                icon: 'file-text',
                validated: false,
                touched: false,
                requiredFields: ['registration_plate', 'brand']
            },
            {
                label: 'Caractéristiques',
                icon: 'settings',
                validated: false,
                touched: false,
                requiredFields: ['fuel_type_id']
            },
            {
                label: 'Acquisition',
                icon: 'receipt',
                validated: false,
                touched: false,
                requiredFields: []
            }
        ],
        fieldErrors: {},
        touchedFields: {},
        stepValidationFields: {
            1: ['registration_plate', 'vin', 'brand', 'model'],
            2: ['vehicle_type_id', 'fuel_type_id', 'transmission_type_id'],
            3: ['acquisition_date', 'status_id']
        },

        init() {
            this.currentStep = readInitialStep(this.$root, 1);
            const errors = getServerErrors();
            if (errors.hasErrors) {
                this.markStepsWithErrors();
                errors.keys.forEach(field => {
                    this.touchedFields[field] = true;
                });
            }
        },

        markStepsWithErrors() {
            const fieldToStepMap = {
                registration_plate: 0,
                vin: 0,
                brand: 0,
                model: 0,
                color: 0,
                vehicle_type_id: 1,
                fuel_type_id: 1,
                transmission_type_id: 1,
                manufacturing_year: 1,
                seats: 1,
                power_hp: 1,
                engine_displacement_cc: 1,
                acquisition_date: 2,
                purchase_price: 2,
                current_value: 2,
                current_mileage: 2,
                status_id: 2,
                notes: 2
            };

            const errors = getServerErrors();
            errors.keys.forEach(field => {
                const stepIndex = fieldToStepMap[field];
                if (stepIndex !== undefined) {
                    this.steps[stepIndex].touched = true;
                    this.steps[stepIndex].validated = false;
                }
            });
        },

        validateField(fieldName, value) {
            this.touchedFields[fieldName] = true;
            const message = this.getFieldErrorMessage(fieldName, value);

            if (message) {
                this.fieldErrors[fieldName] = message;
                setFieldErrorState(this.$root, fieldName, message);
                return false;
            }

            this.clearFieldError(fieldName);
            return true;
        },

        getFieldValue(fieldName) {
            const ui = resolveFieldUi(this.$root, fieldName);
            if (!ui.field) return '';
            if (ui.field.type === 'checkbox' && ui.field.name.endsWith('[]')) {
                const escaped = escapeSelector(fieldName.replace(/\[\]$/, ''));
                return Array.from(this.$root.querySelectorAll(`[name="${escaped}[]"]:checked`)).map((checkbox) => checkbox.value);
            }
            return ui.field.value;
        },

        getFieldErrorMessage(fieldName, value) {
            const trimmed = typeof value === 'string' ? value.trim() : value;

            if (fieldName === 'registration_plate') {
                if (!trimmed) return "L'immatriculation est obligatoire";
                if (trimmed.length > 50) return "L'immatriculation ne doit pas dépasser 50 caractères";
            }

            if (fieldName === 'brand') {
                if (!trimmed) return 'La marque est obligatoire';
                if (trimmed.length > 100) return 'La marque ne doit pas dépasser 100 caractères';
            }

            if (fieldName === 'model' && trimmed && trimmed.length > 100) {
                return 'Le modèle ne doit pas dépasser 100 caractères';
            }

            if (fieldName === 'vin' && trimmed && trimmed.length !== 17) {
                return 'Le VIN doit contenir exactement 17 caractères';
            }

            if (fieldName === 'fuel_type_id' && (!trimmed || trimmed === '0')) {
                return 'Le type de carburant est obligatoire';
            }

            return '';
        },

        validateCurrentStep() {
            const stepIndex = this.currentStep - 1;
            const step = this.steps[stepIndex];
            step.touched = true;

            let allValid = true;
            const fields = this.stepValidationFields[this.currentStep] || step.requiredFields;
            fields.forEach(fieldName => {
                const value = this.getFieldValue(fieldName);
                const isValid = this.validateField(fieldName, value);
                if (!isValid) {
                    allValid = false;
                }
            });

            step.validated = allValid;
            return allValid;
        },

        nextStep() {
            const isValid = this.validateCurrentStep();
            if (!isValid) {
                this.$dispatch('toast', {
                    type: 'error',
                    message: 'Veuillez remplir tous les champs obligatoires avant de continuer'
                });
                this.highlightInvalidFields();
                return;
            }

            if (this.currentStep < 3) {
                this.currentStep++;
            }
        },

        previousStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },

        highlightInvalidFields() {
            const stepIndex = this.currentStep - 1;
            const step = this.steps[stepIndex];

            const fields = this.stepValidationFields[this.currentStep] || step.requiredFields;
            fields.forEach(fieldName => {
                const value = this.getFieldValue(fieldName);
                const message = this.getFieldErrorMessage(fieldName, value);
                if (!message) {
                    return;
                }

                this.touchedFields[fieldName] = true;
                this.fieldErrors[fieldName] = message;
                setFieldErrorState(this.$root, fieldName, message);

                const ui = resolveFieldUi(this.$root, fieldName);
                if (ui.visual) {
                    ui.visual.classList.add('animate-shake');
                    setTimeout(() => {
                        ui.visual.classList.remove('animate-shake');
                    }, 500);
                }
            });
        },

        clearFieldError(fieldName) {
            delete this.fieldErrors[fieldName];
            clearFieldErrorState(this.$root, fieldName);
        },

        onSubmit(e) {
            let allValid = true;

            this.steps.forEach((step, index) => {
                const tempCurrent = this.currentStep;
                this.currentStep = index + 1;
                const isValid = this.validateCurrentStep();
                this.currentStep = tempCurrent;

                if (!isValid) {
                    allValid = false;
                }
            });

            if (!allValid) {
                e.preventDefault();
                const firstInvalidStep = this.steps.findIndex(s => s.touched && !s.validated);
                if (firstInvalidStep !== -1) {
                    this.currentStep = firstInvalidStep + 1;
                }

                this.$dispatch('toast', {
                    type: 'error',
                    message: 'Veuillez corriger les erreurs avant d\'enregistrer'
                });

                return false;
            }

            return true;
        }
    };
}

export function driverFormValidationCreate() {
    return {
        currentStep: 1,
        photoPreview: null,
        fieldErrors: {
            first_name: '',
            last_name: '',
            birth_date: '',
            personal_phone: '',
            personal_email: '',
            blood_type: '',
            address: '',
            employee_number: '',
            recruitment_date: '',
            status_id: '',
            license_number: '',
            license_categories: '',
            license_issue_date: '',
            license_expiry_date: ''
        },
        touchedFields: {
            first_name: false,
            last_name: false,
            birth_date: false,
            personal_phone: false,
            personal_email: false,
            status_id: false,
            employee_number: false,
            license_number: false,
            license_categories: false
        },
        formValid: false,
        licenseExpiryManual: false,

        init() {
            this.currentStep = readInitialStep(this.$root, 1);
            const errors = getDriverErrors();
            if (errors && Object.keys(errors).length > 0) {
                this.handleValidationErrors(errors);
            }
            this.setupLicenseExpiryAuto();
        },

        setupLicenseExpiryAuto() {
            const issueInput = this.$root.querySelector('[name=\"license_issue_date\"]');
            const expiryInput = this.$root.querySelector('[name=\"license_expiry_date\"]');
            if (!issueInput || !expiryInput) return;

            const issueDisplayInput = issueInput.parentElement?.querySelector('input[type=\"text\"]');
            const expiryDisplayInput = expiryInput.parentElement?.querySelector('input[type=\"text\"]');
            const autoExpiryDate = this.computeLicenseExpiry(issueInput.value);
            if (expiryInput.value) {
                if (autoExpiryDate) {
                    const autoServer = this.formatServerDate(autoExpiryDate);
                    this.licenseExpiryManual = expiryInput.value !== autoServer;
                } else {
                    this.licenseExpiryManual = true;
                }
            } else {
                this.licenseExpiryManual = false;
                if (autoExpiryDate) {
                    this.setDatepickerValue(expiryInput, autoExpiryDate);
                }
            }

            const updateExpiry = () => {
                if (this.licenseExpiryManual) return;
                const expiryDate = this.computeLicenseExpiry(issueInput.value);
                if (!expiryDate) return;
                this.setDatepickerValue(expiryInput, expiryDate);
                this.validateField('license_expiry_date', expiryInput.value);
            };

            [issueInput, issueDisplayInput].forEach((element) => {
                if (!element) return;
                element.addEventListener('change', updateExpiry);
                element.addEventListener('blur', updateExpiry);
            });

            [expiryInput, expiryDisplayInput].forEach((element) => {
                if (!element) return;
                element.addEventListener('input', () => {
                    if (!expiryInput.value) {
                        this.licenseExpiryManual = false;
                        updateExpiry();
                    } else {
                        this.licenseExpiryManual = true;
                    }
                });
            });
        },

        computeLicenseExpiry(issueValue) {
            const issueDate = this.parseDateValue(issueValue);
            if (!issueDate) return null;
            const expiryDate = new Date(issueDate);
            expiryDate.setFullYear(expiryDate.getFullYear() + 10);
            expiryDate.setDate(expiryDate.getDate() - 1);
            return expiryDate;
        },

        parseDateValue(value) {
            if (!value) return null;
            const isoMatch = String(value).match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (isoMatch) {
                const year = Number(isoMatch[1]);
                const month = Number(isoMatch[2]);
                const day = Number(isoMatch[3]);
                return new Date(year, month - 1, day);
            }

            const frMatch = String(value).match(/^(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{4})$/);
            if (frMatch) {
                const day = Number(frMatch[1]);
                const month = Number(frMatch[2]);
                const year = Number(frMatch[3]);
                return new Date(year, month - 1, day);
            }

            return null;
        },

        formatServerDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${year}-${month}-${day}`;
        },

        formatDisplayDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        },

        setDatepickerValue(hiddenInput, date) {
            if (!hiddenInput || !date) return;
            const serverValue = this.formatServerDate(date);
            const displayValue = this.formatDisplayDate(date);

            hiddenInput.value = serverValue;
            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));

            const displayInput = hiddenInput.parentElement?.querySelector('input[type=\"text\"]');
            if (displayInput) {
                displayInput.value = displayValue;
                displayInput.dispatchEvent(new Event('input', { bubbles: true }));
                displayInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        },

        updatePhotoPreview(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    this.photoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        validateField(fieldName, value) {
            this.touchedFields[fieldName] = true;

            switch (fieldName) {
                case 'first_name':
                    if (!value || value.trim() === '') {
                        this.fieldErrors.first_name = 'Le prénom est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.first_name);
                    } else if (value.trim().length < 2) {
                        this.fieldErrors.first_name = 'Le prénom doit contenir au moins 2 caractères';
                        this.showFieldError(fieldName, this.fieldErrors.first_name);
                    } else if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(value.trim())) {
                        this.fieldErrors.first_name = 'Le prénom ne doit contenir que des lettres';
                        this.showFieldError(fieldName, this.fieldErrors.first_name);
                    } else {
                        this.fieldErrors.first_name = '';
                        this.removeFieldError(fieldName);
                    }
                    break;

                case 'last_name':
                    if (!value || value.trim() === '') {
                        this.fieldErrors.last_name = 'Le nom est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.last_name);
                    } else if (value.trim().length < 2) {
                        this.fieldErrors.last_name = 'Le nom doit contenir au moins 2 caractères';
                        this.showFieldError(fieldName, this.fieldErrors.last_name);
                    } else if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(value.trim())) {
                        this.fieldErrors.last_name = 'Le nom ne doit contenir que des lettres';
                        this.showFieldError(fieldName, this.fieldErrors.last_name);
                    } else {
                        this.fieldErrors.last_name = '';
                        this.removeFieldError(fieldName);
                    }
                    break;

                case 'birth_date':
                    if (!value) {
                        this.fieldErrors.birth_date = 'La date de naissance est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.birth_date);
                    } else {
                        const birthDate = new Date(value);
                        const today = new Date();
                        const age = today.getFullYear() - birthDate.getFullYear();
                        if (age < 18) {
                            this.fieldErrors.birth_date = 'Le chauffeur doit être majeur (18 ans minimum)';
                            this.showFieldError(fieldName, this.fieldErrors.birth_date);
                        } else if (age > 70) {
                            this.fieldErrors.birth_date = 'L\'âge maximum est de 70 ans';
                            this.showFieldError(fieldName, this.fieldErrors.birth_date);
                        } else {
                            this.fieldErrors.birth_date = '';
                            this.removeFieldError(fieldName);
                        }
                    }
                    break;

                case 'personal_phone':
                    if (!value || value.trim() === '') {
                        this.fieldErrors.personal_phone = 'Le téléphone est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.personal_phone);
                    } else if (!/^(0[567])[0-9]{8}$/.test(value.replace(/\s/g, ''))) {
                        this.fieldErrors.personal_phone = 'Format invalide (ex: 0555123456)';
                        this.showFieldError(fieldName, this.fieldErrors.personal_phone);
                    } else {
                        this.fieldErrors.personal_phone = '';
                        this.removeFieldError(fieldName);
                    }
                    break;

                case 'personal_email':
                    if (value && value.trim() !== '') {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) {
                            this.fieldErrors.personal_email = 'Format email invalide';
                            this.showFieldError(fieldName, this.fieldErrors.personal_email);
                        } else {
                            this.fieldErrors.personal_email = '';
                            this.removeFieldError(fieldName);
                        }
                    } else {
                        this.fieldErrors.personal_email = '';
                        this.removeFieldError(fieldName);
                    }
                    break;

                case 'employee_number':
                    if (!value || value.trim() === '') {
                        this.fieldErrors.employee_number = 'Le matricule est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.employee_number);
                    } else if (value.trim().length < 3) {
                        this.fieldErrors.employee_number = 'Le matricule doit contenir au moins 3 caractères';
                        this.showFieldError(fieldName, this.fieldErrors.employee_number);
                    } else {
                        this.fieldErrors.employee_number = '';
                        this.removeFieldError(fieldName);
                    }
                    break;

                case 'recruitment_date':
                    if (!value) {
                        this.fieldErrors.recruitment_date = 'La date de recrutement est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.recruitment_date);
                    } else {
                        const recruitDate = new Date(value);
                        const today = new Date();
                        if (recruitDate > today) {
                            this.fieldErrors.recruitment_date = 'La date ne peut pas être dans le futur';
                            this.showFieldError(fieldName, this.fieldErrors.recruitment_date);
                        } else {
                            this.fieldErrors.recruitment_date = '';
                            this.removeFieldError(fieldName);
                        }
                    }
                    break;

                case 'status_id':
                    if (!value || value === '' || value === '0') {
                        this.fieldErrors.status_id = 'Le statut du chauffeur est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.status_id);
                    } else {
                        this.fieldErrors.status_id = '';
                        this.removeFieldError(fieldName);
                    }
                    break;

                case 'license_number':
                    if (!value || value.trim() === '') {
                        this.fieldErrors.license_number = 'Le numéro de permis est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.license_number);
                    } else if (value.trim().length < 5) {
                        this.fieldErrors.license_number = 'Le numéro de permis doit contenir au moins 5 caractères';
                        this.showFieldError(fieldName, this.fieldErrors.license_number);
                    } else {
                        this.fieldErrors.license_number = '';
                        this.removeFieldError(fieldName);
                    }
                    break;

                case 'license_categories':
                    if (!value || value.length === 0) {
                        this.fieldErrors.license_categories = 'Au moins une catégorie de permis est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.license_categories);
                    } else {
                        this.fieldErrors.license_categories = '';
                        this.removeFieldError(fieldName);
                    }
                    break;

                case 'license_issue_date':
                    if (!value) {
                        this.fieldErrors.license_issue_date = 'La date de délivrance est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.license_issue_date);
                    } else {
                        this.fieldErrors.license_issue_date = '';
                        this.removeFieldError(fieldName);
                    }
                    break;

                case 'license_expiry_date':
                    if (!value) {
                        this.fieldErrors.license_expiry_date = 'La date d\'expiration est obligatoire';
                        this.showFieldError(fieldName, this.fieldErrors.license_expiry_date);
                    } else {
                        const expiryDate = new Date(value);
                        const today = new Date();
                        if (expiryDate < today) {
                            this.fieldErrors.license_expiry_date = 'Le permis est expiré';
                            this.showFieldError(fieldName, this.fieldErrors.license_expiry_date);
                        } else {
                            this.fieldErrors.license_expiry_date = '';
                            this.removeFieldError(fieldName);
                        }
                    }
                    break;
            }

            this.updateFormValidity();
        },

        showFieldError(fieldName, message) {
            setFieldErrorState(this.$root, fieldName, message);
        },

        removeFieldError(fieldName) {
            clearFieldErrorState(this.$root, fieldName);
        },

        updateFormValidity() {
            let hasErrors = false;
            for (const key in this.fieldErrors) {
                if (this.fieldErrors[key] !== '') {
                    hasErrors = true;
                    break;
                }
            }
            this.formValid = !hasErrors;
        },

        hasError(fieldName) {
            return this.touchedFields[fieldName] && this.fieldErrors[fieldName] !== '';
        },

        validateStep(step) {
            let isValid = true;
            let fieldsToValidate = [];

            switch (step) {
                case 1:
                    fieldsToValidate = ['first_name', 'last_name', 'birth_date', 'personal_phone'];
                    const emailField = this.$root.querySelector('[name="personal_email"]');
                    if (emailField && emailField.value) {
                        fieldsToValidate.push('personal_email');
                    }
                    break;
                case 2:
                    fieldsToValidate = ['employee_number', 'recruitment_date', 'status_id'];
                    break;
                case 3:
                    fieldsToValidate = [
                        'license_number',
                        'license_categories',
                        'license_issue_date',
                        'license_expiry_date'
                    ];
                    break;
                case 4:
                    break;
            }

            fieldsToValidate.forEach(fieldName => {
                if (fieldName === 'license_categories') {
                    const selectedCategories = Array.from(
                        this.$root.querySelectorAll('[name="license_categories[]"]:checked')
                    ).map((checkbox) => checkbox.value);

                    this.validateField(fieldName, selectedCategories);
                    if (this.fieldErrors[fieldName]) {
                        isValid = false;
                    }
                    return;
                }

                const ui = resolveFieldUi(this.$root, fieldName);
                const value = ui.field ? ui.field.value : '';

                this.validateField(fieldName, value);
                if (this.fieldErrors[fieldName]) {
                    isValid = false;
                }
            });

            if (!isValid) {
                this.showStepErrors(step, fieldsToValidate);
            }

            return isValid;
        },

        showStepErrors(step, fields) {
            let errorMessages = [];

            const explicitLabels = {
                first_name: 'Prénom',
                last_name: 'Nom',
                birth_date: 'Date de naissance',
                personal_phone: 'Téléphone personnel',
                personal_email: 'Email personnel',
                employee_number: 'Matricule',
                recruitment_date: 'Date de recrutement',
                status_id: 'Statut du chauffeur',
                license_number: 'Numéro de permis',
                license_categories: 'Catégories de permis',
                license_issue_date: 'Date de délivrance',
                license_expiry_date: "Date d'expiration"
            };

            fields.forEach(fieldName => {
                if (this.fieldErrors[fieldName]) {
                    const ui = resolveFieldUi(this.$root, fieldName);
                    const labelFromDom = ui.field ? ui.field.closest('div')?.querySelector('label')?.textContent : null;
                    const label = explicitLabels[fieldName] || labelFromDom || fieldName;
                    errorMessages.push(`• ${label}: ${this.fieldErrors[fieldName]}`);
                }
            });

            if (errorMessages.length > 0) {
                let alertDiv = document.querySelector('.step-validation-alert');
                if (!alertDiv) {
                    alertDiv = document.createElement('div');
                    alertDiv.className = 'step-validation-alert fixed top-4 right-4 z-50 max-w-md bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg';
                    alertDiv.innerHTML = `
 <div class="flex items-start gap-3">
 <svg class="w-5 h-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
 </svg>
 <div class="flex-1">
 <h3 class="text-sm font-semibold text-red-800">Veuillez corriger les erreurs suivantes :</h3>
 <div class="mt-2 text-sm text-red-700 error-list"></div>
 </div>
 <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
 <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
 </svg>
 </button>
 </div>
 `;
                    document.body.appendChild(alertDiv);
                }
                alertDiv.querySelector('.error-list').innerHTML = errorMessages.join('<br>');

                setTimeout(() => {
                    if (alertDiv) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        },

        nextStep() {
            if (this.validateStep(this.currentStep)) {
                if (this.currentStep < 4) {
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        onSubmit(event) {
            let firstInvalidStep = null;

            for (let step = 1; step <= 4; step++) {
                if (!this.validateStep(step) && firstInvalidStep === null) {
                    firstInvalidStep = step;
                }
            }

            if (firstInvalidStep !== null) {
                event.preventDefault();
                this.currentStep = firstInvalidStep;
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return false;
            }

            this.convertDatesBeforeSubmit(event);
            return true;
        },

        convertDatesBeforeSubmit(event) {
            const form = event.target;
            const dateFields = [
                'birth_date',
                'recruitment_date',
                'contract_end_date',
                'license_issue_date',
                'license_expiry_date'
            ];

            dateFields.forEach(fieldName => {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input && input.value) {
                    const convertedDate = this.convertDateFormat(input.value);
                    if (convertedDate) {
                        input.value = convertedDate;
                    }
                }
            });
        },

        convertDateFormat(dateString) {
            if (!dateString) return null;
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) return dateString;

            const match = dateString.match(/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/);
            if (match) {
                const day = match[1].padStart(2, '0');
                const month = match[2].padStart(2, '0');
                const year = match[3];
                return `${year}-${month}-${day}`;
            }
            return null;
        },

        handleValidationErrors(errors) {
            Object.keys(errors).forEach(field => {
                this.fieldErrors[field] = errors[field][0];
                this.touchedFields[field] = true;
            });

            const fieldToStepMap = {
                first_name: 1,
                last_name: 1,
                birth_date: 1,
                personal_phone: 1,
                address: 1,
                blood_type: 1,
                personal_email: 1,
                photo: 1,
                employee_number: 2,
                recruitment_date: 2,
                contract_end_date: 2,
                status_id: 2,
                notes: 2,
                license_number: 3,
                license_categories: 3,
                license_issue_date: 3,
                license_expiry_date: 3,
                license_authority: 3,
                license_verified: 3,
                user_id: 4,
                emergency_contact_name: 4,
                emergency_contact_phone: 4,
                emergency_contact_relationship: 4
            };

            const errorFields = Object.keys(errors);
            let firstErrorStep = null;

            for (const field of errorFields) {
                if (fieldToStepMap[field]) {
                    if (firstErrorStep === null || fieldToStepMap[field] < firstErrorStep) {
                        firstErrorStep = fieldToStepMap[field];
                    }
                }
            }

            if (firstErrorStep) {
                this.currentStep = firstErrorStep;
            }
        }
    };
}

export function driverFormValidationEdit() {
    return {
        currentStep: 1,
        photoPreview: null,
        fieldErrors: {},
        touchedFields: {},
        licenseExpiryManual: false,

        init() {
            this.currentStep = readInitialStep(this.$root, 1);
            this.fieldErrors = {
                first_name: '',
                last_name: '',
                email: '',
                personal_phone: '',
                birth_date: '',
                license_number: '',
                license_category: '',
                license_issue_date: '',
                license_expiry_date: '',
                recruitment_date: '',
                contract_end_date: '',
                user_id: ''
            };

            this.touchedFields = {
                first_name: false,
                last_name: false,
                email: false,
                personal_phone: false,
                birth_date: false,
                license_number: false,
                license_categories: false,
                license_issue_date: false,
                license_expiry_date: false,
                recruitment_date: false,
                contract_end_date: false,
                user_id: false
            };

            const errors = getDriverErrors();
            if (errors && Object.keys(errors).length > 0) {
                this.handleValidationErrors(errors);
            }
            this.setupLicenseExpiryAuto();
        },

        setupLicenseExpiryAuto() {
            const issueInput = this.$root.querySelector('[name="license_issue_date"]');
            const expiryInput = this.$root.querySelector('[name="license_expiry_date"]');
            if (!issueInput || !expiryInput) return;

            const issueDisplayInput = issueInput.parentElement?.querySelector('input[type="text"]');
            const expiryDisplayInput = expiryInput.parentElement?.querySelector('input[type="text"]');
            const autoExpiryDate = this.computeLicenseExpiry(issueInput.value);
            if (expiryInput.value) {
                if (autoExpiryDate) {
                    const autoServer = this.formatServerDate(autoExpiryDate);
                    this.licenseExpiryManual = expiryInput.value !== autoServer;
                } else {
                    this.licenseExpiryManual = true;
                }
            } else {
                this.licenseExpiryManual = false;
                if (autoExpiryDate) {
                    this.setDatepickerValue(expiryInput, autoExpiryDate);
                }
            }

            const updateExpiry = () => {
                if (this.licenseExpiryManual) return;
                const expiryDate = this.computeLicenseExpiry(issueInput.value);
                if (!expiryDate) return;
                this.setDatepickerValue(expiryInput, expiryDate);
                this.validateField('license_expiry_date', expiryInput.value);
            };

            [issueInput, issueDisplayInput].forEach((element) => {
                if (!element) return;
                element.addEventListener('change', updateExpiry);
                element.addEventListener('blur', updateExpiry);
            });

            [expiryInput, expiryDisplayInput].forEach((element) => {
                if (!element) return;
                element.addEventListener('input', () => {
                    if (!expiryInput.value) {
                        this.licenseExpiryManual = false;
                        updateExpiry();
                    } else {
                        this.licenseExpiryManual = true;
                    }
                });
            });
        },

        computeLicenseExpiry(issueValue) {
            const issueDate = this.parseDateValue(issueValue);
            if (!issueDate) return null;
            const expiryDate = new Date(issueDate);
            expiryDate.setFullYear(expiryDate.getFullYear() + 10);
            expiryDate.setDate(expiryDate.getDate() - 1);
            return expiryDate;
        },

        parseDateValue(value) {
            if (!value) return null;
            const isoMatch = String(value).match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (isoMatch) {
                const year = Number(isoMatch[1]);
                const month = Number(isoMatch[2]);
                const day = Number(isoMatch[3]);
                return new Date(year, month - 1, day);
            }

            const frMatch = String(value).match(/^(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{4})$/);
            if (frMatch) {
                const day = Number(frMatch[1]);
                const month = Number(frMatch[2]);
                const year = Number(frMatch[3]);
                return new Date(year, month - 1, day);
            }

            return null;
        },

        formatServerDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${year}-${month}-${day}`;
        },

        formatDisplayDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        },

        setDatepickerValue(hiddenInput, date) {
            if (!hiddenInput || !date) return;
            const serverValue = this.formatServerDate(date);
            const displayValue = this.formatDisplayDate(date);

            hiddenInput.value = serverValue;
            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));

            const displayInput = hiddenInput.parentElement?.querySelector('input[type="text"]');
            if (displayInput) {
                displayInput.value = displayValue;
                displayInput.dispatchEvent(new Event('input', { bubbles: true }));
                displayInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        },

        updatePhotoPreview(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    this.photoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        validateField(fieldName, value = null) {
            this.touchedFields[fieldName] = true;
            if (fieldName === 'license_categories') {
                if (!value || value.length === 0) {
                    this.fieldErrors.license_categories = 'Au moins une catégorie de permis est obligatoire';
                } else {
                    this.fieldErrors.license_categories = '';
                }
            } else if (value === '') {
                this.fieldErrors[fieldName] = '';
            }
        },

        nextStep() {
            if (this.currentStep < 4) {
                this.currentStep++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        onSubmit(event) {
            this.convertDatesBeforeSubmit(event);
        },

        convertDatesBeforeSubmit(event) {
            const form = event.target;
            const dateFields = [
                'birth_date',
                'recruitment_date',
                'contract_end_date',
                'license_issue_date',
                'license_expiry_date'
            ];

            dateFields.forEach(fieldName => {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input && input.value) {
                    const convertedDate = this.convertDateFormat(input.value);
                    if (convertedDate) {
                        input.value = convertedDate;
                    }
                }
            });
        },

        convertDateFormat(dateString) {
            if (!dateString) return null;
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) return dateString;

            const match = dateString.match(/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/);
            if (match) {
                const day = match[1].padStart(2, '0');
                const month = match[2].padStart(2, '0');
                const year = match[3];
                return `${year}-${month}-${day}`;
            }
            return null;
        },

        handleValidationErrors(errors) {
            Object.keys(errors).forEach(field => {
                this.fieldErrors[field] = errors[field][0];
                this.touchedFields[field] = true;
            });

            const fieldToStepMap = {
                first_name: 1,
                last_name: 1,
                birth_date: 1,
                personal_phone: 1,
                address: 1,
                blood_type: 1,
                personal_email: 1,
                photo: 1,
                employee_number: 2,
                recruitment_date: 2,
                contract_end_date: 2,
                status_id: 2,
                notes: 2,
                license_number: 3,
                license_categories: 3,
                license_issue_date: 3,
                license_expiry_date: 3,
                license_authority: 3,
                license_verified: 3,
                user_id: 4,
                emergency_contact_name: 4,
                emergency_contact_phone: 4,
                emergency_contact_relationship: 4
            };

            const errorFields = Object.keys(errors);
            let firstErrorStep = null;

            for (const field of errorFields) {
                if (fieldToStepMap[field]) {
                    if (firstErrorStep === null || fieldToStepMap[field] < firstErrorStep) {
                        firstErrorStep = fieldToStepMap[field];
                    }
                }
            }

            if (firstErrorStep) {
                this.currentStep = firstErrorStep;
            }
        }
    };
}
