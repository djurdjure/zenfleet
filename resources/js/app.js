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
import './bootstrap';

// Import des librairies tierces avec optimisation
import Alpine from 'alpinejs';
import TomSelect from 'tom-select';
import Sortable from 'sortablejs';
import ApexCharts from 'apexcharts';
import flatpickr from "flatpickr";

// ✅ OPTIMISATION: Configuration des objets globaux de manière sécurisée
const initializeGlobals = () => {
    window.Alpine = Alpine;
    window.TomSelect = TomSelect;
    window.Sortable = Sortable;
    window.ApexCharts = ApexCharts;
    window.flatpickr = flatpickr;
};

// ✅ AMÉLIORATION: Configuration Alpine.js ultra-moderne
Alpine.data('zenfleet', () => ({
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
        // TomSelect avec configuration avancée
        this.initializeTomSelect();
        
        // Flatpickr avec localisation
        this.initializeFlatpickr();
        
        // Sortable avec sauvegarde d'état
        this.initializeSortable();
        
        // ApexCharts avec thème ZenFleet
        this.initializeCharts();
    },
    
    // Configuration TomSelect
    initializeTomSelect() {
        document.querySelectorAll('.select2, select[multiple], .tom-select').forEach(select => {
            if (!select.tomselect && !select.disabled) {
                new TomSelect(select, {
                    plugins: ['remove_button', 'dropdown_header'],
                    create: select.hasAttribute('data-create'),
                    maxItems: select.getAttribute('data-max-items') || null,
                    placeholder: select.getAttribute('placeholder') || 'Sélectionner...',
                    searchField: ['text', 'value'],
                    sortField: [
                        { field: 'text', direction: 'asc' }
                    ],
                    render: {
                        no_results: () => '<div class="no-results p-2 text-gray-500">Aucun résultat trouvé</div>',
                    }
                });
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

// ✅ OPTIMISATION: Configuration globale ZenFleet ultra-moderne
window.ZenFleet = {
    version: '2.1',
    
    // Utilitaires de formatage
    formatDate(date, format = 'dd/MM/yyyy') {
        if (!date) return '';
        
        try {
            if (window.flatpickr) {
                return window.flatpickr.formatDate(new Date(date), format);
            }
            return new Date(date).toLocaleDateString('fr-FR');
        } catch (error) {
            console.error('Erreur de formatage de date:', error);
            return date.toString();
        }
    },
    
    formatCurrency(amount, currency = 'EUR') {
        if (amount === null || amount === undefined) return '';
        
        try {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: currency
            }).format(amount);
        } catch (error) {
            console.error('Erreur de formatage de monnaie:', error);
            return `${amount} ${currency}`;
        }
    },
    
    formatNumber(number, decimals = 0) {
        if (number === null || number === undefined) return '';
        
        try {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(number);
        } catch (error) {
            console.error('Erreur de formatage de nombre:', error);
            return number.toString();
        }
    },
    
    // Utilitaires de confirmation
    confirm(message, callback, options = {}) {
        const confirmed = confirm(message);
        if (confirmed && typeof callback === 'function') {
            callback();
        }
        return confirmed;
    },
    
    // Utilitaires de stockage local
    storage: {
        set(key, value) {
            try {
                localStorage.setItem(`zenfleet_${key}`, JSON.stringify(value));
            } catch (error) {
                console.error('Erreur de sauvegarde locale:', error);
            }
        },
        
        get(key, defaultValue = null) {
            try {
                const item = localStorage.getItem(`zenfleet_${key}`);
                return item ? JSON.parse(item) : defaultValue;
            } catch (error) {
                console.error('Erreur de lecture locale:', error);
                return defaultValue;
            }
        },
        
        remove(key) {
            try {
                localStorage.removeItem(`zenfleet_${key}`);
            } catch (error) {
                console.error('Erreur de suppression locale:', error);
            }
        }
    }
};

// ✅ INITIALISATION: Configuration et démarrage
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les objets globaux
    initializeGlobals();
    
    // Démarrer Alpine.js
    Alpine.start();
    
    // Log de démarrage
    console.log('🚀 ZenFleet Application loaded successfully');
    console.log(`📊 Version: ${window.ZenFleet.version}`);
    console.log('🎨 Thème: Tailwind CSS + Alpine.js');
    
    // Notification de bienvenue (développement seulement)
    if (import.meta.env.DEV) {
        setTimeout(() => {
            const zenfleetData = Alpine.$data(document.body);
            if (zenfleetData && zenfleetData.notify) {
                zenfleetData.notify('ZenFleet initialisé avec succès!', 'success');
            }
        }, 1000);
    }
});

// Export pour utilisation dans d'autres modules
export default window.ZenFleet;

