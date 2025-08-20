<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Affectations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Barre d'outils --}}
            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    
                    {{-- Section gauche : Titre et bouton d'ajout --}}
                    <div class="flex items-center space-x-4">
                        <h3 class="text-xl font-semibold text-gray-700 flex items-center">
                            Affectations
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $totalAssignments }}
                            </span>
                        </h3>
                        @can('create assignments')
                            <a href="{{ route('admin.assignments.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700">
                                <x-lucide-plus class="w-4 h-4 mr-2"/>
                                Nouvelle Affectation
                            </a>
                        @endcan
                    </div>

                    {{-- Section droite : Contrôles --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        
                        {{-- Recherche --}}
                        <div class="relative">
                            <input type="text" id="search" placeholder="Rechercher..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 text-sm">
                            <x-lucide-search class="absolute left-3 top-2.5 h-4 w-4 text-gray-400"/>
                        </div>

                        {{-- Filtres --}}
                        <button id="filtersBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <x-lucide-filter class="w-4 h-4 mr-2"/>
                            Filtres
                        </button>

                        {{-- Navigation calendaire --}}
                        <div class="flex items-center space-x-2">
                            <button id="prevMonth" class="p-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                <x-lucide-chevron-left class="w-4 h-4"/>
                            </button>
                            
                            <button id="todayBtn" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Aujourd'hui
                            </button>
                            
                            <button id="nextMonth" class="p-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                <x-lucide-chevron-right class="w-4 h-4"/>
                            </button>
                        </div>

                        {{-- Sélecteur de vue --}}
                        <select id="viewSelector" class="border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <option value="month">Mois</option>
                            <option value="week">Semaine</option>
                            <option value="day">Jour</option>
                        </select>

                        {{-- Bouton vue tableau --}}
                        <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <x-lucide-table class="w-4 h-4 mr-2"/>
                            Vue Tableau
                        </a>
                    </div>
                </div>
            </div>

            {{-- Calendrier --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    {{-- En-tête du calendrier --}}
                    <div class="flex items-center justify-between mb-6">
                        <h4 id="currentMonth" class="text-2xl font-bold text-gray-900"></h4>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">Légende:</span>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                    <span class="text-xs text-gray-600">En cours</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                                    <span class="text-xs text-gray-600">Terminée</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                    <span class="text-xs text-gray-600">À venir</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Grille du calendrier --}}
                    <div id="calendar" class="grid grid-cols-7 gap-1">
                        {{-- En-têtes des jours --}}
                        <div class="p-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Lun</div>
                        <div class="p-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Mar</div>
                        <div class="p-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Mer</div>
                        <div class="p-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Jeu</div>
                        <div class="p-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Ven</div>
                        <div class="p-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Sam</div>
                        <div class="p-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Dim</div>
                        
                        {{-- Les cellules du calendrier seront générées par JavaScript --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal pour les détails d'affectation --}}
    <div id="assignmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Détails de l'affectation
                            </h3>
                            <div class="mt-4" id="modalContent">
                                {{-- Contenu dynamique --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Configuration du calendrier
        const calendarConfig = {
            currentDate: new Date(),
            assignments: @json($assignments),
            monthNames: [
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ]
        };

        // Initialisation du calendrier
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
            bindEvents();
        });

        function initializeCalendar() {
            renderCalendar();
        }

        function renderCalendar() {
            const calendar = document.getElementById('calendar');
            const currentMonthElement = document.getElementById('currentMonth');
            
            // Mise à jour du titre du mois
            currentMonthElement.textContent = `${calendarConfig.monthNames[calendarConfig.currentDate.getMonth()]} ${calendarConfig.currentDate.getFullYear()}`;
            
            // Calcul des jours du mois
            const year = calendarConfig.currentDate.getFullYear();
            const month = calendarConfig.currentDate.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1));
            
            // Suppression des cellules existantes (sauf les en-têtes)
            const existingCells = calendar.querySelectorAll('.calendar-cell');
            existingCells.forEach(cell => cell.remove());
            
            // Génération des cellules
            for (let i = 0; i < 42; i++) {
                const cellDate = new Date(startDate);
                cellDate.setDate(startDate.getDate() + i);
                
                const cell = createCalendarCell(cellDate, month);
                calendar.appendChild(cell);
            }
        }

        function createCalendarCell(date, currentMonth) {
            const cell = document.createElement('div');
            cell.className = 'calendar-cell min-h-[120px] p-2 border border-gray-200 bg-white hover:bg-gray-50';
            
            const isCurrentMonth = date.getMonth() === currentMonth;
            const isToday = isDateToday(date);
            
            if (!isCurrentMonth) {
                cell.classList.add('bg-gray-50', 'text-gray-400');
            }
            
            if (isToday) {
                cell.classList.add('bg-blue-50', 'border-blue-300');
            }
            
            // Numéro du jour
            const dayNumber = document.createElement('div');
            dayNumber.className = `text-sm font-semibold mb-2 ${isToday ? 'text-blue-600' : 'text-gray-900'}`;
            dayNumber.textContent = date.getDate();
            cell.appendChild(dayNumber);
            
            // Affectations pour ce jour
            const dayAssignments = getAssignmentsForDate(date);
            dayAssignments.forEach(assignment => {
                const assignmentCard = createAssignmentCard(assignment);
                cell.appendChild(assignmentCard);
            });
            
            return cell;
        }

        function createAssignmentCard(assignment) {
            const card = document.createElement('div');
            card.className = 'mb-1 p-2 rounded-md text-xs cursor-pointer transition-colors duration-200';
            
            // Couleur selon le statut
            if (assignment.end_datetime) {
                card.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            } else {
                card.classList.add('bg-green-100', 'text-green-800', 'hover:bg-green-200');
            }
            
            card.innerHTML = `
                <div class="font-semibold truncate">${assignment.vehicle?.brand} ${assignment.vehicle?.model}</div>
                <div class="text-gray-600 truncate">${assignment.vehicle?.registration_plate}</div>
                <div class="text-gray-600 truncate">${assignment.driver?.first_name} ${assignment.driver?.last_name}</div>
            `;
            
            card.addEventListener('click', () => showAssignmentDetails(assignment));
            
            return card;
        }

        function getAssignmentsForDate(date) {
            return calendarConfig.assignments.filter(assignment => {
                const startDate = new Date(assignment.start_datetime);
                const endDate = assignment.end_datetime ? new Date(assignment.end_datetime) : new Date();
                
                return date >= startDate.setHours(0,0,0,0) && date <= endDate.setHours(23,59,59,999);
            });
        }

        function isDateToday(date) {
            const today = new Date();
            return date.toDateString() === today.toDateString();
        }

        function showAssignmentDetails(assignment) {
            const modal = document.getElementById('assignmentModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            ${assignment.driver?.photo_path ? 
                                `<img class="h-12 w-12 rounded-full object-cover" src="/storage/${assignment.driver.photo_path}" alt="Photo">` :
                                `<div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>`
                            }
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold">${assignment.driver?.first_name} ${assignment.driver?.last_name}</h4>
                            <p class="text-gray-600">${assignment.driver?.personal_phone || ''}</p>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <h5 class="font-semibold mb-2">Véhicule</h5>
                        <p><strong>Modèle:</strong> ${assignment.vehicle?.brand} ${assignment.vehicle?.model}</p>
                        <p><strong>Immatriculation:</strong> ${assignment.vehicle?.registration_plate}</p>
                        <p><strong>Kilométrage actuel:</strong> ${assignment.vehicle?.current_mileage?.toLocaleString()} km</p>
                    </div>
                    
                    <div class="border-t pt-4">
                        <h5 class="font-semibold mb-2">Période d'affectation</h5>
                        <p><strong>Début:</strong> ${new Date(assignment.start_datetime).toLocaleString('fr-FR')}</p>
                        <p><strong>Fin:</strong> ${assignment.end_datetime ? new Date(assignment.end_datetime).toLocaleString('fr-FR') : 'En cours'}</p>
                        <p><strong>Statut:</strong> 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${assignment.end_datetime ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800'}">
                                ${assignment.end_datetime ? 'Terminée' : 'En cours'}
                            </span>
                        </p>
                    </div>
                    
                    ${assignment.reason ? `
                        <div class="border-t pt-4">
                            <h5 class="font-semibold mb-2">Motif</h5>
                            <p class="text-gray-700">${assignment.reason}</p>
                        </div>
                    ` : ''}
                    
                    ${assignment.notes ? `
                        <div class="border-t pt-4">
                            <h5 class="font-semibold mb-2">Notes</h5>
                            <p class="text-gray-700">${assignment.notes}</p>
                        </div>
                    ` : ''}
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function bindEvents() {
            // Navigation du calendrier
            document.getElementById('prevMonth').addEventListener('click', () => {
                calendarConfig.currentDate.setMonth(calendarConfig.currentDate.getMonth() - 1);
                renderCalendar();
            });
            
            document.getElementById('nextMonth').addEventListener('click', () => {
                calendarConfig.currentDate.setMonth(calendarConfig.currentDate.getMonth() + 1);
                renderCalendar();
            });
            
            document.getElementById('todayBtn').addEventListener('click', () => {
                calendarConfig.currentDate = new Date();
                renderCalendar();
            });
            
            // Fermeture de la modal
            document.getElementById('closeModal').addEventListener('click', () => {
                document.getElementById('assignmentModal').classList.add('hidden');
            });
            
            // Fermeture de la modal en cliquant à l'extérieur
            document.getElementById('assignmentModal').addEventListener('click', (e) => {
                if (e.target === e.currentTarget) {
                    e.currentTarget.classList.add('hidden');
                }
            });
        }
    </script>
    @endpush
</x-app-layout>

