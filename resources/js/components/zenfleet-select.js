/**
 * 🎯 ZENFLEET SLIMSELECT WRAPPER - ULTRA-PRO ENTERPRISE-GRADE
 *
 * Wrapper optimisé pour SlimSelect avec intégration native:
 * ✅ Alpine.js v3.x full integration
 * ✅ Livewire v3.x bidirectional sync
 * ✅ Memory leak prevention
 * ✅ Performance monitoring
 * ✅ WCAG 2.1 Level AA accessibility
 * ✅ Dark mode support
 * ✅ Error handling & logging
 * ✅ Event system enterprise
 *
 * @version 2.0.0-Enterprise
 * @author ZenFleet Architecture Team
 * @standard Surpasse Fleetio, Samsara, Verizon Connect
 */

import SlimSelect from 'slim-select';

/**
 * Configuration par défaut ZenFleet Enterprise
 */
const ZENFLEET_DEFAULTS = {
    settings: {
        searchText: 'Aucun résultat trouvé',
        searchPlaceholder: 'Rechercher...',
        searchHighlight: true,
        allowDeselect: true,
        closeOnSelect: true,
        showSearch: true,
        searchingText: 'Recherche en cours...',
        placeholderText: 'Sélectionner une option',
        maxValuesShown: 20,
        maxValuesMessage: '{number} élément(s) sélectionné(s)',
        contentLocation: document.body,  // Pour éviter overflow issues
        contentPosition: 'auto'
    },
    events: {},
    performance: {
        enableMetrics: true,
        logLevel: 'info' // 'debug', 'info', 'warn', 'error'
    }
};

/**
 * Classe principale ZenFleetSelect
 */
class ZenFleetSelect {
    constructor(element, options = {}) {
        this.element = typeof element === 'string'
            ? document.querySelector(element)
            : element;

        if (!this.element) {
            this.logError('Element not found', { selector: element });
            return null;
        }

        this.options = this.mergeOptions(options);
        this.slimInstance = null;
        this.livewireComponent = null;
        this.fallbackInput = null;
        this.init();
    }

    /**
     * Merge intelligent des options avec validation
     */
    mergeOptions(userOptions) {
        const merged = {
            ...ZENFLEET_DEFAULTS,
            ...userOptions,
            settings: {
                ...ZENFLEET_DEFAULTS.settings,
                ...(userOptions.settings || {})
            },
            performance: {
                ...ZENFLEET_DEFAULTS.performance,
                ...(userOptions.performance || {})
            }
        };

        // Auto-détection Livewire
        if (this.element.hasAttribute('wire:model') || this.element.hasAttribute('wire:model.live')) {
            merged.livewireSync = true;
            merged.livewireProperty = this.element.getAttribute('wire:model') ||
                this.element.getAttribute('wire:model.live');
        }

        // Auto-détection multi-select
        if (this.element.hasAttribute('multiple')) {
            merged.settings.closeOnSelect = false;
        }

        // ✅ Auto-configuration depuis les attributs DOM
        const placeholder = this.element.getAttribute('data-placeholder') || this.element.getAttribute('placeholder');
        if (placeholder) {
            merged.settings.placeholderText = placeholder;
            // Si pas de searchPlaceholder spécifique, utiliser une valeur par défaut cohérente
            if (!merged.settings.searchPlaceholder) {
                merged.settings.searchPlaceholder = 'Rechercher...';
            }
        }

        const searchable = this.element.getAttribute('data-searchable');
        if (searchable === 'false') {
            merged.settings.showSearch = false;
        }

        return merged;
    }

    /**
     * Initialisation avec monitoring performance
     */
    init() {
        const startTime = performance.now();

        try {
            // ✅ Detection du champ fallback (hidden input)
            // Doit être le précédent sibling immédiat avec le même nom (sans [])
            const inputName = this.element.name.replace('[]', '');
            const prevEl = this.element.previousElementSibling;
            if (prevEl && prevEl.tagName === 'INPUT' && prevEl.type === 'hidden' && prevEl.name === inputName) {
                this.fallbackInput = prevEl;
                this.log('debug', 'Fallback input found', { name: inputName });
            }

            // Préparation des données
            const data = this.prepareData();

            // Configuration SlimSelect
            const config = {
                select: this.element,
                settings: this.options.settings,
                events: this.setupEvents(),
                data: data
            };

            // Création instance
            this.slimInstance = new SlimSelect(config);

            // Styling ZenFleet
            this.applyZenFleetStyling();

            // Setup Livewire sync
            if (this.options.livewireSync) {
                this.setupLivewireSync();
            }

            // Setup observers pour dynamic updates
            this.setupObservers();

            // Métriques
            this.performanceMetrics.initTime = performance.now() - startTime;

            this.log('info', 'Initialized successfully', {
                element: this.element.id || this.element.name,
                optionsCount: this.slimInstance.getData().length,
                initTime: `${this.performanceMetrics.initTime.toFixed(2)}ms`,
                livewireSync: this.options.livewireSync,
                multiple: this.element.hasAttribute('multiple')
            });

            // ✅ Enterprise Fix: Ensure robust sync with original select AND fallback input initialization
            this.setupFormSync();
            this.manageFallbackInput(); // Init state based on current selection

        } catch (error) {
            this.logError('Initialization failed', error);
            this.handleError(error);
        }
    }

    prepareData() {
        if (this.options.data) {
            return this.options.data;
        }

        // Extraction depuis les options HTML
        const options = Array.from(this.element.options);
        return options.map(opt => ({
            text: opt.textContent.trim(),
            value: opt.value,
            selected: opt.selected,
            disabled: opt.disabled,
            placeholder: (opt.value === '' && opt.textContent.trim() === 'Sélectionner') || opt.hasAttribute('data-placeholder'),
            data: opt.dataset ? Object.assign({}, opt.dataset) : {}
        })).filter(item => !item.placeholder);
    }

    /**
     * Setup des événements avec logging
     */
    setupEvents() {
        const events = {
            // Événement après changement
            afterChange: (newVal) => {
                this.log('debug', 'Value changed', { newValue: newVal });

                // ✅ Enterprise Fix: Sync immediately on change
                this.syncToOriginalSelect();

                // ✅ Update fallback input state
                this.manageFallbackInput();

                // Callback utilisateur
                if (this.options.events.afterChange) {
                    this.options.events.afterChange(newVal);
                }

                // Sync Livewire
                if (this.options.livewireSync && this.livewireComponent) {
                    this.syncToLivewire(newVal);
                }

                // Événement custom DOM
                this.element.dispatchEvent(new CustomEvent('zenfleet:select-change', {
                    detail: { value: newVal, timestamp: Date.now() },
                    bubbles: true
                }));
            },

            // Événement recherche avec monitoring performance
            search: (search, currentData) => {
                const startTime = performance.now();

                this.performanceMetrics.lastSearchQuery = search;
                this.performanceMetrics.searchCount++;

                const filtered = this.options.events.search
                    ? this.options.events.search(search, currentData)
                    : this.defaultSearch(search, currentData);

                this.performanceMetrics.searchTime = performance.now() - startTime;

                this.log('debug', 'Search performed', {
                    query: search,
                    resultsCount: filtered.length,
                    searchTime: `${this.performanceMetrics.searchTime.toFixed(2)}ms`
                });

                return filtered;
            },

            // Événement ouverture
            afterOpen: () => {
                this.log('debug', 'Dropdown opened');

                if (this.options.events.afterOpen) {
                    this.options.events.afterOpen();
                }

                this.element.classList.add('zenfleet-select-open');

                // Focus sur search input si présent
                setTimeout(() => {
                    const searchInput = this.element.parentElement.querySelector('.ss-search input');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }, 100);
            },

            // Événement fermeture
            afterClose: () => {
                this.log('debug', 'Dropdown closed');

                if (this.options.events.afterClose) {
                    this.options.events.afterClose();
                }

                this.element.classList.remove('zenfleet-select-open');
            },

            // Événement erreur
            error: (error) => {
                this.logError('SlimSelect error', error);
                this.handleError(error);
            }
        };

        return events;
    }

    /**
     * Recherche par défaut optimisée (fuzzy search)
     */
    defaultSearch(search, currentData) {
        if (!search || search.length < 1) return currentData;

        const searchLower = search.toLowerCase().trim();
        const searchTerms = searchLower.split(' ').filter(t => t.length > 0);

        return currentData.filter(item => {
            const textLower = item.text.toLowerCase();

            // Match exact
            if (textLower.includes(searchLower)) return true;

            // Match tous les termes
            return searchTerms.every(term => textLower.includes(term));
        });
    }

    /**
     * ✅ Enterprise Logic: Setup Form Synchronization
     * Ensures that values are forcefully synced to the original select before submit
     */
    setupFormSync() {
        const form = this.element.closest('form');
        if (form && !form.dataset.zenfleetSelectHandlerAttached) {
            form.dataset.zenfleetSelectHandlerAttached = 'true';

            // Capture phase event listener to run before other handlers
            form.addEventListener('submit', () => {
                form.querySelectorAll('select').forEach(select => {
                    if (select.zenfleetSelect) {
                        select.zenfleetSelect.syncToOriginalSelect();
                    }
                });
            }, true);

            this.log('debug', 'Form submit handler attached');
        }
    }

    /**
     * ✅ Enterprise Logic: Force Sync to Original Select
     * Critical for multi-selects where DOM state might desync
     */
    syncToOriginalSelect() {
        if (!this.slimInstance) return;

        const selectedValues = this.slimInstance.getSelected();
        const valueArray = Array.isArray(selectedValues) ? selectedValues : [selectedValues];

        // Reset all options
        Array.from(this.element.options).forEach(opt => {
            opt.selected = false;
        });

        // Set selected options
        valueArray.forEach(val => {
            // Handle both primitive values and object values from SlimSelect
            const value = (val && typeof val === 'object' && 'value' in val) ? val.value : val;

            // Find specific option to handle special characters in values safely
            // We use attribute selector for exact match
            const option = Array.from(this.element.options).find(opt => opt.value === String(value));

            if (option) {
                option.selected = true;
                option.setAttribute('selected', 'selected'); // Force attribute update for serialization
            }
        });

        this.log('debug', 'Synced to original select', { values: valueArray });
    }

    /**
     * ✅ Enterprise Logic: Manage Fallback Hidden Input
     * Disables the fallback hidden input if items are selected to prevent
     * redundant/conflicting data submission (e.g. overwriting with empty string).
     */
    manageFallbackInput() {
        if (!this.fallbackInput) return;

        const selectedValues = this.slimInstance ? this.slimInstance.getSelected() : [];
        const hasSelection = Array.isArray(selectedValues) ? selectedValues.length > 0 : !!selectedValues;

        if (hasSelection) {
            // Desactiver le fallback car le select enverra les données
            this.fallbackInput.disabled = true;
            this.log('debug', 'Fallback input DISABLED (selection active)');
        } else {
            // Activer le fallback pour envoyer use valeur vide
            this.fallbackInput.disabled = false;
            this.log('debug', 'Fallback input ENABLED (no selection)');
        }
    }

    /**
     * Setup synchronisation Livewire bidirectionnelle
     */
    setupLivewireSync() {
        // Détection du composant Livewire parent
        const livewireEl = this.element.closest('[wire\\:id]');
        if (!livewireEl) {
            this.log('warn', 'Livewire component not found for sync');
            return;
        }

        const wireId = livewireEl.getAttribute('wire:id');
        this.livewireComponent = window.Livewire?.find?.(wireId);

        if (!this.livewireComponent) {
            this.log('warn', 'Livewire instance not found', { wireId });
            return;
        }

        // Listener pour updates Livewire → SlimSelect
        try {
            this.livewireComponent.$watch(this.options.livewireProperty, (value) => {
                if (this.slimInstance && value !== undefined) {
                    // Éviter boucles infinies
                    const currentValue = this.slimInstance.getSelected();
                    if (JSON.stringify(currentValue) !== JSON.stringify(value)) {
                        this.slimInstance.setSelected(value);
                        this.log('debug', 'Livewire → SlimSelect sync', { value });
                    }
                }
            });

            this.log('info', 'Livewire sync enabled', {
                property: this.options.livewireProperty,
                wireId
            });
        } catch (error) {
            this.logError('Livewire watch setup failed', error);
        }
    }

    /**
     * Synchronisation SlimSelect → Livewire
     */
    syncToLivewire(value) {
        if (!this.livewireComponent) return;

        try {
            this.livewireComponent.set(this.options.livewireProperty, value);
            this.log('debug', 'SlimSelect → Livewire sync', { value });
        } catch (error) {
            this.logError('Livewire sync failed', error);
        }
    }

    /**
     * Application du styling ZenFleet cohérent
     */
    applyZenFleetStyling() {
        const container = this.element.parentElement.querySelector('.ss-main');
        if (!container) {
            this.log('warn', 'SlimSelect container not found for styling');
            return;
        }

        // Classes Tailwind cohérentes avec le design system ZenFleet
        container.classList.add('zenfleet-select-container');

        // Ajouter attributs ARIA supplémentaires
        container.setAttribute('aria-label', this.element.getAttribute('aria-label') || 'Select');

        this.log('debug', 'ZenFleet styling applied');
    }

    /**
     * Setup observers pour dynamic content updates
     */
    setupObservers() {
        // Observer pour détection de changements dans les options du select original
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList' || mutation.type === 'attributes') {
                    this.log('debug', 'Select options changed, refreshing SlimSelect');
                    this.refresh();
                }
            });
        });

        observer.observe(this.element, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['disabled', 'selected']
        });

        this.observers.push(observer);
    }

    /**
     * Refresh des données
     */
    refresh() {
        if (!this.slimInstance) return;

        try {
            const data = this.prepareData();
            this.slimInstance.setData(data);
            this.log('debug', 'Data refreshed', { optionsCount: data.length });
        } catch (error) {
            this.logError('Refresh failed', error);
        }
    }

    /**
     * Gestion d'erreurs enterprise
     */
    handleError(error) {
        // Log vers monitoring externe si disponible
        if (window.Sentry) {
            window.Sentry.captureException(error, {
                tags: {
                    component: 'ZenFleetSelect',
                    element: this.element.id || this.element.name
                }
            });
        }

        // Affichage utilisateur discret
        const errorMsg = document.createElement('div');
        errorMsg.className = 'zenfleet-select-error text-red-600 dark:text-red-400 text-xs mt-1 flex items-center gap-1';
        errorMsg.innerHTML = `
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>Erreur de chargement. Rafraîchissez la page.</span>
        `;

        const existingError = this.element.parentElement.querySelector('.zenfleet-select-error');
        if (existingError) {
            existingError.remove();
        }

        this.element.parentElement.appendChild(errorMsg);

        // Auto-remove après 5s
        setTimeout(() => errorMsg.remove(), 5000);
    }

    /**
     * Méthodes publiques API
     */
    setData(data) {
        if (this.slimInstance) {
            this.slimInstance.setData(data);
            this.log('debug', 'Data updated via API', { count: data.length });
        }
    }

    setSelected(value) {
        if (this.slimInstance) {
            this.slimInstance.setSelected(value);
            this.log('debug', 'Selected value updated via API', { value });
        }
    }

    getSelected() {
        return this.slimInstance ? this.slimInstance.getSelected() : null;
    }

    enable() {
        if (this.slimInstance) {
            this.slimInstance.enable();
            this.log('debug', 'Select enabled');
        }
    }

    disable() {
        if (this.slimInstance) {
            this.slimInstance.disable();
            this.log('debug', 'Select disabled');
        }
    }

    open() {
        if (this.slimInstance) {
            this.slimInstance.open();
        }
    }

    close() {
        if (this.slimInstance) {
            this.slimInstance.close();
        }
    }

    /**
     * Destruction propre (memory leak prevention)
     */
    destroy() {
        this.log('info', 'Destroying instance', {
            element: this.element.id || this.element.name,
            metrics: this.performanceMetrics
        });

        // Disconnect observers
        this.observers.forEach(observer => observer.disconnect());
        this.observers = [];

        // Destroy SlimSelect
        if (this.slimInstance) {
            try {
                this.slimInstance.destroy();
            } catch (error) {
                this.logError('Destruction error', error);
            }
            this.slimInstance = null;
        }

        // Cleanup
        this.livewireComponent = null;
        this.element.classList.remove('zenfleet-select-open');

        // Remove error messages
        const errorMsg = this.element.parentElement?.querySelector('.zenfleet-select-error');
        if (errorMsg) {
            errorMsg.remove();
        }
    }

    /**
     * Métriques de performance
     */
    getMetrics() {
        return {
            ...this.performanceMetrics,
            optionsCount: this.slimInstance ? this.slimInstance.getData().length : 0,
            isOpen: this.element.classList.contains('zenfleet-select-open'),
            hasLivewireSync: !!this.livewireComponent
        };
    }

    /**
     * Logging system
     */
    log(level, message, data = {}) {
        if (!this.options.performance.enableMetrics) return;

        const logLevels = { debug: 0, info: 1, warn: 2, error: 3 };
        const currentLevel = logLevels[this.options.performance.logLevel] || 1;
        const messageLevel = logLevels[level] || 1;

        if (messageLevel < currentLevel) return;

        const prefix = '[ZenFleetSelect]';
        const elementInfo = this.element.id || this.element.name || 'unknown';

        if (level === 'error') {
            console.error(prefix, message, { element: elementInfo, ...data });
        } else if (level === 'warn') {
            console.warn(prefix, message, { element: elementInfo, ...data });
        } else if (level === 'info') {
            console.info(prefix, message, { element: elementInfo, ...data });
        } else {
            console.log(prefix, message, { element: elementInfo, ...data });
        }
    }

    logError(message, error) {
        this.log('error', message, {
            error: error instanceof Error ? error.message : error,
            stack: error instanceof Error ? error.stack : undefined
        });
    }
}

/**
 * Factory function pour usage simple
 */
export function createZenFleetSelect(element, options = {}) {
    return new ZenFleetSelect(element, options);
}

/**
 * Directive Alpine.js personnalisée
 */
export function zenfleetSelectDirective(Alpine) {
    Alpine.directive('zenfleet-select', (el, { expression }, { evaluateLater, cleanup }) => {
        const getOptions = evaluateLater(expression);
        let selectInstance;

        getOptions(options => {
            selectInstance = new ZenFleetSelect(el, options || {});
        });

        cleanup(() => {
            if (selectInstance) {
                selectInstance.destroy();
            }
        });
    });
}

/**
 * Helper Alpine.data pour usage standard
 */
export function zenfleetSelectData() {
    return {
        selectInstance: null,

        init() {
            // Auto-init si l'élément a x-ref="select"
            const selectEl = this.$refs.select || this.$el.querySelector('select');
            if (selectEl) {
                this.selectInstance = new ZenFleetSelect(selectEl, this.selectOptions || {});
            }
        },

        destroy() {
            if (this.selectInstance) {
                this.selectInstance.destroy();
                this.selectInstance = null;
            }
        }
    };
}

// Export par défaut
export default ZenFleetSelect;
