{{-- Vue Calendrier Intégrée --}}
<div x-data="calendarComponent()" x-init="init()" class="calendar-container">
    
    {{-- Contrôles du calendrier --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-4 sm:space-y-0">
        
        {{-- Navigation du mois --}}
        <div class="flex items-center space-x-4">
            <button @click="previousMonth()" class="p-2 rounded-md border border-gray-300 hover:bg-gray-50 transition-colors duration-200">
                <x-lucide-chevron-left class="w-5 h-5 text-gray-600"/>
            </button>
            
            <h4 x-text="currentMonthYear" class="text-xl font-semibold text-gray-900 min-w-[200px] text-center"></h4>
            
            <button @click="nextMonth()" class="p-2 rounded-md border border-gray-300 hover:bg-gray-50 transition-colors duration-200">
                <x-lucide-chevron-right class="w-5 h-5 text-gray-600"/>
            </button>
        </div>

        {{-- Contrôles de vue et actions --}}
        <div class="flex items-center space-x-3">
            <button @click="goToToday()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                Aujourd'hui
            </button>
            
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Légende:</span>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
                        <span class="text-xs text-gray-600">En cours</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-gray-400 rounded-full mr-1"></div>
                        <span class="text-xs text-gray-600">Terminée</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grille du calendrier --}}
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        
        {{-- En-têtes des jours de la semaine --}}
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Lun</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Mar</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Mer</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Jeu</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Ven</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Sam</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Dim</div>
        </div>

        {{-- Cellules du calendrier --}}
        <div class="grid grid-cols-7">
            <template x-for="(week, weekIndex) in calendarWeeks" :key="weekIndex">
                <template x-for="(day, dayIndex) in week" :key="`${weekIndex}-${dayIndex}`">
                    <div class="min-h-[120px] border-r border-b border-gray-200 p-2 bg-white hover:bg-gray-50 transition-colors duration-200"
                         :class="{
                             'bg-gray-50 text-gray-400': !day.isCurrentMonth,
                             'bg-blue-50 border-blue-300': day.isToday,
                             'bg-white': day.isCurrentMonth && !day.isToday
                         }">
                        
                        {{-- Numéro du jour --}}
                        <div class="text-sm font-semibold mb-2"
                             :class="{
                                 'text-blue-600': day.isToday,
                                 'text-gray-900': day.isCurrentMonth && !day.isToday,
                                 'text-gray-400': !day.isCurrentMonth
                             }"
                             x-text="day.date.getDate()">
                        </div>

                        {{-- Affectations du jour --}}
                        <div class="space-y-1">
                            <template x-for="assignment in day.assignments" :key="assignment.id">
                                <div @click="showAssignmentDetails(assignment)"
                                     class="p-2 rounded-md text-xs cursor-pointer transition-all duration-200 hover:shadow-sm"
                                     :class="{
                                         'bg-green-100 text-green-800 hover:bg-green-200': !assignment.end_datetime,
                                         'bg-gray-100 text-gray-700 hover:bg-gray-200': assignment.end_datetime
                                     }">
                                    <div class="font-semibold truncate" x-text="`${assignment.vehicle?.brand} ${assignment.vehicle?.model}`"></div>
                                    <div class="text-gray-600 truncate" x-text="assignment.vehicle?.registration_plate"></div>
                                    <div class="text-gray-600 truncate" x-text="`${assignment.driver?.first_name} ${assignment.driver?.last_name}`"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </template>
        </div>
    </div>

    {{-- Modal de détails d'affectation --}}
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Détails de l'affectation
                            </h3>
                            
                            <div x-show="selectedAssignment" class="space-y-4">
                                {{-- Informations du conducteur --}}
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <template x-if="selectedAssignment?.driver?.photo_path">
                                            <img :src="`/storage/${selectedAssignment.driver.photo_path}`" 
                                                 class="h-12 w-12 rounded-full object-cover" 
                                                 :alt="`Photo de ${selectedAssignment.driver?.first_name}`">
                                        </template>
                                        <template x-if="!selectedAssignment?.driver?.photo_path">
                                            <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                                                <x-lucide-user class="h-6 w-6 text-gray-400"/>
                                            </div>
                                        </template>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold" x-text="`${selectedAssignment?.driver?.first_name} ${selectedAssignment?.driver?.last_name}`"></h4>
                                        <p class="text-gray-600" x-text="selectedAssignment?.driver?.personal_phone || ''"></p>
                                    </div>
                                </div>
                                
                                {{-- Informations du véhicule --}}
                                <div class="border-t pt-4">
                                    <h5 class="font-semibold mb-2">Véhicule</h5>
                                    <p><strong>Modèle:</strong> <span x-text="`${selectedAssignment?.vehicle?.brand} ${selectedAssignment?.vehicle?.model}`"></span></p>
                                    <p><strong>Immatriculation:</strong> <span x-text="selectedAssignment?.vehicle?.registration_plate"></span></p>
                                    <p><strong>Kilométrage:</strong> <span x-text="selectedAssignment?.vehicle?.current_mileage?.toLocaleString()"></span> km</p>
                                </div>
                                
                                {{-- Période d'affectation --}}
                                <div class="border-t pt-4">
                                    <h5 class="font-semibold mb-2">Période d'affectation</h5>
                                    <p><strong>Début:</strong> <span x-text="formatDateTime(selectedAssignment?.start_datetime)"></span></p>
                                    <p><strong>Fin:</strong> <span x-text="selectedAssignment?.end_datetime ? formatDateTime(selectedAssignment.end_datetime) : 'En cours'"></span></p>
                                    <p><strong>Statut:</strong> 
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="selectedAssignment?.end_datetime ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800'"
                                              x-text="selectedAssignment?.end_datetime ? 'Terminée' : 'En cours'">
                                        </span>
                                    </p>
                                </div>
                                
                                {{-- Motif et notes --}}
                                <template x-if="selectedAssignment?.reason">
                                    <div class="border-t pt-4">
                                        <h5 class="font-semibold mb-2">Motif</h5>
                                        <p class="text-gray-700" x-text="selectedAssignment.reason"></p>
                                    </div>
                                </template>
                                
                                <template x-if="selectedAssignment?.notes">
                                    <div class="border-t pt-4">
                                        <h5 class="font-semibold mb-2">Notes</h5>
                                        <p class="text-gray-700" x-text="selectedAssignment.notes"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="closeModal()" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calendarComponent() {
    return {
        currentDate: new Date(),
        assignments: @json($assignments),
        calendarWeeks: [],
        currentMonthYear: '',
        showModal: false,
        selectedAssignment: null,
        monthNames: [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ],

        init() {
            this.generateCalendar();
        },

        generateCalendar() {
            const year = this.currentDate.getFullYear();
            const month = this.currentDate.getMonth();
            
            // Mise à jour du titre
            this.currentMonthYear = `${this.monthNames[month]} ${year}`;
            
            // Calcul du premier jour du mois et du nombre de jours
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            
            // Calcul du premier jour à afficher (lundi de la première semaine)
            const startDate = new Date(firstDay);
            const dayOfWeek = firstDay.getDay();
            const daysToSubtract = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
            startDate.setDate(firstDay.getDate() - daysToSubtract);
            
            // Génération des semaines
            this.calendarWeeks = [];
            let currentWeekDate = new Date(startDate);
            
            for (let week = 0; week < 6; week++) {
                const weekDays = [];
                
                for (let day = 0; day < 7; day++) {
                    const dayDate = new Date(currentWeekDate);
                    const dayAssignments = this.getAssignmentsForDate(dayDate);
                    
                    weekDays.push({
                        date: dayDate,
                        isCurrentMonth: dayDate.getMonth() === month,
                        isToday: this.isToday(dayDate),
                        assignments: dayAssignments
                    });
                    
                    currentWeekDate.setDate(currentWeekDate.getDate() + 1);
                }
                
                this.calendarWeeks.push(weekDays);
            }
        },

        getAssignmentsForDate(date) {
            return this.assignments.filter(assignment => {
                const startDate = new Date(assignment.start_datetime);
                const endDate = assignment.end_datetime ? new Date(assignment.end_datetime) : new Date();
                
                // Normalisation des dates pour la comparaison (sans heures)
                const checkDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                const assignmentStart = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
                const assignmentEnd = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate());
                
                return checkDate >= assignmentStart && checkDate <= assignmentEnd;
            });
        },

        isToday(date) {
            const today = new Date();
            return date.toDateString() === today.toDateString();
        },

        previousMonth() {
            this.currentDate.setMonth(this.currentDate.getMonth() - 1);
            this.generateCalendar();
        },

        nextMonth() {
            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            this.generateCalendar();
        },

        goToToday() {
            this.currentDate = new Date();
            this.generateCalendar();
        },

        showAssignmentDetails(assignment) {
            this.selectedAssignment = assignment;
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.selectedAssignment = null;
        },

        formatDateTime(dateTimeString) {
            if (!dateTimeString) return '';
            return new Date(dateTimeString).toLocaleString('fr-FR');
        }
    }
}
</script>

