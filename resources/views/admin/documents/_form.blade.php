{{--
 Formulaire partagé création / édition de document.
 Variables attendues:
 - $categories, $vehicles, $drivers, $suppliers
 - $document (optionnel)
--}}

<div
    x-data="documentForm({
        categories: {{ json_encode($categories) }},
        initialCategoryId: '{{ old('document_category_id', isset($document) ? $document->document_category_id : '') }}',
        initialExtraMetadata: {{ json_encode(old('extra_metadata', isset($document) ? $document->extra_metadata : [])) }},
        allVehicles: {{ json_encode($vehicles->map(fn($v) => ['id' => $v->id, 'name' => "{$v->brand} {$v->model} ({$v->registration_plate})"])) }},
        allDrivers: {{ json_encode($drivers->map(fn($d) => ['id' => $d->id, 'name' => "{$d->first_name} {$d->last_name}"])) }},
        allSuppliers: {{ json_encode($suppliers->map(fn($s) => ['id' => $s->id, 'name' => $s->name])) }}
    })"
    x-init="init()">

    <form :action="actionUrl" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @if(isset($document))
            @method('PUT')
        @endif

        <x-form-section
            title="Fichier et Classification"
            icon="heroicons:document-arrow-up"
            subtitle="Ajoutez le fichier et rattachez-le à la catégorie métier appropriée.">
            <x-field-group :columns="2" :divided="false">
                @if(!isset($document))
                    <div class="md:col-span-2">
                        <label for="document_file" class="block mb-2 text-sm font-medium text-gray-600">
                            Fichier à importer <span class="text-red-600">*</span>
                        </label>
                        <input
                            id="document_file"
                            name="document_file"
                            type="file"
                            required
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="mt-2 text-xs text-gray-600">Types autorisés : PDF, DOCX, JPG, PNG, XLSX. Taille maximale : 10MB.</p>
                        @error('document_file')
                            <p class="mt-2 text-sm text-red-600 flex items-start font-medium">
                                <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label for="document_category_id" class="block mb-2 text-sm font-medium text-gray-600">
                        Catégorie <span class="text-red-600">*</span>
                    </label>
                    <select
                        id="document_category_id"
                        name="document_category_id"
                        x-model="selectedCategoryId"
                        @change="onCategoryChange"
                        required
                        class="block w-full p-2.5 bg-gray-50 border rounded-lg text-sm transition-all duration-200 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] {{ $errors->has('document_category_id') ? 'border-red-500' : 'border-gray-300' }}">
                        <option value="">Sélectionner une catégorie...</option>
                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected((string) old('document_category_id', isset($document) ? $document->document_category_id : '') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('document_category_id')
                        <p class="mt-2 text-sm text-red-600 flex items-start font-medium">
                            <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>
            </x-field-group>
        </x-form-section>

        <x-form-section
            x-show="selectedCategoryMetaSchema && selectedCategoryMetaSchema.fields && selectedCategoryMetaSchema.fields.length > 0"
            style="display: none;"
            title="Métadonnées Spécifiques"
            icon="heroicons:adjustments-horizontal"
            subtitle="Champs dynamiques pilotés par la catégorie sélectionnée.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <template x-for="field in selectedCategoryMetaSchema.fields" :key="field.name">
                    <div x-show="field.visible !== false">
                        <label :for="field.name" class="block mb-2 text-sm font-medium text-gray-600">
                            <span x-text="field.label"></span>
                            <span x-show="field.required" class="text-red-600">*</span>
                        </label>

                        <template x-if="field.type === 'string' || field.type === 'number' || field.type === 'date'">
                            <input
                                :type="field.type"
                                :id="field.name"
                                :name="'extra_metadata[' + field.name + ']'"
                                :required="field.required"
                                :disabled="!field.editable"
                                x-model="extraMetadata[field.name]"
                                class="block w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm transition-all duration-200 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]" />
                        </template>

                        <template x-if="field.type === 'textarea'">
                            <textarea
                                :id="field.name"
                                :name="'extra_metadata[' + field.name + ']'"
                                :required="field.required"
                                :disabled="!field.editable"
                                x-model="extraMetadata[field.name]"
                                rows="3"
                                class="block w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm transition-all duration-200 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]"></textarea>
                        </template>

                        <template x-if="field.type === 'multiselect'">
                            <select
                                :id="field.name"
                                :name="'extra_metadata[' + field.name + '][]'"
                                :required="field.required"
                                :disabled="!field.editable"
                                x-model="extraMetadata[field.name]"
                                multiple
                                :ref="'multiselect_' + field.name">
                                <template x-for="option in field.options" :key="option">
                                    <option :value="option" x-text="option"></option>
                                </template>
                            </select>
                        </template>

                        <template x-if="field.type === 'entity_select'">
                            <select
                                :id="field.name"
                                :name="'extra_metadata[' + field.name + ']'"
                                :required="field.required"
                                :disabled="!field.editable"
                                x-model="extraMetadata[field.name]"
                                :ref="'entity_select_' + field.name">
                                <option value="">Sélectionner...</option>
                                <template x-for="option in getEntityOptions(field.entity)" :key="option.id">
                                    <option :value="option.id" x-text="option.name"></option>
                                </template>
                            </select>
                        </template>
                    </div>
                </template>
            </div>
        </x-form-section>

        @php
            $selectedVehicleIds = old('linked_vehicles', isset($document) ? $document->vehicles->pluck('id')->toArray() : []);
            $selectedDriverIds = old('linked_drivers', isset($document) ? $document->users->pluck('id')->toArray() : []);
            $selectedSupplierIds = old('linked_suppliers', isset($document) ? $document->suppliers->pluck('id')->toArray() : []);
            $vehicleOptions = $vehicles->mapWithKeys(fn($vehicle) => [$vehicle->id => "{$vehicle->brand} {$vehicle->model} ({$vehicle->registration_plate})"])->toArray();
            $driverOptions = $drivers->mapWithKeys(fn($driver) => [$driver->id => "{$driver->first_name} {$driver->last_name}"])->toArray();
            $supplierOptions = $suppliers->mapWithKeys(fn($supplier) => [$supplier->id => $supplier->name])->toArray();
        @endphp

        <x-form-section
            title="Liaisons"
            icon="heroicons:link"
            subtitle="Rattachez le document aux entités de la flotte (optionnel).">
            <x-field-group :columns="1" :divided="false">
                <x-slim-select
                    name="linked_vehicles"
                    label="Véhicules liés"
                    :options="$vehicleOptions"
                    :selected="$selectedVehicleIds"
                    :multiple="true"
                    placeholder="Sélectionner un ou plusieurs véhicules..."
                    :error="$errors->first('linked_vehicles')" />

                <x-slim-select
                    name="linked_drivers"
                    label="Chauffeurs liés"
                    :options="$driverOptions"
                    :selected="$selectedDriverIds"
                    :multiple="true"
                    placeholder="Sélectionner un ou plusieurs chauffeurs..."
                    :error="$errors->first('linked_drivers')" />

                <x-slim-select
                    name="linked_suppliers"
                    label="Fournisseurs liés"
                    :options="$supplierOptions"
                    :selected="$selectedSupplierIds"
                    :multiple="true"
                    placeholder="Sélectionner un ou plusieurs fournisseurs..."
                    :error="$errors->first('linked_suppliers')" />
            </x-field-group>
        </x-form-section>

        <x-form-section
            title="Période de Validité"
            icon="heroicons:calendar-days"
            subtitle="Dates de référence du document (optionnel).">
            <x-field-group :columns="2">
                <x-datepicker
                    name="issue_date"
                    label="Date d'émission"
                    :value="old('issue_date', isset($document) ? $document->issue_date : null)"
                    :error="$errors->first('issue_date')"
                    placeholder="Choisir une date" />

                <x-datepicker
                    name="expiry_date"
                    label="Date d'expiration"
                    :value="old('expiry_date', isset($document) ? $document->expiry_date : null)"
                    :error="$errors->first('expiry_date')"
                    placeholder="Choisir une date" />
            </x-field-group>
        </x-form-section>

        <x-form-section
            title="Informations Complémentaires"
            icon="heroicons:document-text"
            subtitle="Contexte métier et description libre (optionnel).">
            <x-textarea
                name="description"
                label="Description"
                rows="4"
                placeholder="Ajoutez un contexte utile pour ce document..."
                :value="old('description', isset($document) ? $document->description : '')"
                :error="$errors->first('description')" />
        </x-form-section>

        <div class="relative pl-14">
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="px-6 py-4 flex items-center justify-between gap-3">
                    <a href="{{ route('admin.documents.index') }}"
                        class="inline-flex items-center justify-center h-10 px-4 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] transition-all duration-200">
                        Annuler
                    </a>

                    <x-button
                        type="submit"
                        variant="primary"
                        icon="arrow-up-tray">
                        {{ isset($document) ? 'Mettre à jour le Document' : 'Importer le Document' }}
                    </x-button>
                </div>
            </section>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('documentForm', (config) => ({
        categories: config.categories,
        selectedCategoryId: config.initialCategoryId,
        selectedCategoryMetaSchema: null,
        extraMetadata: config.initialExtraMetadata,
        allVehicles: config.allVehicles,
        allDrivers: config.allDrivers,
        allSuppliers: config.allSuppliers,
        actionUrl: @json(isset($document) ? route('admin.documents.update', $document) : route('admin.documents.store')),
        tomSelectInstances: [],

        init() {
            this.onCategoryChange();
        },

        onCategoryChange() {
            const selectedCategory = Object.values(this.categories).find((cat) => String(cat.id) === String(this.selectedCategoryId));
            this.selectedCategoryMetaSchema = selectedCategory ? selectedCategory.meta_schema : null;

            this.$nextTick(() => {
                if (!window.TomSelect) {
                    return;
                }

                if (this.selectedCategoryMetaSchema && this.selectedCategoryMetaSchema.fields) {
                    this.selectedCategoryMetaSchema.fields.forEach((field) => {
                        if (field.type === 'multiselect' || field.type === 'entity_select') {
                            const refName = field.type + '_' + field.name;
                            const element = this.$refs[refName];

                            if (element && !element.tomselect) {
                                this.tomSelectInstances.push(new TomSelect(element, {
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
                case 'vehicle':
                    return this.allVehicles;
                case 'driver':
                    return this.allDrivers;
                case 'supplier':
                    return this.allSuppliers;
                default:
                    return [];
            }
        },

        destroy() {
            this.tomSelectInstances.forEach((instance) => instance.destroy());
        }
    }));
});
</script>
@endpush
