/**
 * Timeline/GANTT JavaScript Module pour ZenFleet
 * Gestion de la vue timeline des affectations de véhicules
 */

class ZenFleetTimeline {
    constructor(config = {}) {
        this.config = {
            currentDate: new Date(),
            currentView: 'month',
            vehicles: [],
            assignments: [],
            apiEndpoints: {
                data: '/admin/assignments/timeline/data',
                search: '/admin/assignments/timeline/search',
                export: '/admin/assignments/timeline/export'
            },
            selectors: {
                container: '#timelineContainer',
                header: '#timelineHeader',
                body: '#timelineBody',
                vehicleRows: '#vehicleRows',
                timeColumns: '#timeColumns',
                currentPeriod: '#currentPeriod',
                viewSelector: '#viewSelector',
                searchInput: '#searchInput',
                prevPeriod: '#prevPeriod',
                nextPeriod: '#nextPeriod',
                todayBtn: '#todayBtn',
                modal: '#assignmentModal',
                modalContent: '#modalContent',
                tooltip: '#assignmentTooltip',
                tooltipContent: '#tooltipContent'
            },
            monthNames: [
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ],
            dayNames: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            colors: {
                active: 'timeline-assignment-bar-active',
                completed: 'timeline-assignment-bar-completed',
                upcoming: 'timeline-assignment-bar-upcoming'
            },
            ...config
        };

        this.isLoading = false;
        this.searchTimeout = null;
        this.currentTooltip = null;

        this.init();
    }

    /**
     * Initialisation de la timeline
     */
    init() {
        this.bindEvents();
        this.loadData();
    }

    /**
     * Liaison des événements
     */
    bindEvents() {
        // Navigation temporelle
        document.querySelector(this.config.selectors.prevPeriod)?.addEventListener('click', () => {
            this.navigatePeriod(-1);
        });

        document.querySelector(this.config.selectors.nextPeriod)?.addEventListener('click', () => {
            this.navigatePeriod(1);
        });

        document.querySelector(this.config.selectors.todayBtn)?.addEventListener('click', () => {
            this.goToToday();
        });

        // Changement de vue
        document.querySelector(this.config.selectors.viewSelector)?.addEventListener('change', (e) => {
            this.changeView(e.target.value);
        });

        // Recherche
        document.querySelector(this.config.selectors.searchInput)?.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });

        // Fermeture de la modal
        document.querySelector(this.config.selectors.modal + ' [data-close]')?.addEventListener('click', () => {
            this.closeModal();
        });

        // Fermeture de la modal en cliquant à l'extérieur
        document.querySelector(this.config.selectors.modal)?.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                this.closeModal();
            }
        });

        // Gestion du redimensionnement
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 250));

        // Gestion du clavier
        document.addEventListener('keydown', (e) => {
            this.handleKeyboard(e);
        });
    }

    /**
     * Navigation dans les périodes
     */
    navigatePeriod(direction) {
        const currentDate = new Date(this.config.currentDate);

        switch (this.config.currentView) {
            case 'month':
                currentDate.setMonth(currentDate.getMonth() + direction);
                break;
            case 'week':
                currentDate.setDate(currentDate.getDate() + (direction * 7));
                break;
            case 'day':
                currentDate.setDate(currentDate.getDate() + direction);
                break;
        }

        this.config.currentDate = currentDate;
        this.loadData();
    }

    /**
     * Aller à aujourd'hui
     */
    goToToday() {
        this.config.currentDate = new Date();
        this.loadData();
    }

    /**
     * Changer de vue
     */
    changeView(view) {
        if (['month', 'week', 'day'].includes(view)) {
            this.config.currentView = view;
            this.loadData();
        }
    }

    /**
     * Gestion de la recherche avec debounce
     */
    handleSearch(query) {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.search(query);
        }, 300);
    }

    /**
     * Recherche dans les affectations
     */
    async search(query) {
        if (this.isLoading) return;

        try {
            this.setLoading(true);

            const params = new URLSearchParams({
                q: query,
                view: this.config.currentView,
                date: this.formatDate(this.config.currentDate)
            });

            const response = await fetch(`${this.config.apiEndpoints.search}?${params}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            this.updateTimeline(data);

        } catch (error) {
            console.error('Erreur lors de la recherche:', error);
            this.showError('Erreur lors de la recherche');
        } finally {
            this.setLoading(false);
        }
    }

    /**
     * Chargement des données
     */
    async loadData() {
        if (this.isLoading) return;

        try {
            this.setLoading(true);

            const params = new URLSearchParams({
                view: this.config.currentView,
                date: this.formatDate(this.config.currentDate)
            });

            const response = await fetch(`${this.config.apiEndpoints.data}?${params}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            this.updateTimeline(data);

        } catch (error) {
            console.error('Erreur lors du chargement:', error);
            this.showError('Erreur lors du chargement des données');
        } finally {
            this.setLoading(false);
        }
    }

    /**
     * Mise à jour de la timeline
     */
    updateTimeline(data) {
        this.config.vehicles = data.vehicles || [];
        this.config.assignments = data.assignments || [];

        this.renderTimelineHeader(data.timeColumns || []);
        this.renderVehicleRows();
        this.updatePeriodTitle();
        this.updateStats(data.stats);
    }

    /**
     * Rendu de l'en-tête de la timeline
     */
    renderTimelineHeader(timeColumns) {
        const timeColumnsContainer = document.querySelector(this.config.selectors.timeColumns);
        if (!timeColumnsContainer) return;

        timeColumnsContainer.innerHTML = '';

        timeColumns.forEach(column => {
            const colDiv = document.createElement('div');
            colDiv.className = 'timeline-time-column';

            if (column.isToday || column.isCurrentHour) {
                colDiv.classList.add('timeline-time-column-current');
            }

            if (column.isWeekend) {
                colDiv.classList.add('timeline-time-column-weekend');
            }

            colDiv.innerHTML = `
                <div class="timeline-time-column-label">${column.label}</div>
                <div class="timeline-time-column-value">${column.value}</div>
            `;

            timeColumnsContainer.appendChild(colDiv);
        });
    }

    /**
     * Rendu des lignes de véhicules
     */
    renderVehicleRows() {
        const vehicleRowsContainer = document.querySelector(this.config.selectors.vehicleRows);
        if (!vehicleRowsContainer) return;

        vehicleRowsContainer.innerHTML = '';

        if (this.config.vehicles.length === 0) {
            this.renderEmptyState();
            return;
        }

        this.config.vehicles.forEach((vehicle, index) => {
            const row = this.createVehicleRow(vehicle, index);
            vehicleRowsContainer.appendChild(row);
        });
    }

    /**
     * Création d'une ligne de véhicule
     */
    createVehicleRow(vehicle, index) {
        const row = document.createElement('div');
        row.className = 'timeline-vehicle-row';
        row.setAttribute('data-vehicle-id', vehicle.id);

        // Colonne véhicule
        const vehicleCell = document.createElement('div');
        vehicleCell.className = 'timeline-vehicle-cell';
        vehicleCell.innerHTML = this.renderVehicleInfo(vehicle);

        // Zone des affectations
        const assignmentsZone = document.createElement('div');
        assignmentsZone.className = 'timeline-assignments-zone';

        // Ajouter les barres d'affectation
        const vehicleAssignments = this.getVehicleAssignments(vehicle.id);
        vehicleAssignments.forEach(assignment => {
            const bar = this.createAssignmentBar(assignment);
            assignmentsZone.appendChild(bar);
        });

        row.appendChild(vehicleCell);
        row.appendChild(assignmentsZone);

        return row;
    }

    /**
     * Rendu des informations du véhicule
     */
    renderVehicleInfo(vehicle) {
        const status = this.getVehicleStatus(vehicle);
        const statusClass = status === 'Affecté' ? 'timeline-vehicle-status-assigned' : 'timeline-vehicle-status-available';

        return `
            <div class="timeline-vehicle-info">
                <div class="timeline-vehicle-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                    </svg>
                </div>
                <div class="timeline-vehicle-details">
                    <div class="timeline-vehicle-name">${vehicle.brand} ${vehicle.model}</div>
                    <div class="timeline-vehicle-plate">${vehicle.registration_plate}</div>
                    <div class="timeline-vehicle-status">
                        <span class="timeline-vehicle-status-badge ${statusClass}">
                            ${status}
                        </span>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Création d'une barre d'affectation
     */
    createAssignmentBar(assignment) {
        const bar = document.createElement('div');
        const position = this.calculateBarPosition(assignment);
        const status = this.getAssignmentStatus(assignment);

        bar.className = `timeline-assignment-bar ${this.config.colors[status]}`;
        bar.style.left = position.left + '%';
        bar.style.width = position.width + '%';
        bar.setAttribute('data-assignment-id', assignment.id);
        bar.setAttribute('tabindex', '0');
        bar.setAttribute('role', 'button');
        bar.setAttribute('aria-label', `Affectation de ${assignment.driver?.first_name} ${assignment.driver?.last_name}`);

        bar.innerHTML = `
            <div class="timeline-assignment-bar-content">
                ${assignment.driver?.first_name} ${assignment.driver?.last_name}
            </div>
        `;

        // Événements
        bar.addEventListener('mouseenter', (e) => this.showTooltip(e, assignment));
        bar.addEventListener('mouseleave', () => this.hideTooltip());
        bar.addEventListener('click', () => this.showAssignmentDetails(assignment));
        bar.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.showAssignmentDetails(assignment);
            }
        });

        return bar;
    }

    /**
     * Calcul de la position de la barre
     */
    calculateBarPosition(assignment) {
        const startDate = new Date(assignment.start_datetime);
        const endDate = assignment.end_datetime ? new Date(assignment.end_datetime) : new Date();

        let left = 0;
        let width = 0;

        switch (this.config.currentView) {
            case 'month':
                const monthStart = new Date(this.config.currentDate.getFullYear(), this.config.currentDate.getMonth(), 1);
                const monthEnd = new Date(this.config.currentDate.getFullYear(), this.config.currentDate.getMonth() + 1, 0);
                const daysInMonth = monthEnd.getDate();

                const startDay = Math.max(1, startDate.getDate());
                const endDay = Math.min(daysInMonth, endDate.getDate());

                left = ((startDay - 1) / daysInMonth) * 100;
                width = ((endDay - startDay + 1) / daysInMonth) * 100;
                break;

            case 'week':
                const weekStart = this.getStartOfWeek(this.config.currentDate);
                const dayOfWeek = Math.floor((startDate - weekStart) / (1000 * 60 * 60 * 24));
                const duration = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));

                left = (dayOfWeek / 7) * 100;
                width = (Math.min(duration, 7 - dayOfWeek) / 7) * 100;
                break;

            case 'day':
                const startHour = startDate.getHours();
                const endHour = endDate.getHours();
                const totalHours = 17; // 6h à 22h

                left = ((Math.max(6, startHour) - 6) / totalHours) * 100;
                width = ((Math.min(22, endHour) - Math.max(6, startHour)) / totalHours) * 100;
                break;
        }

        return {
            left: Math.max(0, left),
            width: Math.max(2, width)
        };
    }

    /**
     * Affichage du tooltip
     */
    showTooltip(event, assignment) {
        const tooltip = document.querySelector(this.config.selectors.tooltip);
        const tooltipContent = document.querySelector(this.config.selectors.tooltipContent);

        if (!tooltip || !tooltipContent) return;

        const startDate = new Date(assignment.start_datetime);
        const endDate = assignment.end_datetime ? new Date(assignment.end_datetime) : null;
        const status = this.getAssignmentStatus(assignment);

        tooltipContent.innerHTML = `
            <div class="timeline-tooltip-content">
                <div class="timeline-tooltip-title">${assignment.driver?.first_name} ${assignment.driver?.last_name}</div>
                <div class="timeline-tooltip-detail">
                    Début: ${startDate.toLocaleDateString('fr-FR')} ${startDate.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}
                </div>
                <div class="timeline-tooltip-detail">
                    Fin: ${endDate ? endDate.toLocaleDateString('fr-FR') + ' ' + endDate.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'}) : 'En cours'}
                </div>
                ${assignment.reason ? `<div class="timeline-tooltip-detail">Motif: ${assignment.reason}</div>` : ''}
                <div class="timeline-tooltip-status">
                    <div class="timeline-tooltip-status-dot timeline-tooltip-status-dot-${status}"></div>
                    <span>${this.getStatusLabel(status)}</span>
                </div>
            </div>
        `;

        // Positionnement
        const rect = event.target.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) + 'px';
        tooltip.style.top = rect.top - 10 + 'px';
        tooltip.classList.add('show');

        this.currentTooltip = tooltip;
    }

    /**
     * Masquage du tooltip
     */
    hideTooltip() {
        if (this.currentTooltip) {
            this.currentTooltip.classList.remove('show');
            this.currentTooltip = null;
        }
    }

    /**
     * Affichage des détails d'affectation
     */
    showAssignmentDetails(assignment) {
        const modal = document.querySelector(this.config.selectors.modal);
        const modalContent = document.querySelector(this.config.selectors.modalContent);

        if (!modal || !modalContent) return;

        modalContent.innerHTML = this.renderAssignmentDetails(assignment);
        modal.classList.remove('hidden');

        // Focus sur la modal pour l'accessibilité
        modal.focus();
    }

    /**
     * Rendu des détails d'affectation
     */
    renderAssignmentDetails(assignment) {
        const status = this.getAssignmentStatus(assignment);
        const statusClass = status === 'active' ? 'bg-primary-100 text-primary-800' :
                           status === 'completed' ? 'bg-success-100 text-success-800' :
                           'bg-warning-100 text-warning-800';

        return `
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        ${assignment.driver?.photo_path ?
                            `<img class="h-16 w-16 rounded-full object-cover border-2 border-gray-200" src="/storage/${assignment.driver.photo_path}" alt="Photo">` :
                            `<div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-200">
                                <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>`
                        }
                    </div>
                    <div>
                        <h4 class="text-xl font-semibold text-gray-900">${assignment.driver?.first_name} ${assignment.driver?.last_name}</h4>
                        <p class="text-gray-600">${assignment.driver?.personal_phone || ''}</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
                                ${this.getStatusLabel(status)}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                                </svg>
                                Véhicule
                            </h5>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p><span class="font-medium">Modèle:</span> ${assignment.vehicle?.brand} ${assignment.vehicle?.model}</p>
                                <p><span class="font-medium">Immatriculation:</span> ${assignment.vehicle?.registration_plate}</p>
                                <p><span class="font-medium">Kilométrage:</span> ${assignment.vehicle?.current_mileage?.toLocaleString()} km</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                Période d'affectation
                            </h5>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p><span class="font-medium">Début:</span> ${new Date(assignment.start_datetime).toLocaleString('fr-FR')}</p>
                                <p><span class="font-medium">Fin:</span> ${assignment.end_datetime ? new Date(assignment.end_datetime).toLocaleString('fr-FR') : 'En cours'}</p>
                                ${assignment.reason ? `<p><span class="font-medium">Motif:</span> ${assignment.reason}</p>` : ''}
                            </div>
                        </div>
                    </div>
                </div>

                ${assignment.notes ? `
                    <div>
                        <h5 class="font-semibold text-gray-900 mb-3">Notes</h5>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700">${assignment.notes}</p>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    }

    /**
     * Fermeture de la modal
     */
    closeModal() {
        const modal = document.querySelector(this.config.selectors.modal);
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    /**
     * Rendu de l'état vide
     */
    renderEmptyState() {
        const vehicleRowsContainer = document.querySelector(this.config.selectors.vehicleRows);
        if (!vehicleRowsContainer) return;

        vehicleRowsContainer.innerHTML = `
            <div class="timeline-empty">
                <svg class="timeline-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <div class="timeline-empty-title">Aucune affectation trouvée</div>
                <div class="timeline-empty-description">
                    Il n'y a aucune affectation pour la période sélectionnée.
                </div>
            </div>
        `;
    }

    /**
     * Mise à jour du titre de la période
     */
    updatePeriodTitle() {
        const currentPeriodElement = document.querySelector(this.config.selectors.currentPeriod);
        if (!currentPeriodElement) return;

        let title = '';

        switch (this.config.currentView) {
            case 'month':
                title = `${this.config.monthNames[this.config.currentDate.getMonth()]} ${this.config.currentDate.getFullYear()}`;
                break;
            case 'week':
                const startOfWeek = this.getStartOfWeek(this.config.currentDate);
                const endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6);
                title = `Semaine du ${startOfWeek.getDate()} au ${endOfWeek.getDate()} ${this.config.monthNames[startOfWeek.getMonth()]} ${startOfWeek.getFullYear()}`;
                break;
            case 'day':
                title = `${this.config.currentDate.getDate()} ${this.config.monthNames[this.config.currentDate.getMonth()]} ${this.config.currentDate.getFullYear()}`;
                break;
        }

        currentPeriodElement.textContent = title;
    }

    /**
     * Mise à jour des statistiques
     */
    updateStats(stats) {
        if (!stats) return;

        // Mise à jour des compteurs dans l'interface
        const activeElement = document.querySelector('[data-stat="active"]');
        const completedElement = document.querySelector('[data-stat="completed"]');
        const upcomingElement = document.querySelector('[data-stat="upcoming"]');

        if (activeElement) activeElement.textContent = stats.active;
        if (completedElement) completedElement.textContent = stats.completed;
        if (upcomingElement) upcomingElement.textContent = stats.upcoming;
    }

    /**
     * Gestion du redimensionnement
     */
    handleResize() {
        // Recalculer les positions si nécessaire
        this.renderVehicleRows();
    }

    /**
     * Gestion du clavier
     */
    handleKeyboard(event) {
        if (event.target.closest(this.config.selectors.modal)) {
            if (event.key === 'Escape') {
                this.closeModal();
            }
            return;
        }

        switch (event.key) {
            case 'ArrowLeft':
                if (event.ctrlKey || event.metaKey) {
                    event.preventDefault();
                    this.navigatePeriod(-1);
                }
                break;
            case 'ArrowRight':
                if (event.ctrlKey || event.metaKey) {
                    event.preventDefault();
                    this.navigatePeriod(1);
                }
                break;
            case 'Home':
                if (event.ctrlKey || event.metaKey) {
                    event.preventDefault();
                    this.goToToday();
                }
                break;
        }
    }

    /**
     * Utilitaires
     */
    getVehicleAssignments(vehicleId) {
        return this.config.assignments.filter(assignment => assignment.vehicle_id === vehicleId);
    }

    getAssignmentStatus(assignment) {
        if (assignment.end_datetime) {
            return 'completed';
        }

        const now = new Date();
        const startDate = new Date(assignment.start_datetime);

        return startDate > now ? 'upcoming' : 'active';
    }

    getVehicleStatus(vehicle) {
        const activeAssignments = this.config.assignments.filter(a =>
            a.vehicle_id === vehicle.id && !a.end_datetime
        );

        return activeAssignments.length > 0 ? 'Affecté' : 'Disponible';
    }

    getStatusLabel(status) {
        const labels = {
            active: 'En cours',
            completed: 'Terminée',
            upcoming: 'À venir'
        };
        return labels[status] || status;
    }

    getStartOfWeek(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1);
        return new Date(d.setDate(diff));
    }

    formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    setLoading(loading) {
        this.isLoading = loading;

        const container = document.querySelector(this.config.selectors.container);
        if (container) {
            if (loading) {
                container.classList.add('timeline-loading');
            } else {
                container.classList.remove('timeline-loading');
            }
        }
    }

    showError(message) {
        // Affichage d'erreur simple - peut être amélioré avec un système de notifications
        console.error(message);
        alert(message);
    }

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

    /**
     * Export des données
     */
    async exportData(format = 'csv') {
        try {
            const params = new URLSearchParams({
                view: this.config.currentView,
                date: this.formatDate(this.config.currentDate),
                format: format
            });

            const response = await fetch(`${this.config.apiEndpoints.export}?${params}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `affectations-${this.config.currentView}-${this.formatDate(this.config.currentDate)}.${format}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

        } catch (error) {
            console.error('Erreur lors de l\'export:', error);
            this.showError('Erreur lors de l\'export');
        }
    }

    /**
     * Destruction de l'instance
     */
    destroy() {
        // Nettoyage des événements et timers
        clearTimeout(this.searchTimeout);
        this.hideTooltip();
        this.closeModal();
    }
}

// Export pour utilisation en module
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ZenFleetTimeline;
}

// Initialisation automatique si le DOM est prêt
if (typeof window !== 'undefined') {
    window.ZenFleetTimeline = ZenFleetTimeline;

    // Auto-initialisation si l'élément timeline existe
    document.addEventListener('DOMContentLoaded', function() {
        const timelineContainer = document.querySelector('#timelineContainer');
        if (timelineContainer && !window.zenfleetTimelineInstance) {
            window.zenfleetTimelineInstance = new ZenFleetTimeline();
        }
    });
}
