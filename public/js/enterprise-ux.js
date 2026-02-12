/**
 * 🚀 ZenFleet Enterprise UX/UI Enhancement Suite
 * ================================================
 * Micro-interactions, animations et optimisations UX enterprise-grade
 * @author Claude Code Enterprise Suite
 * @version 2.0.0
 */

class EnterpriseUX {
    constructor() {
        this.isInitialized = false;
        this.observers = [];
        this.animations = new Map();
        this.performance = {
            animationFrame: null,
            throttleDelay: 16 // 60fps
        };

        this.init();
    }

    /**
     * 🎯 Initialisation du système UX Enterprise
     */
    init() {
        if (this.isInitialized) return;

        this.setupIntersectionObserver();
        this.initScrollAnimations();
        this.initFormEnhancements();
        this.initNavigationEnhancements();
        this.initTooltips();
        this.initLoadingStates();
        this.initMetricAnimations();
        this.initResponsiveOptimizations();
        this.initPerformanceOptimizations();

        this.isInitialized = true;
        console.log('🚀 ZenFleet Enterprise UX Suite initialized');
    }

    /**
     * 👁️ Configuration de l'Intersection Observer pour les animations
     */
    setupIntersectionObserver() {
        if (!('IntersectionObserver' in window)) return;

        const observerOptions = {
            root: null,
            rootMargin: '50px',
            threshold: [0.1, 0.5, 1.0]
        };

        // Observer pour les animations d'entrée
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.triggerAnimation(entry.target);
                }
            });
        }, observerOptions);

        // Ajouter les éléments à observer
        document.querySelectorAll('[data-animate]').forEach(el => {
            animationObserver.observe(el);
        });

        this.observers.push(animationObserver);
    }

    /**
     * 🎬 Déclenchement d'animations spécifiques
     */
    triggerAnimation(element) {
        const animationType = element.dataset.animate;
        const delay = element.dataset.animateDelay || 0;

        setTimeout(() => {
            switch (animationType) {
                case 'slide-in-top':
                    element.classList.add('animate-slide-in-top');
                    break;
                case 'slide-in-bottom':
                    element.classList.add('animate-slide-in-bottom');
                    break;
                case 'slide-in-left':
                    element.classList.add('animate-slide-in-left');
                    break;
                case 'slide-in-right':
                    element.classList.add('animate-slide-in-right');
                    break;
                case 'fade-in':
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(30px)';
                    element.style.transition = 'all 0.6s ease-out';
                    requestAnimationFrame(() => {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    });
                    break;
                case 'scale-in':
                    element.style.transform = 'scale(0.8)';
                    element.style.opacity = '0';
                    element.style.transition = 'all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                    requestAnimationFrame(() => {
                        element.style.transform = 'scale(1)';
                        element.style.opacity = '1';
                    });
                    break;
            }
        }, delay);
    }

    /**
     * 📜 Animations de scroll avancées
     */
    initScrollAnimations() {
        let ticking = false;

        const updateScrollAnimations = () => {
            const scrollY = window.scrollY;
            const windowHeight = window.innerHeight;

            // Parallax pour les headers
            document.querySelectorAll('[data-parallax]').forEach(el => {
                const speed = el.dataset.parallax || 0.5;
                const yPos = -(scrollY * speed);
                el.style.transform = `translateY(${yPos}px)`;
            });

            // Progress bar de scroll
            const scrollProgress = (scrollY / (document.body.scrollHeight - windowHeight)) * 100;
            const progressBars = document.querySelectorAll('.scroll-progress');
            progressBars.forEach(bar => {
                bar.style.width = `${Math.min(scrollProgress, 100)}%`;
            });

            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateScrollAnimations);
                ticking = true;
            }
        }, { passive: true });
    }

    /**
     * 📝 Améliorations des formulaires
     */
    initFormEnhancements() {
        // Validation en temps réel
        document.querySelectorAll('.form-input-enterprise').forEach(input => {
            // Animation focus
            input.addEventListener('focus', (e) => {
                e.target.parentElement?.classList.add('focused');
            });

            input.addEventListener('blur', (e) => {
                e.target.parentElement?.classList.remove('focused');
                this.validateField(e.target);
            });

            // Validation au fur et à mesure
            input.addEventListener('input', this.debounce((e) => {
                this.validateField(e.target);
            }, 300));
        });

        // Auto-resize des textareas
        document.querySelectorAll('textarea.form-input-enterprise').forEach(textarea => {
            textarea.addEventListener('input', (e) => {
                e.target.style.height = 'auto';
                e.target.style.height = e.target.scrollHeight + 'px';
            });
        });
    }

    /**
     * ✅ Validation de champ avec feedback visuel
     */
    validateField(field) {
        const isValid = field.checkValidity();
        const parentElement = field.parentElement;

        // Supprimer les classes précédentes
        parentElement?.classList.remove('field-valid', 'field-invalid');

        // Ajouter la classe appropriée
        if (field.value.length > 0) {
            parentElement?.classList.add(isValid ? 'field-valid' : 'field-invalid');
        }

        // Animation de feedback
        if (!isValid && field.value.length > 0) {
            field.classList.add('shake-animation');
            setTimeout(() => field.classList.remove('shake-animation'), 500);
        }
    }

    /**
     * 🧭 Améliorations de navigation
     */
    initNavigationEnhancements() {
        // Highlight de navigation active avec transition fluide
        const navItems = document.querySelectorAll('.nav-item-enterprise');

        navItems.forEach(item => {
            item.addEventListener('mouseenter', (e) => {
                e.target.classList.add('nav-hover');
            });

            item.addEventListener('mouseleave', (e) => {
                e.target.classList.remove('nav-hover');
            });
        });

        // Navigation breadcrumb dynamique
        this.updateBreadcrumb();

        // Menu contextuel intelligent
        this.initContextualMenus();
    }

    /**
     * 🍞 Mise à jour du breadcrumb
     */
    updateBreadcrumb() {
        const breadcrumb = document.querySelector('.breadcrumb-enterprise');
        if (!breadcrumb) return;

        const path = window.location.pathname.split('/').filter(Boolean);
        const breadcrumbHtml = path.map((segment, index) => {
            const href = '/' + path.slice(0, index + 1).join('/');
            const label = this.humanizeString(segment);
            const isLast = index === path.length - 1;

            return `
                <span class="breadcrumb-item ${isLast ? 'active' : ''}">
                    ${isLast ? label : `<a href="${href}" class="text-blue-600 hover:text-blue-800">${label}</a>`}
                    ${!isLast ? '<span class="breadcrumb-separator">/</span>' : ''}
                </span>
            `;
        }).join('');

        breadcrumb.innerHTML = breadcrumbHtml;
    }

    /**
     * 💬 Système de tooltips avancé
     */
    initTooltips() {
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            element.classList.add('tooltip-enterprise');

            // Positionnement intelligent des tooltips
            element.addEventListener('mouseenter', (e) => {
                this.positionTooltip(e.target);
            });
        });
    }

    /**
     * 📍 Positionnement intelligent des tooltips
     */
    positionTooltip(element) {
        const rect = element.getBoundingClientRect();
        const tooltipClass = 'tooltip-top';

        // Logique de positionnement basée sur l'espace disponible
        if (rect.top < 100) {
            element.classList.add('tooltip-bottom');
        } else if (rect.right > window.innerWidth - 200) {
            element.classList.add('tooltip-left');
        } else if (rect.left < 200) {
            element.classList.add('tooltip-right');
        } else {
            element.classList.add('tooltip-top');
        }
    }

    /**
     * ⏳ États de chargement intelligents
     */
    initLoadingStates() {
        // Intercepter les soumissions de formulaires
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    this.setLoadingState(submitButton, true);
                }
            });
        });

        // Intercepter les requêtes AJAX
        this.interceptAjaxRequests();
    }

    /**
     * 🔄 Gestion des états de chargement
     */
    setLoadingState(element, isLoading) {
        if (isLoading) {
            element.classList.add('enterprise-loading');
            element.disabled = true;
            element.dataset.originalText = element.textContent;
        } else {
            element.classList.remove('enterprise-loading');
            element.disabled = false;
            if (element.dataset.originalText) {
                element.textContent = element.dataset.originalText;
            }
        }
    }

    /**
     * 📊 Animations des métriques et statistiques
     */
    initMetricAnimations() {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.5
        };

        const metricsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateMetric(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('[data-metric]').forEach(metric => {
            metricsObserver.observe(metric);
        });

        this.observers.push(metricsObserver);
    }

    /**
     * 📈 Animation d'une métrique spécifique
     */
    animateMetric(element) {
        const targetValue = parseInt(element.dataset.metric) || 0;
        const duration = parseInt(element.dataset.duration) || 2000;
        const startTime = performance.now();

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Fonction d'easing
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const currentValue = Math.floor(targetValue * easeOutQuart);

            element.textContent = this.formatNumber(currentValue);

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    /**
     * 📱 Optimisations responsive
     */
    initResponsiveOptimizations() {
        // Détection du type d'appareil
        const isMobile = window.innerWidth <= 768;
        const isTablet = window.innerWidth <= 1024 && window.innerWidth > 768;

        document.documentElement.classList.toggle('mobile', isMobile);
        document.documentElement.classList.toggle('tablet', isTablet);
        document.documentElement.classList.toggle('desktop', !isMobile && !isTablet);

        // Optimisations spécifiques mobile
        if (isMobile) {
            this.initMobileOptimizations();
        }

        // Gestion du redimensionnement
        window.addEventListener('resize', this.debounce(() => {
            this.initResponsiveOptimizations();
        }, 250));
    }

    /**
     * 📱 Optimisations spécifiques mobile
     */
    initMobileOptimizations() {
        // Désactiver les animations coûteuses sur mobile
        document.querySelectorAll('.enterprise-hover-lift').forEach(el => {
            el.classList.add('mobile-no-hover');
        });

        // Touch gestures
        this.initTouchGestures();
    }

    /**
     * 👆 Gestion des gestes tactiles
     */
    initTouchGestures() {
        let startX, startY, distX, distY;

        document.addEventListener('touchstart', (e) => {
            const touch = e.touches[0];
            startX = touch.pageX;
            startY = touch.pageY;
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (!startX || !startY) return;

            const touch = e.touches[0];
            distX = touch.pageX - startX;
            distY = touch.pageY - startY;
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            if (!startX || !startY) return;

            // Détection des swipes
            if (Math.abs(distX) > Math.abs(distY)) {
                if (distX > 50) {
                    this.triggerSwipe('right');
                } else if (distX < -50) {
                    this.triggerSwipe('left');
                }
            }

            startX = startY = distX = distY = 0;
        }, { passive: true });
    }

    /**
     * ⚡ Optimisations de performance
     */
    initPerformanceOptimizations() {
        // Lazy loading des images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                img.classList.add('lazy');
                imageObserver.observe(img);
            });
        }

        // Préchargement intelligent des ressources
        this.preloadCriticalResources();
    }

    /**
     * 🔄 Interception des requêtes AJAX
     */
    interceptAjaxRequests() {
        const originalFetch = window.fetch;

        window.fetch = function(...args) {
            const button = document.querySelector('.btn-loading');
            if (button) enterpriseUX.setLoadingState(button, true);

            return originalFetch.apply(this, args)
                .finally(() => {
                    if (button) enterpriseUX.setLoadingState(button, false);
                });
        };
    }

    /**
     * 🎯 Menus contextuels intelligents
     */
    initContextualMenus() {
        document.addEventListener('contextmenu', (e) => {
            const target = e.target.closest('[data-context-menu]');
            if (target) {
                e.preventDefault();
                this.showContextMenu(e, target.dataset.contextMenu);
            }
        });
    }

    /**
     * 🎪 Fonctions utilitaires
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    humanizeString(str) {
        return str.replace(/[-_]/g, ' ')
                  .replace(/\b\w/g, l => l.toUpperCase());
    }

    formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }

    triggerSwipe(direction) {
        const event = new CustomEvent('enterpriseSwipe', {
            detail: { direction }
        });
        document.dispatchEvent(event);
    }

    showContextMenu(event, menuType) {
        // Implémentation du menu contextuel
        console.log(`Context menu: ${menuType} at`, event.clientX, event.clientY);
    }

    preloadCriticalResources() {
        // Préchargement intelligent des ressources critiques
        const criticalResources = [
            '/css/enterprise-ux.css',
            '/js/alpine.min.js'
        ];

        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = resource;
            document.head.appendChild(link);
        });
    }

    /**
     * 🧹 Nettoyage des ressources
     */
    destroy() {
        this.observers.forEach(observer => observer.disconnect());
        this.observers = [];
        this.animations.clear();

        if (this.performance.animationFrame) {
            cancelAnimationFrame(this.performance.animationFrame);
        }

        this.isInitialized = false;
        console.log('🧹 Enterprise UX Suite cleaned up');
    }
}

// 🚀 Initialisation automatique
let enterpriseUX;

document.addEventListener('DOMContentLoaded', () => {
    enterpriseUX = new EnterpriseUX();
});

// Export pour utilisation modulaire
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnterpriseUX;
}

// 🎯 Styles CSS additionnels pour les animations
const additionalStyles = `
.shake-animation {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.field-valid {
    border-color: #10b981 !important;
}

.field-invalid {
    border-color: #ef4444 !important;
}

.lazy {
    opacity: 0;
    transition: opacity 0.3s;
}

.lazy.loaded {
    opacity: 1;
}

.mobile-no-hover:hover {
    transform: none !important;
}

.nav-hover {
    background: linear-gradient(90deg, rgba(59, 130, 246, 0.1), rgba(147, 51, 234, 0.1));
}

.scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    z-index: 9999;
    transition: width 0.3s ease;
}
`;

// Injection des styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);