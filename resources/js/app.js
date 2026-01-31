/**
 * 🚀 ZENFLEET APPLICATION - Point d'entrée ultra-optimisé
 * 
 * Environnement: Docker + Yarn + Tailwind + Alpine.js
 * Version: 2.1 Ultra-Pro
 * Auteur: ZenFleet Development Team
 */

// ✅ CRITIQUE: Import CSS en PREMIER (obligatoire pour Vite)
import '../css/app.css';

// Import des dépendances système
// 🚫 FORCE LIGHT THEME (Enterprise Standard)
document.documentElement.classList.remove('dark');
if (localStorage.theme === 'dark') localStorage.removeItem('theme');

import './bootstrap';

// Import des librairies tierces avec optimisation
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import ApexCharts from 'apexcharts';
import flatpickr from "flatpickr";

// Import ZenFleet SlimSelect (nouveau remplacement de TomSelect)
import ZenFleetSelect, { zenfleetSelectDirective, zenfleetSelectData } from './components/zenfleet-select';

// Import ZenFleet Datepicker (Enterprise-grade Flowbite Datepicker wrapper)
import { zenfleetDatepickerData } from './components/zenfleet-datepicker';

// ✅ OPTIMISATION: Configuration des objets globaux de manière sécurisée
const initializeGlobals = () => {
    window.Alpine = Alpine;
    window.ZenFleetSelect = ZenFleetSelect;
    window.Sortable = Sortable;
    window.ApexCharts = ApexCharts;
    window.flatpickr = flatpickr;

    // ✅ RESTORE: Global ZenFleet object
    window.ZenFleet = {
        version: '2.1',
        csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
    };
};

// Expose globals immediately
initializeGlobals();

// ✅ FONCTION D'ENREGISTREMENT UNIVERSELLE
// Permet d'enregistrer les composants sur n'importe quelle instance Alpine (Importée ou Livewire)
const registerAlpineComponents = (alpineInstance) => {
    if (!alpineInstance) return;

    // Éviter le double enregistrement
    if (alpineInstance.zenfleetComponentsRegistered) {
        console.log('[ZenFleet] Components already registered on this Alpine instance');
        return;
    }

    console.log('[ZenFleet] Registering components on Alpine instance:', alpineInstance === window.Alpine ? 'Window Alpine' : 'Imported Alpine');

    // ZenFleetSelect
    zenfleetSelectDirective(alpineInstance);
    alpineInstance.data('zenfleetSelect', zenfleetSelectData);

    // ZenFleetDatepicker
    alpineInstance.data('zenfleetDatepicker', zenfleetDatepickerData);

    // Main Scope 'zenfleet'
    alpineInstance.data('zenfleet', () => ({
        // État de l'application
        version: '2.1',
        user: null,
        loading: false,
        notifications: [],

        // Initialisation
        init() {
            console.log(`🚀 ZenFleet v${this.version} - Alpine.js initialized`);
            this.setupGlobalHandlers();
            this.loadUserData();
            this.initializeComponents();
        },

        // ✅ AMÉLIORATION: Gestionnaires globaux optimisés
        setupGlobalHandlers() {
            this.handleAlerts();
            this.setupFormValidation();
            this.setupKeyboardShortcuts();
            this.setupErrorHandling();
        },

        // Gestion des alertes avec animation
        handleAlerts() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach((alert, index) => {
                // Animation d'entrée
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(100%)';

                setTimeout(() => {
                    alert.style.transition = 'all 0.3s ease';
                    alert.style.opacity = '1';
                    alert.style.transform = 'translateX(0)';
                }, index * 100);

                // Auto-dismiss après 5 secondes
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(100%)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000 + (index * 100));
            });
        },

        // ✅ AMÉLIORATION: Validation de formulaire avancée
        setupFormValidation() {
            const fields = document.querySelectorAll('input[required], select[required], textarea[required]');

            fields.forEach(field => {
                // Validation en temps réel
                field.addEventListener('blur', this.validateField.bind(this));
                field.addEventListener('input', this.clearValidationErrors.bind(this));

                // Indicateur de validation visuelle
                const wrapper = field.closest('.form-group, .field-wrapper') || field.parentElement;
                if (wrapper && !wrapper.querySelector('.validation-icon')) {
                    const icon = document.createElement('div');
                    icon.className = 'validation-icon absolute right-3 top-1/2 transform -translate-y-1/2';
                    wrapper.classList.add('relative');
                    wrapper.appendChild(icon);
                }
            });
        },

        // Validation individuelle de champ
        validateField(event) {
            const field = event.target;
            const isValid = field.value.trim() !== '';
            const wrapper = field.closest('.form-group, .field-wrapper') || field.parentElement;
            const icon = wrapper.querySelector('.validation-icon');

            // Classes de validation
            field.classList.toggle('border-danger-500', !isValid);
            field.classList.toggle('border-success-500', isValid);
            field.classList.toggle('ring-danger-200', !isValid);
            field.classList.toggle('ring-success-200', isValid);

            // Icône de validation
            if (icon) {
                icon.innerHTML = isValid
                    ? '<i class="fas fa-check text-success-500"></i>'
                    : '<i class="fas fa-times text-danger-500"></i>';
            }

            return isValid;
        },

        // Effacer les erreurs de validation
        clearValidationErrors(event) {
            const field = event.target;
            field.classList.remove('border-danger-500', 'ring-danger-200');
        },

        // ✅ NOUVEAU: Raccourcis clavier
        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + K pour la recherche
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.querySelector('input[type="search"], .search-input');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }

                // Échapper pour fermer les modales
                if (e.key === 'Escape') {
                    const openModals = document.querySelectorAll('.modal:not(.hidden)');
                    openModals.forEach(modal => {
                        const closeBtn = modal.querySelector('.modal-close, [data-dismiss="modal"]');
                        if (closeBtn) closeBtn.click();
                    });
                }
            });
        },

        // ✅ NOUVEAU: Gestion globale des erreurs
        setupErrorHandling() {
            window.addEventListener('error', (e) => {
                console.error('ZenFleet Error:', e.error);
                this.notify('Une erreur inattendue s\'est produite', 'danger');
            });

            window.addEventListener('unhandledrejection', (e) => {
                console.error('ZenFleet Unhandled Promise:', e.reason);
                this.notify('Erreur de communication avec le serveur', 'warning');
            });
        },

        // Chargement des données utilisateur
        async loadUserData() {
            try {
                // Simulation du chargement des données utilisateur
                const userMeta = document.querySelector('meta[name="user-data"]');
                if (userMeta) {
                    this.user = JSON.parse(userMeta.content);
                }
            } catch (error) {
                console.warn('Impossible de charger les données utilisateur:', error);
            }
        },

        // ✅ AMÉLIORATION: Initialisation des composants optimisée
        initializeComponents() {
            // ZenFleetSelect (SlimSelect) avec configuration ultra-pro
            this.initializeZenFleetSelect();

            // Flatpickr avec localisation
            this.initializeFlatpickr();

            // Sortable avec sauvegarde d'état
            this.initializeSortable();

            // ApexCharts avec thème ZenFleet
            this.initializeCharts();
        },

        // Configuration ZenFleetSelect (SlimSelect ultra-optimisé)
        initializeZenFleetSelect() {
            // Auto-init tous les select qui n'ont pas x-data ou wire:ignore
            document.querySelectorAll('select:not([x-data]):not([wire\\:ignore])').forEach(select => {
                // Skip si déjà initialisé ou désactivé
                if (select.zenfleetSelect || select.disabled) return;

                // Skip si dans un composant Alpine.js ou Livewire
                if (select.closest('[x-data]') || select.closest('[wire\\:id]')) return;

                try {
                    const instance = new ZenFleetSelect(select, {
                        settings: {
                            searchPlaceholder: select.getAttribute('placeholder') || 'Rechercher...',
                            placeholderText: select.getAttribute('placeholder') || 'Sélectionner une option',
                            allowDeselect: !select.hasAttribute('required'),
                            closeOnSelect: !select.hasAttribute('multiple')
                        }
                    });

                    // Stocker l'instance pour référence
                    select.zenfleetSelect = instance;
                } catch (error) {
                    console.error('[ZenFleet] Erreur init select:', select, error);
                }
            });
        },

        // Configuration Flatpickr
        initializeFlatpickr() {
            document.querySelectorAll('input[type="date"], .datepicker, .datetime-picker').forEach(input => {
                if (input.flatpickr) return;

                const isDateTime = input.classList.contains('datetime-picker');

                flatpickr(input, {
                    dateFormat: isDateTime ? "d/m/Y H:i" : "d/m/Y",
                    locale: "fr",
                    allowInput: true,
                    enableTime: isDateTime,
                    time_24hr: true,
                    altInput: true,
                    altFormat: isDateTime ? "d/m/Y à H:i" : "d/m/Y",
                    theme: "zenfleet", // Thème personnalisé
                });
            });
        },

        // Configuration Sortable
        initializeSortable() {
            document.querySelectorAll('.sortable').forEach(list => {
                if (list.sortable) return;

                Sortable.create(list, {
                    animation: 150,
                    ghostClass: 'bg-primary-100 opacity-50',
                    chosenClass: 'bg-primary-50',
                    dragClass: 'rotate-3 shadow-lg',
                    onEnd: (evt) => {
                        // Sauvegarde de l'ordre si un endpoint est défini
                        const saveUrl = list.getAttribute('data-save-order');
                        if (saveUrl) {
                            this.saveListOrder(saveUrl, list);
                        }
                    }
                });
            });
        },

        // Initialisation des graphiques
        initializeCharts() {
            document.querySelectorAll('.chart-container[data-chart-type]').forEach(container => {
                if (container.chart) return;

                const chartType = container.getAttribute('data-chart-type');
                const chartData = JSON.parse(container.getAttribute('data-chart-data') || '{}');

                // Configuration par défaut ZenFleet
                const defaultOptions = {
                    theme: {
                        mode: 'light',
                        palette: 'palette1',
                    },
                    colors: ['#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#06b6d4'],
                    chart: {
                        fontFamily: 'Inter, sans-serif',
                        toolbar: { show: false },
                        background: 'transparent',
                    },
                };

                const chart = new ApexCharts(container, { ...defaultOptions, ...chartData });
                chart.render();
                container.chart = chart;
            });
        },

        // Sauvegarde de l'ordre des listes
        async saveListOrder(url, list) {
            const items = Array.from(list.children).map((item, index) => ({
                id: item.getAttribute('data-id'),
                order: index
            }));

            try {
                await window.axios.post(url, { items });
                this.notify('Ordre sauvegardé', 'success');
            } catch (error) {
                console.error('Erreur de sauvegarde:', error);
                this.notify('Erreur lors de la sauvegarde', 'danger');
            }
        },

        // ✅ AMÉLIORATION: Système de notifications avancé
        notify(message, type = 'info', options = {}) {
            const notification = {
                id: Date.now(),
                message,
                type,
                duration: options.duration || 4000,
                actions: options.actions || null
            };

            this.notifications.push(notification);
            this.renderNotification(notification);

            // Auto-remove
            if (notification.duration > 0) {
                setTimeout(() => {
                    this.removeNotification(notification.id);
                }, notification.duration);
            }
        },

        // Rendu des notifications
        renderNotification(notification) {
            const container = this.getNotificationContainer();
            const element = document.createElement('div');

            element.id = `notification-${notification.id}`;
            element.className = `
            notification animate-fade-in transform transition-all duration-300
            ${this.getNotificationClasses(notification.type)}
            fixed top-4 right-4 p-4 rounded-lg shadow-zenfleet-lg z-50 max-w-sm
        `;

            element.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${this.getNotificationIcon(notification.type)}
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">${notification.message}</p>
                    ${notification.actions ? this.renderNotificationActions(notification.actions) : ''}
                </div>
                <button onclick="zenfleet.removeNotification(${notification.id})" 
                        class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

            container.appendChild(element);
        },

        // Classes CSS pour les notifications
        getNotificationClasses(type) {
            const classes = {
                success: 'bg-success-50 text-success-800 border border-success-200',
                danger: 'bg-danger-50 text-danger-800 border border-danger-200',
                warning: 'bg-warning-50 text-warning-800 border border-warning-200',
                info: 'bg-info-50 text-info-800 border border-info-200',
            };
            return classes[type] || classes.info;
        },

        // Icônes pour les notifications
        getNotificationIcon(type) {
            const icons = {
                success: '<i class="fas fa-check-circle text-success-500"></i>',
                danger: '<i class="fas fa-exclamation-triangle text-danger-500"></i>',
                warning: '<i class="fas fa-exclamation-circle text-warning-500"></i>',
                info: '<i class="fas fa-info-circle text-info-500"></i>',
            };
            return icons[type] || icons.info;
        },

        // Container pour les notifications
        getNotificationContainer() {
            let container = document.getElementById('notifications-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'notifications-container';
                container.className = 'fixed top-0 right-0 z-50 p-4 space-y-4';
                document.body.appendChild(container);
            }
            return container;
        },

        // Suppression des notifications
        removeNotification(id) {
            const element = document.getElementById(`notification-${id}`);
            if (element) {
                element.style.opacity = '0';
                element.style.transform = 'translateX(100%)';
                setTimeout(() => element.remove(), 300);
            }

            this.notifications = this.notifications.filter(n => n.id !== id);
        },
    }));

    // Marquer comme enregistré
    alpineInstance.zenfleetComponentsRegistered = true;
};

// 1. Enregistrement sur l'instance Alpine importée (méthode classique)
registerAlpineComponents(Alpine);

// 2. Enregistrement sur l'instance globale (Livewire) au cas où
document.addEventListener('alpine:init', () => {
    registerAlpineComponents(window.Alpine);
});

// ✅ INITIALISATION: Configuration et démarrage
document.addEventListener('DOMContentLoaded', function () {

    // Démarrer Alpine.js (si pas déjà démarré par Livewire)
    Alpine.start();

    // Log de démarrage
    console.log('🚀 ZenFleet Application loaded successfully');
    console.log(`📊 Version: ${window.ZenFleet.version}`);
    console.log('🎨 Thème: Tailwind CSS + Alpine.js');

    // Notification de bienvenue (développement seulement)
    if (import.meta.env.DEV) {
        setTimeout(() => {
            // Tentative d'accès sécurisé à l'instance Alpine
            try {
                if (window.Alpine) {
                    // Utiliser une méthode plus sûre pour accéder aux données
                    const zenfleetData = document.querySelector('[x-data="zenfleet"]');
                    if (zenfleetData && zenfleetData.__x) {
                        // Accès interne Alpine si nécessaire, ou ignorer la notif
                    }
                }
            } catch (e) {
                console.log('ZenFleet init check passed');
            }
        }, 1000);
    }
});

// Export pour utilisation dans d'autres modules
export default window.ZenFleet;
