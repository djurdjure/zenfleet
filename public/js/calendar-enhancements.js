/**
 * Améliorations JavaScript pour l'interface calendaire ZenFleet
 */

// Utilitaires pour l'amélioration de l'expérience utilisateur
class CalendarEnhancements {
    constructor() {
        this.init();
    }

    init() {
        this.setupKeyboardNavigation();
        this.setupTouchGestures();
        this.setupPerformanceOptimizations();
        this.setupAccessibility();
        this.setupAnimations();
    }

    /**
     * Navigation au clavier pour l'accessibilité
     */
    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            const activeElement = document.activeElement;
            
            // Navigation dans le calendrier avec les flèches
            if (activeElement && activeElement.classList.contains('calendar-cell')) {
                switch (e.key) {
                    case 'ArrowRight':
                        e.preventDefault();
                        this.navigateToNextCell(activeElement);
                        break;
                    case 'ArrowLeft':
                        e.preventDefault();
                        this.navigateToPrevCell(activeElement);
                        break;
                    case 'ArrowDown':
                        e.preventDefault();
                        this.navigateToNextWeek(activeElement);
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        this.navigateToPrevWeek(activeElement);
                        break;
                    case 'Enter':
                    case ' ':
                        e.preventDefault();
                        this.selectCell(activeElement);
                        break;
                }
            }

            // Raccourcis clavier globaux
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        this.triggerPreviousMonth();
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        this.triggerNextMonth();
                        break;
                    case 'Home':
                        e.preventDefault();
                        this.triggerToday();
                        break;
                }
            }
        });
    }

    /**
     * Support des gestes tactiles pour mobile
     */
    setupTouchGestures() {
        let startX = 0;
        let startY = 0;
        const calendar = document.querySelector('.calendar-container');
        
        if (!calendar) return;

        calendar.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        }, { passive: true });

        calendar.addEventListener('touchend', (e) => {
            if (!startX || !startY) return;

            const endX = e.changedTouches[0].clientX;
            const endY = e.changedTouches[0].clientY;
            
            const diffX = startX - endX;
            const diffY = startY - endY;

            // Seuil minimum pour déclencher le geste
            const threshold = 50;

            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > threshold) {
                if (diffX > 0) {
                    // Swipe vers la gauche - mois suivant
                    this.triggerNextMonth();
                } else {
                    // Swipe vers la droite - mois précédent
                    this.triggerPreviousMonth();
                }
            }

            startX = 0;
            startY = 0;
        }, { passive: true });
    }

    /**
     * Optimisations de performance
     */
    setupPerformanceOptimizations() {
        // Lazy loading des cartes d'affectation
        this.setupLazyLoading();
        
        // Debounce pour les recherches
        this.setupSearchDebounce();
        
        // Virtual scrolling pour les grandes listes
        this.setupVirtualScrolling();
    }

    setupLazyLoading() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    card.classList.add('loaded');
                    observer.unobserve(card);
                }
            });
        }, {
            rootMargin: '50px'
        });

        document.querySelectorAll('.assignment-card').forEach(card => {
            observer.observe(card);
        });
    }

    setupSearchDebounce() {
        const searchInput = document.getElementById('search');
        if (!searchInput) return;

        let timeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.performSearch(e.target.value);
            }, 300);
        });
    }

    setupVirtualScrolling() {
        // Implémentation basique du virtual scrolling pour les grandes listes
        const containers = document.querySelectorAll('.large-list');
        containers.forEach(container => {
            this.initVirtualScroll(container);
        });
    }

    /**
     * Améliorations d'accessibilité
     */
    setupAccessibility() {
        // ARIA labels dynamiques
        this.updateAriaLabels();
        
        // Support des lecteurs d'écran
        this.setupScreenReaderSupport();
        
        // Gestion du focus
        this.setupFocusManagement();
    }

    updateAriaLabels() {
        document.querySelectorAll('.calendar-cell').forEach((cell, index) => {
            const date = this.getCellDate(cell);
            const assignments = cell.querySelectorAll('.assignment-card').length;
            
            cell.setAttribute('aria-label', 
                `${date.toLocaleDateString('fr-FR', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                })}, ${assignments} affectation${assignments > 1 ? 's' : ''}`
            );
            
            cell.setAttribute('tabindex', '0');
            cell.setAttribute('role', 'gridcell');
        });
    }

    setupScreenReaderSupport() {
        // Annonces pour les changements de mois
        const monthTitle = document.getElementById('currentMonth');
        if (monthTitle) {
            const observer = new MutationObserver(() => {
                this.announceToScreenReader(`Mois affiché: ${monthTitle.textContent}`);
            });
            
            observer.observe(monthTitle, { childList: true, subtree: true });
        }
    }

    setupFocusManagement() {
        // Gestion du focus lors de l'ouverture/fermeture des modales
        document.addEventListener('modal:open', (e) => {
            this.previousFocus = document.activeElement;
            setTimeout(() => {
                const modal = e.detail.modal;
                const firstFocusable = modal.querySelector('button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (firstFocusable) firstFocusable.focus();
            }, 100);
        });

        document.addEventListener('modal:close', () => {
            if (this.previousFocus) {
                this.previousFocus.focus();
                this.previousFocus = null;
            }
        });
    }

    /**
     * Animations et transitions fluides
     */
    setupAnimations() {
        // Animation d'entrée pour les nouvelles cartes
        this.setupCardAnimations();
        
        // Transitions de vue fluides
        this.setupViewTransitions();
        
        // Animations de chargement
        this.setupLoadingAnimations();
    }

    setupCardAnimations() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1 && node.classList.contains('assignment-card')) {
                        node.style.opacity = '0';
                        node.style.transform = 'translateY(10px)';
                        
                        requestAnimationFrame(() => {
                            node.style.transition = 'all 0.3s ease';
                            node.style.opacity = '1';
                            node.style.transform = 'translateY(0)';
                        });
                    }
                });
            });
        });

        document.querySelectorAll('.calendar-cell').forEach(cell => {
            observer.observe(cell, { childList: true });
        });
    }

    setupViewTransitions() {
        // Transition fluide entre vue tableau et calendrier
        document.addEventListener('view:change', (e) => {
            const { from, to } = e.detail;
            
            if (from) {
                from.classList.add('view-transition-leave');
                setTimeout(() => from.style.display = 'none', 300);
            }
            
            if (to) {
                to.style.display = 'block';
                to.classList.add('view-transition-enter');
                setTimeout(() => to.classList.remove('view-transition-enter'), 300);
            }
        });
    }

    setupLoadingAnimations() {
        // Skeleton loading pour les cartes
        this.createSkeletonLoaders();
        
        // Indicateurs de chargement pour les actions AJAX
        this.setupAjaxLoaders();
    }

    /**
     * Méthodes utilitaires
     */
    navigateToNextCell(currentCell) {
        const nextCell = currentCell.nextElementSibling;
        if (nextCell && nextCell.classList.contains('calendar-cell')) {
            nextCell.focus();
        }
    }

    navigateToPrevCell(currentCell) {
        const prevCell = currentCell.previousElementSibling;
        if (prevCell && prevCell.classList.contains('calendar-cell')) {
            prevCell.focus();
        }
    }

    navigateToNextWeek(currentCell) {
        const cells = Array.from(document.querySelectorAll('.calendar-cell'));
        const currentIndex = cells.indexOf(currentCell);
        const nextWeekCell = cells[currentIndex + 7];
        if (nextWeekCell) {
            nextWeekCell.focus();
        }
    }

    navigateToPrevWeek(currentCell) {
        const cells = Array.from(document.querySelectorAll('.calendar-cell'));
        const currentIndex = cells.indexOf(currentCell);
        const prevWeekCell = cells[currentIndex - 7];
        if (prevWeekCell) {
            prevWeekCell.focus();
        }
    }

    selectCell(cell) {
        // Logique de sélection de cellule
        cell.classList.toggle('selected');
        this.announceToScreenReader(`Cellule ${cell.getAttribute('aria-label')} sélectionnée`);
    }

    triggerPreviousMonth() {
        const prevButton = document.getElementById('prevMonth');
        if (prevButton) prevButton.click();
    }

    triggerNextMonth() {
        const nextButton = document.getElementById('nextMonth');
        if (nextButton) nextButton.click();
    }

    triggerToday() {
        const todayButton = document.getElementById('todayBtn');
        if (todayButton) todayButton.click();
    }

    getCellDate(cell) {
        // Extraction de la date depuis la cellule
        const dayNumber = cell.querySelector('.text-sm')?.textContent;
        const monthYear = document.getElementById('currentMonth')?.textContent;
        
        if (dayNumber && monthYear) {
            // Parsing basique - à améliorer selon le format exact
            return new Date(`${monthYear} ${dayNumber}`);
        }
        
        return new Date();
    }

    announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }

    performSearch(query) {
        // Logique de recherche avec mise en surbrillance
        const cards = document.querySelectorAll('.assignment-card');
        
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            const matches = text.includes(query.toLowerCase());
            
            card.style.display = matches || !query ? 'block' : 'none';
            
            if (matches && query) {
                this.highlightText(card, query);
            } else {
                this.removeHighlight(card);
            }
        });
    }

    highlightText(element, query) {
        // Mise en surbrillance du texte recherché
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );

        const textNodes = [];
        let node;
        while (node = walker.nextNode()) {
            textNodes.push(node);
        }

        textNodes.forEach(textNode => {
            const text = textNode.textContent;
            const regex = new RegExp(`(${query})`, 'gi');
            
            if (regex.test(text)) {
                const highlightedText = text.replace(regex, '<mark>$1</mark>');
                const wrapper = document.createElement('span');
                wrapper.innerHTML = highlightedText;
                textNode.parentNode.replaceChild(wrapper, textNode);
            }
        });
    }

    removeHighlight(element) {
        const marks = element.querySelectorAll('mark');
        marks.forEach(mark => {
            mark.outerHTML = mark.innerHTML;
        });
    }

    createSkeletonLoaders() {
        // Création de loaders skeleton pour l'amélioration de l'UX
        const skeletonHTML = `
            <div class="skeleton-card animate-pulse">
                <div class="h-4 bg-gray-200 rounded mb-2"></div>
                <div class="h-3 bg-gray-200 rounded mb-1"></div>
                <div class="h-3 bg-gray-200 rounded w-3/4"></div>
            </div>
        `;
        
        // Utilisation lors du chargement de nouvelles données
        return skeletonHTML;
    }

    setupAjaxLoaders() {
        // Intercepter les requêtes AJAX pour afficher des loaders
        const originalFetch = window.fetch;
        
        window.fetch = function(...args) {
            const loadingIndicator = document.querySelector('.loading-indicator');
            if (loadingIndicator) {
                loadingIndicator.style.display = 'block';
            }
            
            return originalFetch.apply(this, args)
                .finally(() => {
                    if (loadingIndicator) {
                        loadingIndicator.style.display = 'none';
                    }
                });
        };
    }
}

// Initialisation automatique
document.addEventListener('DOMContentLoaded', () => {
    new CalendarEnhancements();
});

// Export pour utilisation modulaire
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CalendarEnhancements;
}

