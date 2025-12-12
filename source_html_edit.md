

<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

        
        <div class="mb-6">
            
            <nav class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                <a href="http://localhost/admin/dashboard" class="hover:text-blue-600 transition-colors">
                    <span
 class="iconify block w-4 h-4"
 data-icon="heroicons:home"
 data-inline="false"
></span>
                </a>
                <span
 class="iconify block w-4 h-4 text-gray-400"
 data-icon="heroicons:chevron-right"
 data-inline="false"
></span>
                <a href="http://localhost/admin/drivers" class="hover:text-blue-600 transition-colors">
                    Gestion des Chauffeurs
                </a>
                <span
 class="iconify block w-4 h-4 text-gray-400"
 data-icon="heroicons:chevron-right"
 data-inline="false"
></span>
                <a href="http://localhost/admin/drivers/10" class="hover:text-blue-600 transition-colors">
                    Mohamed Boubenia
                </a>
                <span
 class="iconify block w-4 h-4 text-gray-400"
 data-icon="heroicons:chevron-right"
 data-inline="false"
></span>
                <span class="font-semibold text-gray-900">Modifier</span>
            </nav>

            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <span
 class="iconify block w-6 h-6 text-blue-600"
 data-icon="heroicons:pencil"
 data-inline="false"
></span>
                Modifier le Chauffeur
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Mohamed Boubenia • Matricule: DIF-900001
            </p>
        </div>

        
        
        
        <div x-data="driverFormValidation()" x-init="init()">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-0 mb-6">
 
 <div class="w-full bg-white border-b border-gray-200 py-8">
    <div class="px-4 mx-auto">
        <ol class="flex items-start justify-center gap-0 w-full max-w-4xl mx-auto">
                            
                <li class="flex flex-col items-center relative flex-1">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative"
                            x-bind:class="{
                                'border-blue-600 shadow-lg shadow-blue-500/40': currentStep === 1,
                                'border-blue-600 shadow-md shadow-blue-500/20': currentStep &gt; 1,
                                'border-gray-300 shadow-sm': currentStep &lt; 1
                            }">

                            
                            <span class="iconify w-6 h-6 transition-colors duration-300"
                                x-bind:class="{
                                    'text-gray-400': currentStep === 1,      
                                    'text-blue-600': currentStep &gt; 1,   
                                    'text-gray-300': currentStep &lt; 1       
                                }"
                                x-bind:data-icon="'lucide:' + &quot;user&quot;"
                                data-inline="false">
                            </span>
                        </div>

                        
                                                    <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-300"
                                x-bind:class="currentStep &gt; 1 ? 'bg-blue-600 shadow-sm' : 'bg-gray-300'">
                            </div>
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug"
                        x-bind:class="{
                            'text-blue-600 font-bold text-sm': currentStep === 1,
                            'text-blue-600 font-semibold': currentStep &gt; 1,
                            'text-gray-500': currentStep &lt; 1
                        }">
                        Informations Personnelles
                    </span>

                </li>
                            
                <li class="flex flex-col items-center relative flex-1">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative"
                            x-bind:class="{
                                'border-blue-600 shadow-lg shadow-blue-500/40': currentStep === 2,
                                'border-blue-600 shadow-md shadow-blue-500/20': currentStep &gt; 2,
                                'border-gray-300 shadow-sm': currentStep &lt; 2
                            }">

                            
                            <span class="iconify w-6 h-6 transition-colors duration-300"
                                x-bind:class="{
                                    'text-gray-400': currentStep === 2,      
                                    'text-blue-600': currentStep &gt; 2,   
                                    'text-gray-300': currentStep &lt; 2       
                                }"
                                x-bind:data-icon="'lucide:' + &quot;briefcase&quot;"
                                data-inline="false">
                            </span>
                        </div>

                        
                                                    <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-300"
                                x-bind:class="currentStep &gt; 2 ? 'bg-blue-600 shadow-sm' : 'bg-gray-300'">
                            </div>
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug"
                        x-bind:class="{
                            'text-blue-600 font-bold text-sm': currentStep === 2,
                            'text-blue-600 font-semibold': currentStep &gt; 2,
                            'text-gray-500': currentStep &lt; 2
                        }">
                        Informations Professionnelles
                    </span>

                </li>
                            
                <li class="flex flex-col items-center relative flex-1">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative"
                            x-bind:class="{
                                'border-blue-600 shadow-lg shadow-blue-500/40': currentStep === 3,
                                'border-blue-600 shadow-md shadow-blue-500/20': currentStep &gt; 3,
                                'border-gray-300 shadow-sm': currentStep &lt; 3
                            }">

                            
                            <span class="iconify w-6 h-6 transition-colors duration-300"
                                x-bind:class="{
                                    'text-gray-400': currentStep === 3,      
                                    'text-blue-600': currentStep &gt; 3,   
                                    'text-gray-300': currentStep &lt; 3       
                                }"
                                x-bind:data-icon="'lucide:' + &quot;id-card&quot;"
                                data-inline="false">
                            </span>
                        </div>

                        
                                                    <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-300"
                                x-bind:class="currentStep &gt; 3 ? 'bg-blue-600 shadow-sm' : 'bg-gray-300'">
                            </div>
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug"
                        x-bind:class="{
                            'text-blue-600 font-bold text-sm': currentStep === 3,
                            'text-blue-600 font-semibold': currentStep &gt; 3,
                            'text-gray-500': currentStep &lt; 3
                        }">
                        Permis de Conduire
                    </span>

                </li>
                            
                <li class="flex flex-col items-center relative flex-none">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative"
                            x-bind:class="{
                                'border-blue-600 shadow-lg shadow-blue-500/40': currentStep === 4,
                                'border-blue-600 shadow-md shadow-blue-500/20': currentStep &gt; 4,
                                'border-gray-300 shadow-sm': currentStep &lt; 4
                            }">

                            
                            <span class="iconify w-6 h-6 transition-colors duration-300"
                                x-bind:class="{
                                    'text-gray-400': currentStep === 4,      
                                    'text-blue-600': currentStep &gt; 4,   
                                    'text-gray-300': currentStep &lt; 4       
                                }"
                                x-bind:data-icon="'lucide:' + &quot;link&quot;"
                                data-inline="false">
                            </span>
                        </div>

                        
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug"
                        x-bind:class="{
                            'text-blue-600 font-bold text-sm': currentStep === 4,
                            'text-blue-600 font-semibold': currentStep &gt; 4,
                            'text-gray-500': currentStep &lt; 4
                        }">
                        Compte &amp; Urgence
                    </span>

                </li>
                    </ol>
    </div>
</div>

                
                <form method="POST" action="http://localhost/admin/drivers/10" enctype="multipart/form-data" @submit="onSubmit" class="p-6">
                    <input type="hidden" name="_token" value="IQ0VPQ01Kzv8dJRQYUbtefoBO83ydWF7Tl1yCwER" autocomplete="off">                    <input type="hidden" name="_method" value="PUT">                    <input type="hidden" name="current_step" x-model="currentStep">

                    
                    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
 class="iconify block w-5 h-5 text-blue-600"
 data-icon="heroicons:user"
 data-inline="false"
></span>
                                    Informations Personnelles
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="" @blur="validateField('first_name', $event.target.value)">
  <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900">
 Prénom
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="heroicons:user"
 data-inline="false"
></span>
 </div>
 
 <input
 type="text"
 name="first_name"
 id="first_name"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: Ahmed"
 value="Mohamed"
  required   
 x-bind:class="(fieldErrors && fieldErrors['first_name'] && touchedFields && touchedFields['first_name']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 @blur="validateField('first_name', $event.target.value)"
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['first_name'] && touchedFields && touchedFields['first_name']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="" @blur="validateField('last_name', $event.target.value)">
  <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900">
 Nom
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="heroicons:user"
 data-inline="false"
></span>
 </div>
 
 <input
 type="text"
 name="last_name"
 id="last_name"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: Benali"
 value="Boubenia"
  required   
 x-bind:class="(fieldErrors && fieldErrors['last_name'] && touchedFields && touchedFields['last_name']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 @blur="validateField('last_name', $event.target.value)"
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['last_name'] && touchedFields && touchedFields['last_name']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="">
  <label for="birth_date" class="block mb-2 text-sm font-medium text-gray-900">
 Date de naissance
  </label>
 
 <div class="relative">
 <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
 <span
 class="iconify block w-4 h-4 text-gray-500"
 data-icon="heroicons:calendar-days"
 data-inline="false"
></span>
 </div>
 <input
 type="text"
 name="birth_date"
 id="birth_date"
 class="datepicker !bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 block w-full pl-10 p-2.5 transition-colors duration-200"
 placeholder="Choisir une date"
 value="19/08/2025"
     data-max-date="2025-12-09"  data-date-format="d/m/Y"
 autocomplete="off"
 
 />
 </div>

 </div>

 
 
                                    <div class="">
  <label for="personal_phone" class="block mb-2 text-sm font-medium text-gray-900">
 Téléphone personnel
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="heroicons:phone"
 data-inline="false"
></span>
 </div>
 
 <input
 type="tel"
 name="personal_phone"
 id="personal_phone"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: 0555123456"
 value="+213770457933"
   
 x-bind:class="(fieldErrors && fieldErrors['personal_phone'] && touchedFields && touchedFields['personal_phone']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['personal_phone'] && touchedFields && touchedFields['personal_phone']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="">
  <label for="personal_email" class="block mb-2 text-sm font-medium text-gray-900">
 Email personnel
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="heroicons:envelope"
 data-inline="false"
></span>
 </div>
 
 <input
 type="email"
 name="personal_email"
 id="personal_email"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: ahmed.benali@email.com"
 value="m.boubenia@gmail.com"
   
 x-bind:class="(fieldErrors && fieldErrors['personal_email'] && touchedFields && touchedFields['personal_email']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['personal_email'] && touchedFields && touchedFields['personal_email']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Groupe sanguin
                                                                                        <span class="ml-2 text-xs text-gray-500 font-normal">
                                                (Actuel: O-)
                                            </span>
                                                                                    </label>
                                        <select
                                            name="blood_type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm ">
                                            <option value="">Sélectionner</option>
                                            <option value="A+" >A+</option>
                                            <option value="A-" >A-</option>
                                            <option value="B+" >B+</option>
                                            <option value="B-" >B-</option>
                                            <option value="AB+" >AB+</option>
                                            <option value="AB-" >AB-</option>
                                            <option value="O+" >O+</option>
                                            <option value="O-" selected>O-</option>
                                        </select>
                                                                            </div>

                                    <div class="md:col-span-2">
                                        <div class="">
  <label for="address" class="block mb-2 text-sm font-medium text-gray-900">
 Adresse
  </label>
 
 <textarea
 name="address"
 id="address"
 rows="3"
 class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 !bg-gray-50"
 placeholder="Adresse complète du chauffeur..."
   
 >Quelque part avec une jolie vue à Boumerdes</textarea>

 </div>
                                    </div>

                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Photo du chauffeur
                                        </label>
                                        <div class="flex items-center gap-6">
                                            
                                            <div class="flex-shrink-0">
                                                                                                <div x-show="!photoPreview">
                                                    <img src="http://localhost/storage/drivers/photos/93Q4LyafGvTHP1zzhk1S2X8Q3oCeVDFruc0h3bmh.png" class="h-24 w-24 rounded-full object-cover ring-2 ring-blue-100" alt="Photo actuelle">
                                                </div>
                                                                                                <img x-show="photoPreview" :src="photoPreview" class="h-24 w-24 rounded-full object-cover ring-2 ring-blue-100" alt="Nouvelle photo" x-cloak>
                                            </div>
                                            
                                            <div class="flex-1">
                                                <input
                                                    type="file"
                                                    name="photo"
                                                    id="photo"
                                                    accept="image/*"
                                                    @change="updatePhotoPreview($event)"
                                                    class="block w-full text-sm text-gray-500
 file:mr-4 file:py-2 file:px-4
 file:rounded-lg file:border-0
 file:text-sm file:font-medium
 file:bg-blue-50 file:text-blue-700
 hover:file:bg-blue-100
 cursor-pointer">
                                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF jusqu'à 5MB. Laissez vide pour conserver la photo actuelle.</p>
                                                                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
 class="iconify block w-5 h-5 text-blue-600"
 data-icon="heroicons:briefcase"
 data-inline="false"
></span>
                                    Informations Professionnelles
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="">
  <label for="employee_number" class="block mb-2 text-sm font-medium text-gray-900">
 Matricule
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="heroicons:identification"
 data-inline="false"
></span>
 </div>
 
 <input
 type="text"
 name="employee_number"
 id="employee_number"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: EMP-2024-001"
 value="DIF-900001"
   
 x-bind:class="(fieldErrors && fieldErrors['employee_number'] && touchedFields && touchedFields['employee_number']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['employee_number'] && touchedFields && touchedFields['employee_number']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="">
  <label for="recruitment_date" class="block mb-2 text-sm font-medium text-gray-900">
 Date de recrutement
  </label>
 
 <div class="relative">
 <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
 <span
 class="iconify block w-4 h-4 text-gray-500"
 data-icon="heroicons:calendar-days"
 data-inline="false"
></span>
 </div>
 <input
 type="text"
 name="recruitment_date"
 id="recruitment_date"
 class="datepicker !bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 block w-full pl-10 p-2.5 transition-colors duration-200"
 placeholder="Choisir une date"
 value="20/12/2024"
     data-max-date="2025-12-09"  data-date-format="d/m/Y"
 autocomplete="off"
 
 />
 </div>

 </div>


                                    <div class="">
  <label for="contract_end_date" class="block mb-2 text-sm font-medium text-gray-900">
 Fin de contrat
  </label>
 
 <div class="relative">
 <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
 <span
 class="iconify block w-4 h-4 text-gray-500"
 data-icon="heroicons:calendar-days"
 data-inline="false"
></span>
 </div>
 <input
 type="text"
 name="contract_end_date"
 id="contract_end_date"
 class="datepicker !bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 block w-full pl-10 p-2.5 transition-colors duration-200"
 placeholder="Choisir une date"
 value="31/12/2030"
    data-min-date="2025-12-09"   data-date-format="d/m/Y"
 autocomplete="off"
 
 />
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Date de fin du contrat (optionnel)
 </p>
 </div>


                                    <div class="" @change="validateField('status_id', $event.target.value)">
        <label for="slimselect-status_id-693876fd92585" class="block mb-2 text-sm font-medium text-gray-900">
        Statut du Chauffeur
                <span class="text-red-500">*</span>
            </label>
    
    <select
        name="status_id"
        id="slimselect-status_id-693876fd92585"
        class="slimselect-field w-full"
        data-slimselect="true"
        data-placeholder="Sélectionnez un statut..."
                        @change="validateField('status_id', $event.target.value)">
        
                        <option value="" data-placeholder="true">Sélectionnez un statut...</option>
        
                <option
            value="11"
            >
            Available
        </option>
                <option
            value="1"
            >
            Actif
        </option>
                <option
            value="7"
            selected>
            Disponible
        </option>
                <option
            value="8"
            >
            En mission
        </option>
                <option
            value="2"
            >
            En service
        </option>
                <option
            value="9"
            >
            En congé
        </option>
                <option
            value="3"
            >
            En congé
        </option>
                <option
            value="4"
            >
            En formation
        </option>
                <option
            value="10"
            >
            Autre
        </option>
                    </select>

    
    
    <p x-show="fieldErrors && fieldErrors['status_id'] && touchedFields && touchedFields['status_id']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
        <span>Ce champ est obligatoire</span>
    </p>
</div>



                                    <div class="md:col-span-2">
                                        <div class="">
  <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">
 Notes professionnelles
  </label>
 
 <textarea
 name="notes"
 id="notes"
 rows="4"
 class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 !bg-gray-50"
 placeholder="Informations complémentaires sur le chauffeur..."
   
 >Informaticien à l&#039;origine</textarea>

  <p class="mt-2 text-sm text-gray-600">
 Compétences, formations, remarques, etc.
 </p>
 </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
 class="iconify block w-5 h-5 text-blue-600"
 data-icon="heroicons:identification"
 data-inline="false"
></span>
                                    Permis de Conduire
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="">
  <label for="license_number" class="block mb-2 text-sm font-medium text-gray-900">
 Numéro de permis
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="heroicons:identification"
 data-inline="false"
></span>
 </div>
 
 <input
 type="text"
 name="license_number"
 id="license_number"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: 123456789"
 value="Dz-53000-00"
  required   
 x-bind:class="(fieldErrors && fieldErrors['license_number'] && touchedFields && touchedFields['license_number']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['license_number'] && touchedFields && touchedFields['license_number']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Catégories de permis <span class="text-red-500">*</span>
                                                                                    </label>
                                                                                <div class="" @change="validateField('license_categories', $event.target.value)">
    
    <select
        name="license_categories[]"
        id="slimselect-license_categories[]-693876fd926e3"
        class="slimselect-field w-full"
        data-slimselect="true"
        data-placeholder="Sélectionner les catégories"
                 multiple         @change="validateField('license_categories', $event.target.value)">
        
                
                <option
            value="A1"
            >
            A1 - Motocyclettes légères
        </option>
                <option
            value="A"
            >
            A - Motocyclettes
        </option>
                <option
            value="B"
            >
            B - Véhicules légers
        </option>
                <option
            value="BE"
            >
            B(E) - Véhicules légers avec remorque
        </option>
                <option
            value="C1"
            >
            C1 - Poids lourds légers
        </option>
                <option
            value="C1E"
            >
            C1(E) - Poids lourds légers avec remorque
        </option>
                <option
            value="C"
            >
            C - Poids lourds
        </option>
                <option
            value="CE"
            >
            C(E) - Poids lourds avec remorque
        </option>
                <option
            value="D"
            >
            D - Transport de personnes
        </option>
                <option
            value="DE"
            >
            D(E) - Transport de personnes avec remorque
        </option>
                <option
            value="F"
            >
            F - Véhicules agricoles
        </option>
                    </select>

        <p class="mt-2 text-sm text-gray-500">
        Maintenez Ctrl pour sélectionner plusieurs catégories
    </p>
    
    
    <p x-show="fieldErrors && fieldErrors['license_categories[]'] && touchedFields && touchedFields['license_categories[]']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
        <span>Ce champ est obligatoire</span>
    </p>
</div>

                                                                                <p class="mt-1 text-xs text-gray-500">Maintenez Ctrl pour sélectionner plusieurs catégories</p>
                                    </div>

                                    <div class="">
  <label for="license_issue_date" class="block mb-2 text-sm font-medium text-gray-900">
 Date de délivrance
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
 <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
 <span
 class="iconify block w-4 h-4 text-gray-500"
 data-icon="heroicons:calendar-days"
 data-inline="false"
></span>
 </div>
 <input
 type="text"
 name="license_issue_date"
 id="license_issue_date"
 class="datepicker !bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 block w-full pl-10 p-2.5 transition-colors duration-200"
 placeholder="Choisir une date"
 value="10/10/2020"
  required     data-max-date="2025-12-09"  data-date-format="d/m/Y"
 autocomplete="off"
 
 />
 </div>

 </div>


                                    <div class="">
  <label for="license_expiry_date" class="block mb-2 text-sm font-medium text-gray-900">
 Date d&#039;expiration
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
 <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
 <span
 class="iconify block w-4 h-4 text-gray-500"
 data-icon="heroicons:calendar-days"
 data-inline="false"
></span>
 </div>
 <input
 type="text"
 name="license_expiry_date"
 id="license_expiry_date"
 class="datepicker !bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 block w-full pl-10 p-2.5 transition-colors duration-200"
 placeholder="Choisir une date"
 value="10/10/2030"
  required    data-min-date="2025-12-09"   data-date-format="d/m/Y"
 autocomplete="off"
 
 />
 </div>

 </div>


                                    <div class="">
  <label for="license_authority" class="block mb-2 text-sm font-medium text-gray-900">
 Autorité de délivrance
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="heroicons:building-office-2"
 data-inline="false"
></span>
 </div>
 
 <input
 type="text"
 name="license_authority"
 id="license_authority"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: Wilaya d&#039;Alger"
 value="Boumerdes"
   
 x-bind:class="(fieldErrors && fieldErrors['license_authority'] && touchedFields && touchedFields['license_authority']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['license_authority'] && touchedFields && touchedFields['license_authority']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="flex items-center h-full pt-6">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                name="license_verified"
                                                value="1"
                                                
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700 font-medium">
                                                <span
 class="iconify block w-4 h-4 inline text-blue-600"
 data-icon="heroicons:check-badge"
 data-inline="false"
></span>
                                                Permis vérifié
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        style="display: none;">
                        <div class="space-y-6">
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
 class="iconify block w-5 h-5 text-blue-600"
 data-icon="heroicons:user-circle"
 data-inline="false"
></span>
                                    Compte Utilisateur (Optionnel)
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Compte utilisateur
                                                                                        <span class="ml-2 text-xs text-gray-500 font-normal">
                                                (Actuel: )
                                            </span>
                                                                                    </label>
                                        <div class="">
    
    <select
        name="user_id"
        id="slimselect-user_id-693876fd93127"
        class="slimselect-field w-full"
        data-slimselect="true"
        data-placeholder="Rechercher un utilisateur..."
                        >
        
                        <option value="" data-placeholder="true">Rechercher un utilisateur...</option>
        
                <option
            value="1"
            >
             (mohamed.meziani@trans-algerlogistics.local)
        </option>
                <option
            value="2"
            >
             (amine.belabes@trans-algerlogistics.local)
        </option>
                <option
            value="25"
            selected>
             (mohamedboubenia@zenfleet.dz)
        </option>
                <option
            value="23"
            >
            Ali Boumalou (ali@zenfleet.dz)
        </option>
                <option
            value="5"
            >
            Gestionnaire Flotte (gestionnaire@zenfleet.dz)
        </option>
                <option
            value="6"
            >
            SUPER VISEUR (superviseur@zenfleet.dz)
        </option>
                <option
            value="3"
            >
            Super Administrateur (superadmin@zenfleet.dz)
        </option>
                <option
            value="4"
            >
            admin zenfleet (admin@zenfleet.dz)
        </option>
                <option
            value="7"
            >
            hamid Baroudi (comptable@zenfleet.dz)
        </option>
                    </select>

        <p class="mt-2 text-sm text-gray-500">
        Sélectionnez un compte existant ou laissez vide (optionnel)
    </p>
    
    
    <p x-show="fieldErrors && fieldErrors['user_id'] && touchedFields && touchedFields['user_id']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
        <span>Ce champ est obligatoire</span>
    </p>
</div>

                                                                                <p class="mt-1 text-xs text-gray-500">
                                            Sélectionnez un compte existant ou laissez vide (optionnel)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
 class="iconify block w-5 h-5 text-red-600"
 data-icon="heroicons:phone"
 data-inline="false"
></span>
                                    Contact d'Urgence
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="">
  <label for="emergency_contact_name" class="block mb-2 text-sm font-medium text-gray-900">
 Nom du contact
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="heroicons:user"
 data-inline="false"
></span>
 </div>
 
 <input
 type="text"
 name="emergency_contact_name"
 id="emergency_contact_name"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: Fatima Benali"
 value="Boubenia Ali"
   
 x-bind:class="(fieldErrors && fieldErrors['emergency_contact_name'] && touchedFields && touchedFields['emergency_contact_name']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['emergency_contact_name'] && touchedFields && touchedFields['emergency_contact_name']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="">
  <label for="emergency_contact_phone" class="block mb-2 text-sm font-medium text-gray-900">
 Téléphone du contact
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="heroicons:phone"
 data-inline="false"
></span>
 </div>
 
 <input
 type="tel"
 name="emergency_contact_phone"
 id="emergency_contact_phone"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: 0555987654"
 value="0661590000"
   
 x-bind:class="(fieldErrors && fieldErrors['emergency_contact_phone'] && touchedFields && touchedFields['emergency_contact_phone']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['emergency_contact_phone'] && touchedFields && touchedFields['emergency_contact_phone']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="">
  <label for="emergency_contact_relationship" class="block mb-2 text-sm font-medium text-gray-900">
 Lien de parenté
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="heroicons:users"
 data-inline="false"
></span>
 </div>
 
 <input
 type="text"
 name="emergency_contact_relationship"
 id="emergency_contact_relationship"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: Épouse, Frère, Mère"
 value="Frère"
   
 x-bind:class="(fieldErrors && fieldErrors['emergency_contact_relationship'] && touchedFields && touchedFields['emergency_contact_relationship']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['emergency_contact_relationship'] && touchedFields && touchedFields['emergency_contact_relationship']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
 class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
 data-icon="lucide:circle-alert"
 data-inline="false"
></span>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                        <div>
                            <button
                                type="button"
                                @click="prevStep()"
                                x-show="currentStep > 1"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700">
                                <span
 class="iconify block w-4 h-4"
 data-icon="heroicons:arrow-left"
 data-inline="false"
></span>
                                Précédent
                            </button>
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="http://localhost/admin/drivers/10"
                                class="text-gray-600 hover:text-gray-900 font-medium text-sm transition-colors">
                                Annuler
                            </a>

                            <button
                                type="button"
                                @click="nextStep()"
                                x-show="currentStep < 4"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm">
                                Suivant
                                <span
 class="iconify block w-4 h-4"
 data-icon="heroicons:arrow-right"
 data-inline="false"
></span>
                            </button>

                            <button
                                type="submit"
                                x-show="currentStep === 4"
                                x-cloak
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm">
                                <span
 class="iconify block w-5 h-5"
 data-icon="heroicons:check"
 data-inline="false"
></span>
                                Enregistrer les Modifications
                            </button>
                        </div>
                    </div>
                </form>
</div>

        </div>
    </div>
</section>

<!DOCTYPE html>
<html lang="fr" class="h-full bg-zinc-50">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="IQ0VPQ01Kzv8dJRQYUbtefoBO83ydWF7Tl1yCwER">
  <meta name="user-data" content="{&quot;id&quot;:4,&quot;name&quot;:&quot;admin zenfleet&quot;,&quot;role&quot;:&quot;Admin&quot;}">
 
 <title>Modifier le Chauffeur - Mohamed Boubenia - ZenFleet</title>

 <!-- Fonts -->
 <link rel="preconnect" href="https://fonts.bunny.net">
 <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

 <!-- Iconify CDN -->
 <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

 <!-- Font Awesome 6 -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

 
 
 
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">

 
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slim-select@2/dist/slimselect.css">

 
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
 
 
 <style>
 /* 🎨 FLATPICKR ENTERPRISE-GRADE LIGHT MODE - ZenFleet Ultra-Pro */
 .flatpickr-calendar {
 background-color: white !important;
 border: 1px solid rgb(229 231 235);
 border-radius: 0.75rem;
 box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
 font-family: inherit;
 }

 /* En-tête (mois/année) - Bleu blue-600 premium */
 .flatpickr-months {
 background: rgb(37 99 235) !important;
 border-radius: 0.75rem 0.75rem 0 0;
 padding: 0.875rem 0;
 }

 .flatpickr-months .flatpickr-month,
 .flatpickr-current-month .flatpickr-monthDropdown-months {
 background-color: transparent !important;
 color: white !important;
 font-weight: 600;
 font-size: 1rem;
 }

 /* Boutons navigation */
 .flatpickr-months .flatpickr-prev-month,
 .flatpickr-months .flatpickr-next-month {
 fill: white !important;
 transition: all 0.2s;
 }

 .flatpickr-months .flatpickr-prev-month:hover,
 .flatpickr-months .flatpickr-next-month:hover {
 fill: rgb(219 234 254) !important;
 transform: scale(1.15);
 }

 /* Jours de la semaine */
 .flatpickr-weekdays {
 background-color: rgb(249 250 251) !important;
 padding: 0.625rem 0;
 border-bottom: 1px solid rgb(229 231 235);
 }

 .flatpickr-weekday {
 color: rgb(107 114 128) !important;
 font-weight: 600;
 font-size: 0.75rem;
 text-transform: uppercase;
 letter-spacing: 0.05em;
 }

 /* Corps du calendrier */
 .flatpickr-days {
 background-color: white !important;
 }

 /* Jours du mois */
 .flatpickr-day {
 color: rgb(17 24 39) !important;
 border-radius: 0.5rem;
 font-weight: 500;
 transition: all 0.2s;
 border: 1px solid transparent;
 }

 .flatpickr-day.today {
 border: 2px solid rgb(37 99 235) !important;
 font-weight: 700;
 color: rgb(37 99 235) !important;
 background-color: rgb(239 246 255) !important;
 }

 .flatpickr-day.selected,
 .flatpickr-day.selected:hover {
 background-color: rgb(37 99 235) !important;
 border-color: rgb(37 99 235) !important;
 color: white !important;
 font-weight: 700;
 box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
 }

 .flatpickr-day:hover:not(.selected):not(.flatpickr-disabled):not(.today) {
 background-color: rgb(243 244 246) !important;
 border-color: rgb(229 231 235) !important;
 color: rgb(17 24 39) !important;
 transform: scale(1.05);
 }

 .flatpickr-day.flatpickr-disabled {
 color: rgb(209 213 219) !important;
 opacity: 0.4;
 }
 </style>

 <link rel="preload" as="style" href="http://localhost/build/assets/app-Fs2d3d4g.css" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/app-VGFaS7N_.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/vendor-common-B9ygI19o.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/ui-public-2hikc2V1.js" /><link rel="stylesheet" href="http://localhost/build/assets/app-Fs2d3d4g.css" data-navigate-track="reload" /><script type="module" src="http://localhost/build/assets/app-VGFaS7N_.js" data-navigate-track="reload"></script>  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
 <style>
 /* 🎨 FLATPICKR ENTERPRISE-GRADE LIGHT MODE - ZenFleet Ultra-Pro */
 .flatpickr-calendar {
 background-color: white !important;
 border: 1px solid rgb(229 231 235);
 border-radius: 0.75rem;
 box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
 font-family: inherit;
 }

 /* En-tête (mois/année) - Bleu blue-600 premium */
 .flatpickr-months {
 background: rgb(37 99 235) !important;
 border-radius: 0.75rem 0.75rem 0 0;
 padding: 0.875rem 0;
 }

 .flatpickr-months .flatpickr-month,
 .flatpickr-current-month .flatpickr-monthDropdown-months {
 background-color: transparent !important;
 color: white !important;
 font-weight: 600;
 font-size: 1rem;
 }

 /* Boutons navigation */
 .flatpickr-months .flatpickr-prev-month,
 .flatpickr-months .flatpickr-next-month {
 fill: white !important;
 transition: all 0.2s;
 }

 .flatpickr-months .flatpickr-prev-month:hover,
 .flatpickr-months .flatpickr-next-month:hover {
 fill: rgb(219 234 254) !important;
 transform: scale(1.15);
 }

 /* Jours de la semaine */
 .flatpickr-weekdays {
 background-color: rgb(249 250 251) !important;
 padding: 0.625rem 0;
 border-bottom: 1px solid rgb(229 231 235);
 }

 .flatpickr-weekday {
 color: rgb(107 114 128) !important;
 font-weight: 600;
 font-size: 0.75rem;
 text-transform: uppercase;
 letter-spacing: 0.05em;
 }

 /* Corps du calendrier */
 .flatpickr-days {
 background-color: white !important;
 }

 /* Jours du mois */
 .flatpickr-day {
 color: rgb(17 24 39) !important;
 border-radius: 0.5rem;
 font-weight: 500;
 transition: all 0.2s;
 border: 1px solid transparent;
 }

 .flatpickr-day.today {
 border: 2px solid rgb(37 99 235) !important;
 font-weight: 700;
 color: rgb(37 99 235) !important;
 background-color: rgb(239 246 255) !important;
 }

 .flatpickr-day.selected,
 .flatpickr-day.selected:hover {
 background-color: rgb(37 99 235) !important;
 border-color: rgb(37 99 235) !important;
 color: white !important;
 font-weight: 700;
 box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
 }

 .flatpickr-day:hover:not(.selected):not(.flatpickr-disabled):not(.today) {
 background-color: rgb(243 244 246) !important;
 border-color: rgb(229 231 235) !important;
 color: rgb(17 24 39) !important;
 transform: scale(1.05);
 }

 .flatpickr-day.flatpickr-disabled {
 color: rgb(209 213 219) !important;
 opacity: 0.4;
 }

 /* Input avec bordure rouge si erreur */
 input.datepicker.border-red-500 + .flatpickr-calendar {
 border-color: rgb(239 68 68);
 }
 </style>
 <style>
    /* ========================================
   ZENFLEET SLIMSELECT ENTERPRISE THEME
   ======================================== */

    :root {
        --ss-main-height: 42px;
        --ss-primary-color: #2563eb;
        /* blue-600 */
        --ss-bg-color: #ffffff;
        --ss-font-color: #111827;
        /* gray-900 */
        --ss-font-placeholder-color: #9ca3af;
        /* gray-400 */
        --ss-border-color: #d1d5db;
        /* gray-300 */
        --ss-border-radius: 0.5rem;
        /* rounded-lg */
        --ss-spacing-l: 10px;
        --ss-spacing-m: 8px;
        --ss-spacing-s: 4px;
        --ss-animation-timing: 0.2s;
        --ss-focus-color: #3b82f6;
        /* blue-500 */
        --ss-error-color: #dc2626;
        /* red-600 */
    }

    /* Main container styling */
    .ss-main {
        background-color: #f9fafb;
        /* gray-50 */
        border-color: #d1d5db;
        /* gray-300 */
        color: #111827;
        /* gray-900 */
        border-radius: 0.5rem;
        /* rounded-lg */
        padding: 2px 0;
        /* Ajustement padding */
        min-height: 42px;
        /* Hauteur minimale */
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        /* shadow-sm */
    }

    /* Focus state */
    .ss-main:focus-within {
        border-color: var(--ss-focus-color);
        box-shadow: 0 0 0 1px var(--ss-focus-color);
        /* ring-1 */
        background-color: #ffffff;
    }

    /* Values styling */
    .ss-main .ss-values .ss-single {
        padding: 4px var(--ss-spacing-l);
        font-size: 0.875rem;
        /* text-sm = 14px */
        line-height: 1.25rem;
        /* leading-5 */
        font-weight: 400;
    }

    /* Placeholder styling */
    .ss-main .ss-values .ss-placeholder {
        font-size: 0.875rem;
        font-style: normal;
    }

    /* Dropdown content - ombre plus prononcée */
    .ss-content {
        margin-top: 4px;
        box-shadow:
            0 10px 15px -3px rgba(0, 0, 0, 0.1),
            /* shadow-lg */
            0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-color: #e5e7eb;
        /* gray-200 */
    }

    /* Champ de recherche */
    .ss-content .ss-search {
        background-color: #f9fafb;
        /* gray-50 */
        border-bottom: 1px solid #e5e7eb;
        /* gray-200 */
        padding: var(--ss-spacing-m);
    }

    .ss-content .ss-search input {
        font-size: 0.875rem;
        padding: 10px 12px;
        border-radius: 6px;
        /* rounded-md */
    }

    .ss-content .ss-search input:focus {
        border-color: var(--ss-focus-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Options - style hover amélioré */
    .ss-content .ss-list .ss-option {
        font-size: 0.875rem;
        padding: 10px var(--ss-spacing-l);
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .ss-content .ss-list .ss-option:hover {
        background-color: #eff6ff;
        /* blue-50 */
        color: var(--ss-font-color);
        /* Garder texte lisible */
    }

    /* Option sélectionnée - fond plus subtil */
    .ss-content .ss-list .ss-option.ss-highlighted,
    .ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected {
        background-color: var(--ss-primary-color);
        color: #ffffff;
    }

    /* Option sélectionnée avec checkmark */
    .ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected::after {
        content: '✓';
        margin-left: auto;
        font-weight: 600;
    }

    /* État d'erreur de validation */
    .slimselect-error .ss-main {
        border-color: var(--ss-error-color) !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
        /* ring-red-600/10 */
    }

    /* Cacher le placeholder dans la liste des options */
    .ss-content .ss-list .ss-option[data-placeholder="true"] {
        display: none !important;
    }

    /* Message d'erreur */
    .ss-content .ss-list .ss-error {
        font-size: 0.875rem;
        padding: var(--ss-spacing-l);
    }

    /* Message de recherche en cours */
    .ss-content .ss-list .ss-searching {
        font-size: 0.875rem;
        color: var(--ss-primary-color);
        padding: var(--ss-spacing-l);
    }

    /* Flèche de dropdown */
    .ss-main .ss-arrow path {
        stroke-width: 14;
    }

    /* Animation d'ouverture du dropdown */
    .ss-content.ss-open-below,
    .ss-content.ss-open-above {
        animation: zenfleetSlideIn var(--ss-animation-timing) ease-out;
    }

    @keyframes zenfleetSlideIn {
        from {
            opacity: 0;
            transform: scaleY(0.95) translateY(-4px);
        }

        to {
            opacity: 1;
            transform: scaleY(1) translateY(0);
        }
    }

    /* ========================================
   RESPONSIVE MOBILE
   ======================================== */
    @media (max-width: 640px) {
        :root {
            --ss-main-height: 44px;
            /* Plus grand pour touch */
            --ss-content-height: 240px;
        }

        .ss-content .ss-list .ss-option {
            padding: 12px var(--ss-spacing-l);
            /* Touch-friendly */
            min-height: 44px;
            /* iOS minimum */
        }

        .ss-content .ss-search input {
            padding: 12px;
            font-size: 16px;
            /* Évite zoom iOS */
        }
    }

    /* ========================================
   ACCESSIBILITÉ
   ======================================== */
    @media (prefers-reduced-motion: reduce) {

        .ss-main,
        .ss-content,
        .ss-option {
            transition: none !important;
            animation: none !important;
        }
    }
</style>    <!-- Livewire Styles --><style >[wire\:loading][wire\:loading], [wire\:loading\.delay][wire\:loading\.delay], [wire\:loading\.inline-block][wire\:loading\.inline-block], [wire\:loading\.inline][wire\:loading\.inline], [wire\:loading\.block][wire\:loading\.block], [wire\:loading\.flex][wire\:loading\.flex], [wire\:loading\.table][wire\:loading\.table], [wire\:loading\.grid][wire\:loading\.grid], [wire\:loading\.inline-flex][wire\:loading\.inline-flex] {display: none;}[wire\:loading\.delay\.none][wire\:loading\.delay\.none], [wire\:loading\.delay\.shortest][wire\:loading\.delay\.shortest], [wire\:loading\.delay\.shorter][wire\:loading\.delay\.shorter], [wire\:loading\.delay\.short][wire\:loading\.delay\.short], [wire\:loading\.delay\.default][wire\:loading\.delay\.default], [wire\:loading\.delay\.long][wire\:loading\.delay\.long], [wire\:loading\.delay\.longer][wire\:loading\.delay\.longer], [wire\:loading\.delay\.longest][wire\:loading\.delay\.longest] {display: none;}[wire\:offline][wire\:offline] {display: none;}[wire\:dirty]:not(textarea):not(input):not(select) {display: none;}:root {--livewire-progress-bar-color: #2299dd;}[x-cloak] {display: none !important;}[wire\:cloak] {display: none !important;}</style>
</head>
<body class="h-full">
 <div class="min-h-full">
 
 <div class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
 <div class="flex grow flex-col overflow-hidden bg-[#eef2f7] border-r border-gray-200/60 shadow-sm">
 
 <div class="w-full flex-none px-4 py-4 h-16 flex items-center border-b border-gray-300/50">
 <div class="flex items-center w-full">
 <div class="relative mr-3">
 <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-md">
 <span
 class="iconify block w-5 h-5 text-white"
 data-icon="mdi:truck-fast"
 data-inline="false"
></span>
 </div>
 </div>
 <div class="flex-1">
 <span class="text-gray-800 text-lg font-bold tracking-tight">ZenFleet</span>
 <div class="text-xs text-gray-600 font-medium">Fleet Management</div>
 </div>
 </div>
 </div>

 
 <div class="flex flex-col flex-1 overflow-hidden">
 <ul class="grow overflow-x-hidden overflow-y-auto w-full px-2 py-4 mb-0 scrollbar-thin scrollbar-thumb-gray-400/30 scrollbar-track-transparent" role="tree">
 
 <li class="flex">
  <a href="http://localhost/admin/dashboard"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="material-symbols:dashboard-rounded"
 data-inline="false"
></span>
 <span class="flex-1">Dashboard</span>
 </a>
 </li>

 
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:car-multiple"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Véhicules</span>
 <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
 x-bind:style="`height: 0%; top: 0%;`"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1.5">
  <a href="http://localhost/admin/vehicles"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="mdi:format-list-bulleted"
 data-inline="false"
></span>
 Gestion Véhicules
 </a>
   <a href="http://localhost/admin/assignments"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="mdi:clipboard-text"
 data-inline="false"
></span>
 Affectations
 </a>
  </div>
 </div>
 </div>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: true }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-md">
 <span
 class="iconify block w-5 h-5 mr-3 text-white"
 data-icon="mdi:account-group"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Chauffeurs</span>
 <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
 </button>
 <div x-show="open" 
 x-transition:enter="transition ease-out duration-300" 
 x-transition:enter-start="opacity-0 max-h-0" 
 x-transition:enter-end="opacity-100 max-h-96" 
 x-transition:leave="transition ease-in duration-200" 
 x-transition:leave-start="opacity-100 max-h-96" 
 x-transition:leave-end="opacity-0 max-h-0" 
 class="overflow-hidden">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
 x-bind:style="`height: 0%; top: 0%;`"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1">
  <a href="http://localhost/admin/drivers"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="mdi:view-list"
 data-inline="false"
></span>
 Liste
 </a>
   <a href="http://localhost/admin/drivers/sanctions"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="mdi:gavel"
 data-inline="false"
></span>
 Sanctions
 </a>
  </div>
 </div>
 </div>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/depots"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:office-building"
 data-inline="false"
></span>
 <span class="flex-1">Dépôts</span>
 </a>
 </li>
 
 
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:speedometer"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Kilométrage</span>
 <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300 h-1/2"
 x-bind:style="`top: 0%;`"></div>
 </div>
 </div>
 <ul class="flex-1 space-y-1 pb-2">
 
 <li>
  <a href="http://localhost/admin/mileage-readings"
 class="flex items-center h-9 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-5 h-5 mr-2 text-gray-600"
 data-icon="mdi:history"
 data-inline="false"
></span>
 Historique
 </a>
 </li>
 
  <li>
  <a href="http://localhost/admin/mileage-readings/update"
 class="flex items-center h-9 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-5 h-5 mr-2 text-gray-600"
 data-icon="mdi:pencil"
 data-inline="false"
></span>
 Mettre à jour
 </a>
 </li>
  </ul>
 </div>
 </div>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="lucide:wrench"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Maintenance</span>
 <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="lucide:chevron-down"
 data-inline="false"
></span>
 </button>
 <div x-show="open" 
 x-transition:enter="transition ease-out duration-300" 
 x-transition:enter-start="opacity-0 max-h-0" 
 x-transition:enter-end="opacity-100 max-h-[500px]" 
 x-transition:leave="transition ease-in duration-200" 
 x-transition:leave-start="opacity-100 max-h-[500px]" 
 x-transition:leave-end="opacity-0 max-h-0" 
 class="overflow-hidden">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
 x-bind:style="`height: 0%; top: 0%;`"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1">
 
 <a href="http://localhost/admin/maintenance/dashboard"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:layout-dashboard"
 data-inline="false"
></span>
 Vue d'ensemble
 </a>

 
 <a href="http://localhost/admin/maintenance/operations"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:list"
 data-inline="false"
></span>
 Opérations
 </a>

 
 <a href="http://localhost/admin/maintenance/operations/kanban"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:columns-3"
 data-inline="false"
></span>
 Kanban
 </a>

 
 <a href="http://localhost/admin/maintenance/operations/calendar"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:calendar-days"
 data-inline="false"
></span>
 Calendrier
 </a>

 
 <a href="http://localhost/admin/maintenance/schedules"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:repeat"
 data-inline="false"
></span>
 Planifications
 </a>

 
  <a href="http://localhost/admin/repair-requests"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:hammer"
 data-inline="false"
></span>
 Demandes Réparation
 </a>
  </div>
 </div>
 </div>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/alerts"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:bell-ring"
 data-inline="false"
></span>
 <span class="flex-1">Alertes</span>
 </a>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/documents"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:file-document"
 data-inline="false"
></span>
 <span class="flex-1">Documents</span>
 </a>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/suppliers"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:store"
 data-inline="false"
></span>
 <span class="flex-1">Fournisseurs</span>
 </a>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="solar:wallet-money-bold"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Dépenses</span>
   <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="lucide:chevron-down"
 data-inline="false"
></span>
 </button>
 <div x-show="open" 
 x-transition:enter="transition ease-out duration-300" 
 x-transition:enter-start="opacity-0 max-h-0" 
 x-transition:enter-end="opacity-100 max-h-[400px]" 
 x-transition:leave="transition ease-in duration-200" 
 x-transition:leave-start="opacity-100 max-h-[400px]" 
 x-transition:leave-end="opacity-0 max-h-0" 
 class="overflow-hidden">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
 x-bind:style="`height: 0%; top: 0%;`"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1">
 
 <a href="http://localhost/admin/vehicle-expenses"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:layout-dashboard"
 data-inline="false"
></span>
 Tableau de bord
 </a>

 
  <a href="http://localhost/admin/vehicle-expenses/create"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:plus-circle"
 data-inline="false"
></span>
 Nouvelle dépense
 </a>
 
 
  <a href="http://localhost/admin/vehicle-expenses/dashboard"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:chart-line"
 data-inline="false"
></span>
 Analytics
 </a>
 
 
  <a href="http://localhost/admin/vehicle-expenses?filter=pending_approval"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:clock"
 data-inline="false"
></span>
 Approbations
  </a>
 
 
 <a href="http://localhost/admin/vehicle-expenses?section=groups"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:wallet"
 data-inline="false"
></span>
 Budgets
 </a>

 
  <a href="http://localhost/admin/vehicle-expenses/export"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:download"
 data-inline="false"
></span>
 Export
 </a>
 
 
  <a href="http://localhost/admin/vehicle-expenses/analytics/cost-trends"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:trending-up"
 data-inline="false"
></span>
 TCO & Tendances
 </a>
  </div>
 </div>
 </div>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/reports"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:chart-bar"
 data-inline="false"
></span>
 <span class="flex-1">Rapports</span>
 </a>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:cog"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Administration</span>
 <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
 x-bind:style="`height: 0%; top: 0%;`"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1">
  <a href="http://localhost/admin/users"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="mdi:account-multiple"
 data-inline="false"
></span>
 Utilisateurs
 </a>
   <a href="http://localhost/admin/roles"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="mdi:shield-check"
 data-inline="false"
></span>
 Rôles & Permissions
 </a>
     </div>
 </div>
 </div>
 </li>
  </ul>

 
 </div>
 </div>
 </div>

 
 <div class="lg:hidden" x-data="{ open: false }">
 
 <div x-show="open"
 x-transition:enter="transition-opacity ease-linear duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 x-transition:leave="transition-opacity ease-linear duration-300"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
 class="relative z-50 lg:hidden">
 <div class="fixed inset-0 bg-gray-900/80" @click="open = false"></div>

 <div class="fixed inset-0 flex">
 <div x-show="open"
 x-transition:enter="transition ease-in-out duration-300 transform"
 x-transition:enter-start="-translate-x-full"
 x-transition:enter-end="translate-x-0"
 x-transition:leave="transition ease-in-out duration-300 transform"
 x-transition:leave-start="translate-x-0"
 x-transition:leave-end="-translate-x-full"
 class="relative mr-16 flex w-full max-w-xs flex-1">
 
 <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-zinc-50 px-6 pb-4">
 
 <div class="flex h-16 shrink-0 items-center">
 <div class="flex items-center">
 <span
 class="iconify block w-6 h-6 text-zinc-900 mr-3"
 data-icon="heroicons:truck"
 data-inline="false"
></span>
 <span class="text-zinc-900 text-xl font-bold">ZenFleet</span>
 </div>
 </div>

 
 <nav class="flex flex-1 flex-col">
 <ul role="list" class="flex flex-1 flex-col gap-y-2">
 <li>
 <ul role="list" class="-mx-2 space-y-1">
 
 <li>
  <a href="http://localhost/admin/dashboard"
 class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <span
 class="iconify block h-5 w-5 shrink-0"
 data-icon="heroicons:home"
 data-inline="false"
></span>
 Dashboard
 </a>
 </li>

 
 
 
  <li x-data="{ open: false }">
 <button @click="open = !open"
 class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <span
 class="iconify block h-5 w-5 shrink-0"
 data-icon="heroicons:truck"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Véhicules</span>
 <span
 class="iconify block h-4 w-4 transition-transform" :class="{ 'rotate-90': open }"
 data-icon="heroicons:chevron-right"
 data-inline="false"
></span>
 </button>
 <div x-show="open" x-transition class="mt-1">
 <ul class="ml-6 space-y-1">
 <li class="relative">
 <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
 <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
 <a href="http://localhost/admin/vehicles"
 class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
 <span
 class="iconify block h-4 w-4 shrink-0"
 data-icon="heroicons:truck"
 data-inline="false"
></span>
 Gestion Véhicules
 </a>
 </li>
 <li class="relative">
 <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
 <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
 <a href="http://localhost/admin/assignments"
 class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
 <span
 class="iconify block h-4 w-4 shrink-0"
 data-icon="heroicons:clipboard-document-list"
 data-inline="false"
></span>
 Affectations
 </a>
 </li>
 </ul>
 </div>
 </li>
 
 
  <li>
 <a href="http://localhost/admin/drivers"
 class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold bg-zinc-950 text-white">
 <span
 class="iconify block h-5 w-5 shrink-0"
 data-icon="heroicons:user"
 data-inline="false"
></span>
 Chauffeurs
 </a>
 </li>
 
 
  <li>
 <a href="http://localhost/admin/depots"
 class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <span
 class="iconify block h-5 w-5 shrink-0"
 data-icon="mdi:office-building"
 data-inline="false"
></span>
 Dépôts
 </a>
 </li>
 
 
  <li x-data="{ open: false }">
 <button @click="open = !open"
 class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <span
 class="iconify block h-5 w-5 shrink-0"
 data-icon="mdi:cog"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Administration</span>
 <span
 class="iconify block h-4 w-4 transition-transform" :class="{ 'rotate-90': open }"
 data-icon="heroicons:chevron-right"
 data-inline="false"
></span>
 </button>
 <div x-show="open" x-transition class="mt-1">
 <ul class="ml-6 space-y-1">
 <li class="relative">
 <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
 <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
 <a href="http://localhost/admin/users"
 class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
 <span
 class="iconify block h-4 w-4 shrink-0"
 data-icon="mdi:account-multiple"
 data-inline="false"
></span>
 Utilisateurs
 </a>
 </li>
 <li class="relative">
 <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
 <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
 <a href="http://localhost/admin/roles"
 class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
 <span
 class="iconify block h-4 w-4 shrink-0"
 data-icon="mdi:shield-check"
 data-inline="false"
></span>
 Rôles & Permissions
 </a>
 </li>
 </ul>
 </div>
 </li>
  </ul>
 </li>
 </ul>
 </nav>
 </div>
 </div>
 </div>
 </div>

 
 <div class="sticky top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-sm sm:px-6 lg:hidden">
 <button type="button" @click="open = true" class="-m-2.5 p-2.5 text-zinc-500 lg:hidden">
 <span class="sr-only">Ouvrir la sidebar</span>
 <span
 class="iconify block h-6 w-6"
 data-icon="heroicons:bars-3"
 data-inline="false"
></span>
 </button>
 <div class="flex-1 text-sm font-semibold leading-6 text-zinc-900">ZenFleet</div>
 <div class="h-8 w-8 bg-zinc-100 rounded-full flex items-center justify-center">
 <span
 class="iconify block h-4 w-4 text-zinc-500"
 data-icon="heroicons:user"
 data-inline="false"
></span>
 </div>
 </div>
 </div>

 
 <div class="lg:pl-64">
 <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-zinc-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
 <div class="h-6 w-px bg-zinc-200 lg:hidden" aria-hidden="true"></div>

 <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
 <div class="relative flex flex-1">
 
 </div>
 <div class="flex items-center gap-x-4 lg:gap-x-6">
 
 <div class="relative hidden lg:block">
 <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
 <span
 class="iconify block h-4 w-4 text-zinc-400"
 data-icon="heroicons:magnifying-glass"
 data-inline="false"
></span>
 </div>
 <input type="search"
 placeholder="Rechercher..."
 class="block w-64 rounded-md border-0 bg-white py-1.5 pl-10 pr-3 text-zinc-900 ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-zinc-600 sm:text-sm sm:leading-6">
 </div>

 
 <div class="relative">
 <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
 <span class="sr-only">Voir les notifications</span>
 <span
 class="iconify block h-6 w-6"
 data-icon="mdi:bell-ring"
 data-inline="false"
></span>
 <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
 </button>
 </div>

 
 <div class="relative">
 <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
 <span class="sr-only">Messages</span>
 <span
 class="iconify block h-6 w-6"
 data-icon="heroicons:envelope"
 data-inline="false"
></span>
 <span class="absolute -top-1 -right-1 h-4 w-4 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">2</span>
 </button>
 </div>

 
 <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600">
 <span class="sr-only">Basculer le mode sombre</span>
 <span
 class="iconify block h-6 w-6"
 data-icon="heroicons:moon"
 data-inline="false"
></span>
 </button>

 
 <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-zinc-200" aria-hidden="true"></div>

 
 <div class="relative" x-data="{ open: false }">
 <button type="button" @click="open = !open" class="-m-1.5 flex items-center p-1.5 hover:bg-zinc-50 rounded-lg transition-colors">
 <span class="sr-only">Ouvrir le menu utilisateur</span>
 <div class="h-8 w-8 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
 <span
 class="iconify block text-white w-4 h-4"
 data-icon="heroicons:user"
 data-inline="false"
></span>
 </div>
 <span class="hidden lg:flex lg:items-center">
 <div class="ml-3 text-left">
 <div class="text-sm font-semibold leading-5 text-zinc-900">admin zenfleet</div>
 <div class="text-xs leading-4 text-zinc-500">Admin</div>
 </div>
 <span
 class="iconify block ml-2 h-4 w-4 text-zinc-500 transition-transform" :class="{ 'rotate-180': open }"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
 </span>
 </button>

 <div x-show="open"
 @click.away="open = false"
 x-transition:enter="transition ease-out duration-100"
 x-transition:enter-start="transform opacity-0 scale-95"
 x-transition:enter-end="transform opacity-100 scale-100"
 x-transition:leave="transition ease-in duration-75"
 x-transition:leave-start="transform opacity-100 scale-100"
 x-transition:leave-end="transform opacity-0 scale-95"
 class="absolute right-0 z-10 mt-2.5 w-56 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-zinc-900/5">

 
 <div class="px-4 py-3 border-b border-zinc-100">
 <div class="flex items-center">
 <div class="h-10 w-10 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
 <span
 class="iconify block text-white w-5 h-5"
 data-icon="heroicons:user"
 data-inline="false"
></span>
 </div>
 <div class="ml-3">
 <div class="text-sm font-medium text-zinc-900">admin zenfleet</div>
 <div class="text-xs text-zinc-500">admin@zenfleet.dz</div>
 </div>
 </div>
 </div>

 
 <div class="py-1">
 <a href="http://localhost/profile"
 class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <span
 class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
 data-icon="heroicons:user-circle"
 data-inline="false"
></span>
 Mon Profil
 </a>
 <a href="#"
 class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <span
 class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
 data-icon="mdi:cog"
 data-inline="false"
></span>
 Paramètres
 </a>
 <a href="#"
 class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <span
 class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
 data-icon="heroicons:question-mark-circle"
 data-inline="false"
></span>
 Aide & Support
 </a>
 <div class="border-t border-zinc-100 my-1"></div>
 <form method="POST" action="http://localhost/logout">
 <input type="hidden" name="_token" value="IQ0VPQ01Kzv8dJRQYUbtefoBO83ydWF7Tl1yCwER" autocomplete="off"> <button type="submit"
 class="group flex w-full items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <span
 class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
 data-icon="heroicons:arrow-right-on-rectangle"
 data-inline="false"
></span>
 Se déconnecter
 </button>
 </form>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>

 <main class="lg:pl-64 py-10">
 <div class="px-4 sm:px-6 lg:px-8">
 <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('driverFormValidation', () => ({
            currentStep: 1,
            photoPreview: null,
            fieldErrors: {},
            touchedFields: {},

            init() {
                // Initialisation des champs si nécessaire
                this.fieldErrors = {
                    first_name: '',
                    last_name: '',
                    email: '',
                    personal_phone: '',
                    birth_date: '',
                    license_number: '',
                    license_categories: '',
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

                            },

            updatePhotoPreview(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.photoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            validateField(fieldName, value = null) {
                this.touchedFields[fieldName] = true;
                // Logique de validation simple côté client (optionnel mais recommandé pour l'UX immédiate)
                if (fieldName === 'license_categories' && (!value || value.length === 0)) {
                    this.fieldErrors[fieldName] = 'Au moins une catégorie est requise.';
                } else if (value === '') {
                    // Reset error if value is provided (very basic)
                    this.fieldErrors[fieldName] = '';
                }
            },

            nextStep() {
                if (this.currentStep < 4) {
                    this.currentStep++;
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            },

            onSubmit(event) {
                this.convertDatesBeforeSubmit(event);
            },

            convertDatesBeforeSubmit(event) {
                const form = event.target;
                const dateFields = [
                    'birth_date', 'recruitment_date', 'contract_end_date',
                    'license_issue_date', 'license_expiry_date'
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

                // Support dd/mm/yyyy
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
                console.log('Server Errors:', errors);

                // Map server errors to fieldErrors
                Object.keys(errors).forEach(field => {
                    this.fieldErrors[field] = errors[field][0];
                    this.touchedFields[field] = true;
                });

                // Mapping des champs vers les étapes
                const fieldToStepMap = {
                    'first_name': 1,
                    'last_name': 1,
                    'birth_date': 1,
                    'personal_phone': 1,
                    'address': 1,
                    'blood_type': 1,
                    'personal_email': 1,
                    'photo': 1,
                    'employee_number': 2,
                    'recruitment_date': 2,
                    'contract_end_date': 2,
                    'status_id': 2,
                    'notes': 2,
                    'license_number': 3,
                    'license_categories': 3,
                    'license_issue_date': 3,
                    'license_expiry_date': 3,
                    'license_authority': 3,
                    'license_verified': 3,
                    'user_id': 4,
                    'emergency_contact_name': 4,
                    'emergency_contact_phone': 4,
                    'emergency_contact_relationship': 4
                };

                // Trouver la première étape contenant une erreur
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
        }));
    });
</script>
 </div>
 </main>
 </div>
 </div>

 
 
 
 <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

 
 <script src="https://cdn.jsdelivr.net/npm/slim-select@2/dist/slimselect.min.js"></script>

 
 <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
 
 
 <script>
 document.addEventListener('DOMContentLoaded', function() {
 // ====================================================================
 // TOM SELECT - Initialisation Globale
 // ====================================================================
 document.querySelectorAll('.tomselect').forEach(function(el) {
 if (el.tomselect) return; // Déjà initialisé
 
 new TomSelect(el, {
 plugins: ['clear_button', 'remove_button'],
 maxOptions: 100,
 placeholder: el.getAttribute('data-placeholder') || 'Rechercher...',
 allowEmptyOption: true,
 create: false,
 sortField: {
 field: "text",
 direction: "asc"
 },
 render: {
 no_results: function(data, escape) {
 return '<div class="no-results p-2 text-sm text-gray-500">Aucun résultat trouvé</div>';
 }
 }
 });
 });

 // ====================================================================
 // FLATPICKR DATEPICKER - Initialisation Globale
 // ====================================================================
 document.querySelectorAll('.datepicker').forEach(function(el) {
 if (el._flatpickr) return; // Déjà initialisé
 
 const minDate = el.getAttribute('data-min-date');
 const maxDate = el.getAttribute('data-max-date');
 const dateFormat = el.getAttribute('data-date-format') || 'd/m/Y';

 flatpickr(el, {
 locale: 'fr',
 dateFormat: dateFormat,
 minDate: minDate,
 maxDate: maxDate,
 allowInput: true,
 disableMobile: true,
 nextArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>',
 prevArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>',
 });
 });

 // ====================================================================
 // FLATPICKR TIMEPICKER - Initialisation Globale avec Masque
 // ====================================================================
 
 // Fonction de masque de saisie pour le format HH:MM
 function applyTimeMask(input) {
 input.addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, ''); // Garder seulement les chiffres

 if (value.length >= 2) {
 // Limiter les heures à 23
 let hours = parseInt(value.substring(0, 2));
 if (hours > 23) hours = 23;

 let formattedValue = String(hours).padStart(2, '0');

 if (value.length >= 3) {
 // Limiter les minutes à 59
 let minutes = parseInt(value.substring(2, 4));
 if (minutes > 59) minutes = 59;
 formattedValue += ':' + String(minutes).padStart(2, '0');
 } else if (value.length === 2) {
 formattedValue += ':';
 }

 e.target.value = formattedValue;
 }
 });

 // Empêcher la suppression du ':'
 input.addEventListener('keydown', function(e) {
 if (e.key === 'Backspace') {
 const cursorPos = e.target.selectionStart;
 if (cursorPos === 3 && e.target.value.charAt(2) === ':') {
 e.preventDefault();
 e.target.value = e.target.value.substring(0, 2);
 }
 }
 });
 }
 
 document.querySelectorAll('.timepicker').forEach(function(el) {
 if (el._flatpickr) return; // Déjà initialisé
 
 const enableSeconds = el.getAttribute('data-enable-seconds') === 'true';

 // Appliquer le masque de saisie
 applyTimeMask(el);

 flatpickr(el, {
 enableTime: true,
 noCalendar: true,
 dateFormat: enableSeconds ? "H:i:S" : "H:i",
 time_24hr: true,
 allowInput: true,
 disableMobile: true,
 defaultHour: 0,
 defaultMinute: 0,
 });
 });
 });
 
 // ====================================================================
 // LIVEWIRE - Réinitialisation après mises à jour
 // ====================================================================
 document.addEventListener('livewire:navigated', function () {
 // Réinitialiser Tom Select
 document.querySelectorAll('.tomselect').forEach(function(el) {
 if (!el.tomselect) {
 new TomSelect(el, {
 plugins: ['clear_button', 'remove_button'],
 maxOptions: 100,
 placeholder: el.getAttribute('data-placeholder') || 'Rechercher...',
 allowEmptyOption: true,
 create: false,
 });
 }
 });
 
 // Réinitialiser Flatpickr
 document.querySelectorAll('.datepicker, .timepicker').forEach(function(el) {
 if (!el._flatpickr) {
 flatpickr(el, {
 locale: 'fr',
 allowInput: true,
 disableMobile: true,
 });
 }
 });
 });
 </script>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
 <script>
 document.addEventListener('DOMContentLoaded', function() {
 document.querySelectorAll('.datepicker').forEach(function(el) {
 const minDate = el.getAttribute('data-min-date');
 const maxDate = el.getAttribute('data-max-date');
 const dateFormat = el.getAttribute('data-date-format') || 'd/m/Y';

 flatpickr(el, {
 locale: 'fr',
 dateFormat: dateFormat,
 minDate: minDate,
 maxDate: maxDate,
 allowInput: true,
 disableMobile: true,
 nextArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>',
 prevArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>',
 });
 });
 });
 </script>
 <script>
    /**
     * ====================================================================
     * 🎯 SLIMSELECT ENTERPRISE INITIALIZATION
     * ====================================================================
     * Initialisation globale de SlimSelect avec style ZenFleet
     */
    function initializeSlimSelects() {
        document.querySelectorAll('[data-slimselect="true"]').forEach(function(el) {
            // Skip si déjà initialisé
            if (el.slimSelectInstance) return;

            const placeholder = el.getAttribute('data-placeholder') || 'Sélectionnez...';
            const isSearchable = el.getAttribute('data-searchable') !== 'false';

            try {
                const instance = new SlimSelect({
                    select: el,
                    settings: {
                        showSearch: isSearchable,
                        searchPlaceholder: 'Rechercher...',
                        searchText: 'Aucun résultat',
                        searchingText: 'Recherche...',
                        allowDeselect: true,
                        placeholderText: placeholder,
                        hideSelected: false,
                        contentLocation: document.body,
                        contentPosition: 'absolute'
                    },
                    events: {
                        afterChange: (newVal) => {
                            // Dispatch change event pour Alpine.js et autres listeners
                            el.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        },
                        afterOpen: () => {
                            // Focus sur le champ de recherche
                            const searchInput = document.querySelector('.ss-search input');
                            if (searchInput) {
                                setTimeout(() => searchInput.focus(), 50);
                            }
                        }
                    }
                });

                // Stocker l'instance pour référence
                el.slimSelectInstance = instance;
            } catch (e) {
                console.error('SlimSelect init error:', e);
            }
        });
    }

    // Initialiser au chargement du DOM
    document.addEventListener('DOMContentLoaded', initializeSlimSelects);

    // Réinitialiser après navigation Livewire
    document.addEventListener('livewire:navigated', initializeSlimSelects);

    // Support Alpine.js
    document.addEventListener('alpine:init', function() {
        Alpine.magic('slimselect', (el) => {
            return () => {
                const selectEl = el.querySelector('[data-slimselect="true"]');
                return selectEl?.slimSelectInstance;
            };
        });
    });
</script>
 

 
 <div x-data="toastManager()"
      @toast.window="showToast($event.detail)"
      class="fixed top-4 right-4 z-50 space-y-2"
      style="pointer-events: none;">
     <template x-for="(toast, index) in toasts" :key="toast.id">
         <div x-show="toast.show"
              x-transition:enter="transition ease-out duration-300 transform"
              x-transition:enter-start="opacity-0 translate-x-full"
              x-transition:enter-end="opacity-100 translate-x-0"
              x-transition:leave="transition ease-in duration-200 transform"
              x-transition:leave-start="opacity-100 translate-x-0"
              x-transition:leave-end="opacity-0 translate-x-full"
              class="max-w-md w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden"
              :class="{
                  'bg-green-50 border border-green-200': toast.type === 'success',
                  'bg-red-50 border border-red-200': toast.type === 'error',
                  'bg-blue-50 border border-blue-200': toast.type === 'info',
                  'bg-yellow-50 border border-yellow-200': toast.type === 'warning'
              }">
             <div class="p-4">
                 <div class="flex items-start">
                     <div class="flex-shrink-0">
                         <template x-if="toast.type === 'success'">
                             <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                             </svg>
                         </template>
                         <template x-if="toast.type === 'error'">
                             <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                             </svg>
                         </template>
                         <template x-if="toast.type === 'info'">
                             <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                             </svg>
                         </template>
                         <template x-if="toast.type === 'warning'">
                             <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                             </svg>
                         </template>
                     </div>
                     <div class="ml-3 flex-1">
                         <template x-if="toast.title">
                             <p class="text-sm font-semibold mb-1"
                                :class="{
                                    'text-green-900': toast.type === 'success',
                                    'text-red-900': toast.type === 'error',
                                    'text-blue-900': toast.type === 'info',
                                    'text-yellow-900': toast.type === 'warning'
                                }"
                                x-text="toast.title"></p>
                         </template>
                         <p class="text-sm"
                            :class="{
                                'text-green-800': toast.type === 'success',
                                'text-red-800': toast.type === 'error',
                                'text-blue-800': toast.type === 'info',
                                'text-yellow-800': toast.type === 'warning'
                            }"
                            x-text="toast.message || 'Notification'"></p>
                     </div>
                     <div class="ml-4 flex-shrink-0 flex">
                         <button @click="removeToast(toast.id)"
                                 class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                                 :class="{
                                     'text-green-500 hover:text-green-600 focus:ring-green-500': toast.type === 'success',
                                     'text-red-500 hover:text-red-600 focus:ring-red-500': toast.type === 'error',
                                     'text-blue-500 hover:text-blue-600 focus:ring-blue-500': toast.type === 'info',
                                     'text-yellow-500 hover:text-yellow-600 focus:ring-yellow-500': toast.type === 'warning'
                                 }">
                             <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                 <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 0 010-1.414z" clip-rule="evenodd" />
                             </svg>
                         </button>
                     </div>
                 </div>
            </div>
         </div>
         </div>
     </template>
 </div>

 <script>
 function toastManager() {
     return {
         toasts: [],
         counter: 0,

         showToast(detail) {
             const id = ++this.counter;
             const toast = {
                 id: id,
                 type: detail.type || 'info',
                 title: detail.title || '',
                 message: detail.message || 'Notification',
                 show: true
             };

             this.toasts.push(toast);

             // Auto-remove after 5 seconds
             setTimeout(() => {
                 this.removeToast(id);
             }, 5000);
         },

         removeToast(id) {
             const index = this.toasts.findIndex(t => t.id === id);
             if (index !== -1) {
                 this.toasts[index].show = false;
                 setTimeout(() => {
                     this.toasts.splice(index, 1);
                 }, 300);
             }
         }
     }
 }
 </script>
    <script data-navigate-once="true">window.livewireScriptConfig = {"csrf":"IQ0VPQ01Kzv8dJRQYUbtefoBO83ydWF7Tl1yCwER","uri":"\/livewire\/update","progressBar":"","nonce":""};</script>
</body>
</html>