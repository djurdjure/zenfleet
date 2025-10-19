{{--
 This form is used for both creating and editing documents.
 It expects the following variables:
 - $categories: A collection of DocumentCategory models.
 - $vehicles: A collection of vehicle models.
 - $drivers: A collection of user models (drivers).
 - $suppliers: A collection of supplier models.
 - $document (optional): The document model, only present when editing.
--}}

<div x-data="documentForm({
 categories: {{ json_encode($categories) }},
 initialCategoryId: '{{ old('document_category_id', isset($document) ? $document->document_category_id : '') }}',
 initialExtraMetadata: {{ json_encode(old('extra_metadata', isset($document) ? $document->extra_metadata : [])) }},
 allVehicles: {{ json_encode($vehicles->map(fn($v) => ['id' => $v->id, 'name' => "{$v->brand} {$v->model} ({$v->registration_plate})"])) }},
 allDrivers: {{ json_encode($drivers->map(fn($d) => ['id' => $d->id, 'name' => "{$d->first_name} {$d->last_name}"])) }},
 allSuppliers: {{ json_encode($suppliers) }}
})" x-init="init()">

 <form :action="actionUrl" method="POST" enctype="multipart/form-data" class="space-y-8">
 @csrf
 @if(isset($document))
 @method('PUT')
 @endif

 {{-- Section Fichier et Classification --}}
 <div class="p-6 border border-gray-200 rounded-lg">
 <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-6">
 Fichier et Classification
 </h3>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 @if(!isset($document))
 <div class="md:col-span-2">
 <x-input-label for="document_file" :value="__('Fichier à importer')" required />
 <x-text-input id="document_file" name="document_file" type="file" class="mt-1 block w-full" required />
 <p class="mt-2 text-sm text-gray-500">Types autorisés : PDF, DOCX, JPG, PNG, XLSX. Taille maximale : 10MB.</p>
 <x-input-error :messages="$errors->get('document_file')" class="mt-2" />
 </div>
 @endif

 <div>
 <x-input-label for="document_category_id" :value="__('Catégorie')" required />
 <select id="document_category_id" name="document_category_id" x-model="selectedCategoryId" @change="onCategoryChange" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" required>
 <option value="">-- Choisir une catégorie --</option>
 @foreach ($categories as $category)
 <option value="{{ $category->id }}">{{ $category->name }}</option>
 @endforeach
 </select>
 <x-input-error :messages="$errors->get('document_category_id')" class="mt-2" />
 </div>
 </div>
 </div>

 {{-- Section Champs Spécifiques (Dynamique) --}}
 <div x-show="selectedCategoryMetaSchema && selectedCategoryMetaSchema.fields.length > 0" class="p-6 border border-gray-200 rounded-lg bg-gray-50" style="display: none;">
 <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-6">
 Champs Spécifiques à la Catégorie
 </h3>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <template x-for="field in selectedCategoryMetaSchema.fields" :key="field.name">
 <div class="form-group" x-show="field.visible">
 <label :for="field.name" x-text="field.label" class="block font-medium text-sm text-gray-700"></label>
 
 <template x-if="field.type === 'string' || field.type === 'number' || field.type === 'date'">
 <input :type="field.type" :id="field.name" :name="'extra_metadata[' + field.name + ']'" :required="field.required" :disabled="!field.editable" x-model="extraMetadata[field.name]" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
 </template>
 <template x-if="field.type === 'textarea'">
 <textarea :id="field.name" :name="'extra_metadata[' + field.name + ']'" :required="field.required" :disabled="!field.editable" x-model="extraMetadata[field.name]" rows="3" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"></textarea>
 </template>
 <template x-if="field.type === 'multiselect'">
 <select :id="field.name" :name="'extra_metadata[' + field.name + '][]'" :required="field.required" :disabled="!field.editable" x-model="extraMetadata[field.name]" multiple :ref="'multiselect_' + field.name">
 <template x-for="option in field.options" :key="option">
 <option :value="option" x-text="option"></option>
 </template>
 </select>
 </template>
 <template x-if="field.type === 'entity_select'">
 <select :id="field.name" :name="'extra_metadata[' + field.name + ']'" :required="field.required" :disabled="!field.editable" x-model="extraMetadata[field.name]" :ref="'entity_select_' + field.name">
 <option value="">-- Sélectionner --</option>
 <template x-for="option in getEntityOptions(field.entity)" :key="option.id">
 <option :value="option.id" x-text="option.name"></option>
 </template>
 </select>
 </template>
 </div>
 </template>
 </div>
 </div>

 {{-- Section Liaisons --}}
 <div class="p-6 border border-gray-200 rounded-lg">
 <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-6">
 Lier le document à <span class="text-sm font-normal text-gray-500">(optionnel)</span>
 </h3>
 <div class="space-y-6">
 <div>
 <label for="linked_vehicles" class="block font-medium text-sm text-gray-700">Véhicules liés</label>
 <select id="linked_vehicles" name="linked_vehicles[]" multiple>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle->id }}" @selected(in_array($vehicle->id, old('linked_vehicles', isset($document) ? $document->vehicles->pluck('id')->toArray() : [])))>
 {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->registration_plate }})
 </option>
 @endforeach
 </select>
 </div>
 <div>
 <label for="linked_drivers" class="block font-medium text-sm text-gray-700">Chauffeurs liés</label>
 <select id="linked_drivers" name="linked_drivers[]" multiple>
 @foreach($drivers as $driver)
 <option value="{{ $driver->id }}" @selected(in_array($driver->id, old('linked_drivers', isset($document) ? $document->users->pluck('id')->toArray() : [])))>
 {{ $driver->first_name }} {{ $driver->last_name }}
 </option>
 @endforeach
 </select>
 </div>
 <div>
 <label for="linked_suppliers" class="block font-medium text-sm text-gray-700">Fournisseurs liés</label>
 <select id="linked_suppliers" name="linked_suppliers[]" multiple>
 @foreach($suppliers as $supplier)
 <option value="{{ $supplier->id }}" @selected(in_array($supplier->id, old('linked_suppliers', isset($document) ? $document->suppliers->pluck('id')->toArray() : [])))>
 {{ $supplier->name }}
 </option>
 @endforeach
 </select>
 </div>
 </div>
 </div>

 {{-- Section Dates --}}
 <div class="p-6 border border-gray-200 rounded-lg">
 <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-6">
 Dates de Validité <span class="text-sm font-normal text-gray-500">(optionnel)</span>
 </h3>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <x-date-picker id="issue_date" name="issue_date" label="Date d'émission" :value="old('issue_date', isset($document) ? $document->issue_date : null)" />
 <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
 </div>
 <div>
 <x-date-picker id="expiry_date" name="expiry_date" label="Date d'expiration" :value="old('expiry_date', isset($document) ? $document->expiry_date : null)" />
 <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
 </div>
 </div>
 </div>

 {{-- Section Description --}}
 <div class="p-6 border border-gray-200 rounded-lg">
 <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-6">
 Informations Complémentaires <span class="text-sm font-normal text-gray-500">(optionnel)</span>
 </h3>
 <div>
 <x-input-label for="description" :value="__('Description')" />
 <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">{{ old('description', isset($document) ? $document->description : '') }}</textarea>
 <x-input-error :messages="$errors->get('description')" class="mt-2" />
 </div>
 </div>

 {{-- Actions --}}
 <div class="flex items-center justify-end mt-8 pt-8 border-t border-gray-200">
 <a href="{{ route('admin.documents.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
 Annuler
 </a>
 <x-primary-button class="ml-4">
 <x-iconify icon="heroicons:arrow-up-tray" class="w-4 h-4 mr-2" / />
 {{ isset($document) ? __('Mettre à jour') : __('Importer le Document') }}
 </x-primary-button>
 </div>
 </form>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
 Alpine.data('documentForm', (config) => ({
 // --- Data ---
 categories: config.categories,
 selectedCategoryId: config.initialCategoryId,
 selectedCategoryMetaSchema: null,
 extraMetadata: config.initialExtraMetadata,
 allVehicles: config.allVehicles,
 allDrivers: config.allDrivers,
 allSuppliers: config.allSuppliers,
 actionUrl: @json(isset($document) ? route('admin.documents.update', $document) : route('admin.documents.store')),
 tomSelectInstances: [],

 // --- Methods ---
 init() {
 this.onCategoryChange();
 this.initializeTomSelect();
 },
 
 initializeTomSelect() {
 const tomSelectConfig = {
 plugins: ['remove_button'],
 sortField: { field: 'text', direction: 'asc' }
 };
 this.tomSelectInstances.push(new TomSelect(document.getElementById('linked_vehicles'), tomSelectConfig));
 this.tomSelectInstances.push(new TomSelect(document.getElementById('linked_drivers'), tomSelectConfig));
 this.tomSelectInstances.push(new TomSelect(document.getElementById('linked_suppliers'), tomSelectConfig));
 },

 onCategoryChange() {
 const selectedCategory = Object.values(this.categories).find(cat => cat.id == this.selectedCategoryId);
 this.selectedCategoryMetaSchema = selectedCategory ? selectedCategory.meta_schema : null;
 
 // Initialize dynamic Tom Select fields after DOM update
 this.$nextTick(() => {
 if (this.selectedCategoryMetaSchema && this.selectedCategoryMetaSchema.fields) {
 this.selectedCategoryMetaSchema.fields.forEach(field => {
 if (field.type === 'multiselect' || field.type === 'entity_select') {
 const el = this.$refs[field.type + '_' + field.name];
 if (el && !el.tomselect) { // Avoid re-initializing
 this.tomSelectInstances.push(new TomSelect(el, {
 plugins: ['remove_button'],
 sortField: { field: 'text', direction: 'asc' }
 }));
 }
 }
 });
 }
 });
 },

 getEntityOptions(entityType) {
 switch (entityType) {
 case 'vehicle': return this.allVehicles;
 case 'driver': return this.allDrivers;
 case 'supplier': return this.allSuppliers;
 default: return [];
 }
 },

 destroy() {
 this.tomSelectInstances.forEach(instance => instance.destroy());
 }
 }));
});
</script>
@endpush
