/**
 * 🚀 ZENFLEET ADMIN - Interface d'administration ultra-optimisée
 * Version: 2.1 Admin-Pro
 * Spécialisé pour l'interface d'administration
 */

// ✅ CORRECTION: Import CSS admin en premier
import '../../css/admin/app.css';

// ✅ ENTERPRISE: Local CSS imports
// ✅ ENTERPRISE: Local CSS imports
import 'slim-select/styles'; // Correct alias from package.json
import 'flatpickr/dist/flatpickr.min.css';
// ✅ Font import moved to CSS to avoid PostCSS warning

// ✅ ENTERPRISE: Import SlimSelect globally
import SlimSelect from 'slim-select';

window.SlimSelect = SlimSelect;

// ✅ ENTERPRISE: Import Flowbite Datepicker globally
import Datepicker from 'flowbite-datepicker/Datepicker';
import fr from './locales/fr.js'; // Use local manual import
Object.assign(Datepicker.locales, { fr }); // Register French Locale

window.Datepicker = Datepicker;
console.log('📅 Flowbite Datepicker configured globally:', !!window.Datepicker);

// ✅ ENTERPRISE: Import Iconify runtime locally
import Iconify from '@iconify/iconify';
window.Iconify = Iconify;

// ✅ CRITIQUE: Import Livewire 3 pour wire:click et composants Livewire
import { Livewire, Alpine } from '../../../vendor/livewire/livewire/dist/livewire.esm.js';

// Import des dépendances système (ESM)
import axios from 'axios';

// ✅ OPTIMISATION: Imports sélectifs pour l'admin
// ✅ OPTIMISATION: Imports sélectifs pour l'admin
import flatpickr from 'flatpickr';
import { French } from 'flatpickr/dist/l10n/fr.js';

// Configuration sécurisée des objets globaux admin
const initializeAdminGlobals = () => {
    window.axios = axios;
    // window.TomSelect est chargé via CDN
    window.flatpickr = flatpickr;
    // Configurer la locale française par défaut globalement
    flatpickr.localize(French);
};

// ✅ NOUVELLE ARCHITECTURE: Classe ZenFleetAdmin moderne
class ZenFleetAdmin {
    constructor() {
        this.version = '2.1';
        this.user = null;
        this.notifications = [];
        this.components = new Map();

        this.init();
    }

    async init() {
        console.log(`🚀 ZenFleet Admin v${this.version} initialized`);

        // Initialisation séquentielle
        this.setupCSRF();
        this.setupAxiosInterceptors();
        await this.loadUserData();
        this.initializeComponents();
        this.setupEventListeners();

        console.log('✅ ZenFleet Admin ready');
    }

    // ✅ CORRECTION: Configuration CSRF moderne (ESM)
    setupCSRF() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        } else {
            console.warn('⚠️ CSRF token not found');
        }
    }

    // ✅ NOUVEAU: Intercepteurs Axios pour l'admin
    setupAxiosInterceptors() {
        // Request interceptor
        axios.interceptors.request.use(
            config => {
                // Afficher un loader pour les requêtes longues
                if (config.showLoader !== false) {
                    this.showLoader();
                }
                return config;
            },
            error => Promise.reject(error)
        );

        // Response interceptor
        axios.interceptors.response.use(
            response => {
                this.hideLoader();
                return response;
            },
            error => {
                this.hideLoader();
                this.handleAxiosError(error);
                return Promise.reject(error);
            }
        );
    }

    // Gestion des erreurs Axios
    handleAxiosError(error) {
        const status = error.response?.status;
        let message = 'Une erreur est survenue';

        switch (status) {
            case 401:
                message = 'Session expirée. Veuillez vous reconnecter.';
                setTimeout(() => window.location.href = '/login', 2000);
                break;
            case 403:
                message = 'Accès refusé';
                break;
            case 404:
                message = 'Ressource non trouvée';
                break;
            case 422:
                message = 'Données invalides';
                this.handleValidationErrors(error.response.data);
                return;
            case 500:
                message = 'Erreur serveur';
                break;
        }

        this.notify(message, 'error');
    }

    // Gestion des erreurs de validation
    handleValidationErrors(data) {
        if (data.errors) {
            Object.keys(data.errors).forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('border-red-500');

                    // Afficher le message d'erreur
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-red-500 text-sm mt-1';
                    errorDiv.textContent = data.errors[field][0];

                    // Supprimer l'ancien message d'erreur
                    const oldError = input.parentElement.querySelector('.text-red-500');
                    if (oldError) oldError.remove();

                    input.parentElement.appendChild(errorDiv);
                }
            });
        }
    }

    // ✅ AMÉLIORATION: Chargement des données utilisateur
    async loadUserData() {
        try {
            const userMeta = document.querySelector('meta[name="user-data"]');
            if (userMeta) {
                this.user = JSON.parse(userMeta.content);
                console.log('👤 User data loaded:', this.user.name);
            }
        } catch (error) {
            console.warn('⚠️ Unable to load user data:', error);
        }
    }

    // ✅ OPTIMISATION: Initialisation des composants admin
    initializeComponents() {
        this.initializeTomSelect();
        this.initializeFlatpickr();
        this.initializeTooltips();
        this.initializeForms();
        this.initializeModals();
        this.initializeDataTables();
        this.initializeFileUploads();
    }

    // Configuration TomSelect pour admin - SUPPRIMÉ (Legacy)
    initializeTomSelect() {
        console.log('NOTICE: TomSelect has been removed in favor of SlimSelect');
    }

    // ✅ NOUVEAU: Initialisation Flatpickr POUT TIMEPICKER/DATETIME (Legacy/Specific)
    initializeFlatpickr() {
        // Configurer la locale française par défaut
        flatpickr.localize(French);

        // NOTE: Les datepickers simples (.datepicker) sont maintenant gérés par Flowbite via Alpine.js

        // DATETIMEPICKERS (.datetimepicker) - Doivent rester sur Flatpickr car Flowbite n'a pas de temps
        const datetimepickers = document.querySelectorAll('.datetimepicker');
        datetimepickers.forEach(el => {
            if (!el._flatpickr) {
                const minDate = el.getAttribute('data-min-date');
                const maxDate = el.getAttribute('data-max-date');

                flatpickr(el, {
                    locale: 'fr',
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    altInput: true,
                    altFormat: "d/m/Y H:i",
                    minDate: minDate,
                    maxDate: maxDate,
                    time_24hr: true,
                    allowInput: true
                });
            }
        });

        // TIMEPICKERS - ENTERPRISE GRADE sans masque restrictif
        const timepickers = document.querySelectorAll('.timepicker');
        timepickers.forEach(el => {
            if (!el._flatpickr) {
                const enableSeconds = el.getAttribute('data-enable-seconds') === 'true';

                flatpickr(el, {
                    enableTime: true,
                    noCalendar: true,
                    // ✅ FORMAT: H:i (14:30) - Compatible Laravel
                    dateFormat: enableSeconds ? "H:i:S" : "H:i",
                    time_24hr: true,
                    // ✅ IMPORTANT: allowInput pour saisie manuelle libre
                    allowInput: true,
                    disableMobile: true,
                    // ✅ Heure par défaut: heure actuelle
                    defaultHour: new Date().getHours(),
                    defaultMinute: new Date().getMinutes(),
                    // ✅ Incréments: 15 minutes pour faciliter la saisie
                    minuteIncrement: 1,
                    // ✅ Parser flexible pour accepter différents formats
                    parseDate: (datestr) => {
                        // Accepter H:i, HH:i, H:i:s, etc.
                        const parts = datestr.split(':');
                        if (parts.length >= 2) {
                            const date = new Date();
                            date.setHours(parseInt(parts[0]) || 0);
                            date.setMinutes(parseInt(parts[1]) || 0);
                            if (parts.length >= 3) {
                                date.setSeconds(parseInt(parts[2]) || 0);
                            }
                            return date;
                        }
                        return new Date();
                    },
                });
            }
        });

        console.log(`📅 ${datetimepickers.length} datetimepickers + ${timepickers.length} timepickers initialized via Flatpickr`);
    }

    // Initialisation des tooltips
    initializeTooltips() {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(element => {
            // Implémentation simple de tooltip
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target, e.target.getAttribute('data-tooltip'));
            });

            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });

        console.log(`💡 ${tooltips.length} tooltips initialized`);
    }

    // ✅ AMÉLIORATION: Validation de formulaire avancée
    initializeForms() {
        const forms = document.querySelectorAll('form[data-validate]');

        forms.forEach(form => {
            // Validation en temps réel
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', (e) => this.validateField(e.target));
                input.addEventListener('input', (e) => this.clearFieldErrors(e.target));
            });

            // Validation à la soumission
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.notify('Veuillez corriger les erreurs du formulaire', 'warning');
                }
            });
        });

        console.log(`📋 ${forms.length} forms initialized with validation`);
    }

    // Validation de champ individuel
    validateField(field) {
        const rules = field.getAttribute('data-rules')?.split('|') || [];
        let isValid = true;
        let errorMessage = '';

        // Validation required
        if (rules.includes('required') && !field.value.trim()) {
            isValid = false;
            errorMessage = 'Ce champ est requis';
        }

        // Validation email
        if (rules.includes('email') && field.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                isValid = false;
                errorMessage = 'Format email invalide';
            }
        }

        // Validation min length
        const minLength = rules.find(r => r.startsWith('min:'));
        if (minLength && field.value) {
            const min = parseInt(minLength.split(':')[1]);
            if (field.value.length < min) {
                isValid = false;
                errorMessage = `Minimum ${min} caractères requis`;
            }
        }

        // Appliquer le style de validation
        field.classList.toggle('border-red-500', !isValid);
        field.classList.toggle('border-green-500', isValid && field.value);

        // Afficher/masquer le message d'erreur
        this.toggleFieldError(field, isValid, errorMessage);

        return isValid;
    }

    // Afficher/masquer erreur de champ
    toggleFieldError(field, isValid, errorMessage) {
        const wrapper = field.parentElement;
        let errorDiv = wrapper.querySelector('.field-error');

        if (!isValid && errorMessage) {
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'field-error text-red-500 text-sm mt-1';
                wrapper.appendChild(errorDiv);
            }
            errorDiv.textContent = errorMessage;
        } else if (errorDiv) {
            errorDiv.remove();
        }
    }

    // Effacer les erreurs de champ
    clearFieldErrors(field) {
        field.classList.remove('border-red-500');
        const errorDiv = field.parentElement.querySelector('.field-error');
        if (errorDiv) errorDiv.remove();
    }

    // Validation complète du formulaire
    validateForm(form) {
        const inputs = form.querySelectorAll('input[data-rules], select[data-rules], textarea[data-rules]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    // ✅ NOUVEAU: Initialisation des modales
    initializeModals() {
        const modalTriggers = document.querySelectorAll('[data-modal]');
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal');
                this.openModal(modalId);
            });
        });

        // Fermer les modales avec Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }

    // ✅ NOUVEAU: Gestion des tableaux de données
    initializeDataTables() {
        const tables = document.querySelectorAll('.data-table');
        tables.forEach(table => {
            // Tri des colonnes
            const headers = table.querySelectorAll('th[data-sortable]');
            headers.forEach(header => {
                header.addEventListener('click', () => {
                    this.sortTable(table, header);
                });
            });

            // Filtrage
            const filterInput = table.parentElement.querySelector('.table-filter');
            if (filterInput) {
                filterInput.addEventListener('input', (e) => {
                    this.filterTable(table, e.target.value);
                });
            }
        });
    }

    // ✅ NOUVEAU: Upload de fichiers
    initializeFileUploads() {
        const uploads = document.querySelectorAll('.file-upload');
        uploads.forEach(upload => {
            const input = upload.querySelector('input[type="file"]');
            const dropZone = upload.querySelector('.drop-zone');

            if (dropZone) {
                // Drag & Drop
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('drag-over');
                });

                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('drag-over');
                });

                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('drag-over');

                    const files = e.dataTransfer.files;
                    if (files.length > 0 && input) {
                        input.files = files;
                        this.handleFileUpload(input, files[0]);
                    }
                });
            }

            // Upload classique
            if (input) {
                input.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        this.handleFileUpload(e.target, e.target.files[0]);
                    }
                });
            }
        });
    }

    // Gestion de l'upload de fichier
    handleFileUpload(input, file) {
        // Validation du fichier
        const maxSize = input.getAttribute('data-max-size') || 2048; // 2MB par défaut
        const allowedTypes = input.getAttribute('data-allowed-types')?.split(',') || [];

        if (file.size > maxSize * 1024) {
            this.notify(`Fichier trop volumineux (max: ${maxSize}KB)`, 'error');
            return;
        }

        if (allowedTypes.length > 0 && !allowedTypes.includes(file.type)) {
            this.notify('Type de fichier non autorisé', 'error');
            return;
        }

        // Afficher un aperçu si c'est une image
        if (file.type.startsWith('image/')) {
            this.showImagePreview(input, file);
        }

        console.log('📁 File selected:', file.name, `(${(file.size / 1024).toFixed(2)}KB)`);
    }

    // Aperçu d'image
    showImagePreview(input, file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = input.parentElement.querySelector('.image-preview') ||
                this.createImagePreview(input.parentElement);
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    // ✅ SYSTÈME DE NOTIFICATIONS AVANCÉ
    notify(message, type = 'info', duration = 4000) {
        const notification = {
            id: Date.now(),
            message,
            type,
            duration
        };

        this.notifications.push(notification);
        this.renderNotification(notification);

        if (duration > 0) {
            setTimeout(() => this.removeNotification(notification.id), duration);
        }

        return notification.id;
    }

    // Rendu de notification
    renderNotification(notification) {
        const container = this.getNotificationContainer();
        const element = document.createElement('div');

        element.id = `notification-${notification.id}`;
        element.className = `
            notification transform transition-all duration-300 ease-out
            ${this.getNotificationClasses(notification.type)}
            fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm
            translate-x-full opacity-0
        `;

        element.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-3">
                    ${this.getNotificationIcon(notification.type)}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium">${notification.message}</p>
                </div>
                <button onclick="window.zenfleetAdmin.removeNotification(${notification.id})" 
                        class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        `;

        container.appendChild(element);

        // Animation d'entrée
        requestAnimationFrame(() => {
            element.classList.remove('translate-x-full', 'opacity-0');
        });
    }

    // Classes CSS pour notifications
    getNotificationClasses(type) {
        const classes = {
            success: 'bg-green-50 text-green-800 border border-green-200',
            error: 'bg-red-50 text-red-800 border border-red-200',
            warning: 'bg-yellow-50 text-yellow-800 border border-yellow-200',
            info: 'bg-blue-50 text-blue-800 border border-blue-200',
        };
        return classes[type] || classes.info;
    }

    // Icônes pour notifications (inline SVG - no CDN dependency)
    getNotificationIcon(type) {
        const icons = {
            success: '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
            error: '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
            warning: '<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
            info: '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
        };
        return icons[type] || icons.info;
    }

    // Container pour notifications
    getNotificationContainer() {
        let container = document.getElementById('admin-notifications');
        if (!container) {
            container = document.createElement('div');
            container.id = 'admin-notifications';
            container.className = 'fixed top-0 right-0 z-50 p-4 space-y-4 pointer-events-none';
            container.style.maxHeight = '100vh';
            container.style.overflowY = 'auto';
            document.body.appendChild(container);
        }
        return container;
    }

    // Suppression de notification
    removeNotification(id) {
        const element = document.getElementById(`notification-${id}`);
        if (element) {
            element.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => element.remove(), 300);
        }

        this.notifications = this.notifications.filter(n => n.id !== id);
    }

    // ✅ UTILITAIRES ADMIN
    showLoader() {
        let loader = document.getElementById('admin-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'admin-loader';
            loader.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            loader.innerHTML = `
                <div class="bg-white p-4 rounded-lg flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="text-gray-700">Chargement...</span>
                </div>
            `;
            document.body.appendChild(loader);
        }
        loader.classList.remove('hidden');
    }

    hideLoader() {
        const loader = document.getElementById('admin-loader');
        if (loader) {
            loader.classList.add('hidden');
        }
    }

    // Événements globaux
    setupEventListeners() {
        // Raccourcis clavier admin
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + S pour sauvegarder
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                const saveBtn = document.querySelector('.btn-save, [data-action="save"]');
                if (saveBtn) saveBtn.click();
            }

            // Ctrl/Cmd + K pour recherche
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('.admin-search, .search-input');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });

        // Confirmation avant fermeture si formulaire modifié
        let formModified = false;
        document.addEventListener('input', () => formModified = true);
        document.addEventListener('change', () => formModified = true);

        window.addEventListener('beforeunload', (e) => {
            if (formModified) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Marquer les formulaires comme sauvegardés lors de la soumission
        document.addEventListener('submit', () => formModified = false);
    }
}

// ✅ INITIALISATION LIVEWIRE + ALPINE
// Démarrer Livewire (doit être fait AVANT DOMContentLoaded)
Livewire.start();

// ✅ INITIALISATION GLOBALE
document.addEventListener('DOMContentLoaded', function () {
    // Initialiser les objets globaux
    initializeAdminGlobals();

    // Créer l'instance admin globale
    window.zenfleetAdmin = new ZenFleetAdmin();

    console.log('🎉 ZenFleet Admin fully loaded and ready!');
    console.log('⚡ Livewire 3 initialized and active');
});

// Export pour utilisation dans d'autres modules
export default ZenFleetAdmin;

