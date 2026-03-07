@extends('layouts.admin.catalyst')

@section('title', 'Nouvelle planification maintenance')

@section('content')
<section class="zf-page min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-10 lg:mx-0">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-600">Nouvelle planification maintenance</h1>
            <p class="mt-1 text-xs text-gray-600">Configurez une maintenance preventive recurrante pour un vehicule.</p>
        </div>

        @if(session('error'))
            <x-alert type="error" title="Erreur" dismissible class="mb-6">
                {{ session('error') }}
            </x-alert>
        @endif

        @if ($errors->any())
            <x-alert type="error" title="Erreurs de validation" dismissible class="mb-6">
                Veuillez corriger les erreurs suivantes:
                <ul class="mt-2 ml-5 list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form method="POST" action="{{ route('admin.maintenance.schedules.store') }}" class="space-y-8">
            @csrf

            <x-form-section
                title="Contexte de planification"
                icon="lucide:car-front"
                subtitle="Selection du vehicule et du type de maintenance recurrante.">
                <x-field-group :columns="2">
                    <x-slim-select name="vehicle_id" label="Vehicule" required :error="$errors->first('vehicle_id')" placeholder="Selectionner un vehicule">
                        <option value="" data-placeholder="true">Selectionner un vehicule</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                            </option>
                        @endforeach
                    </x-slim-select>

                    <x-slim-select name="maintenance_type_id" label="Type de maintenance" required :error="$errors->first('maintenance_type_id')" placeholder="Selectionner un type">
                        <option value="" data-placeholder="true">Selectionner un type</option>
                        @foreach($maintenanceTypes as $type)
                            <option
                                value="{{ $type->id }}"
                                data-interval-km="{{ (int) ($type->default_interval_km ?? 0) }}"
                                data-interval-days="{{ (int) ($type->default_interval_days ?? 0) }}"
                                {{ old('maintenance_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ ucfirst($type->category) }})
                            </option>
                        @endforeach
                    </x-slim-select>
                </x-field-group>
            </x-form-section>

            <x-form-section
                title="Regles de recurrence"
                icon="lucide:repeat"
                subtitle="Definissez le declenchement par kilometrage, duree ou combinaison des deux.">
                <x-field-group :columns="3" :divided="false">
                    <x-slim-select
                        name="interval_type"
                        label="Type d'intervalle"
                        required
                        :error="$errors->first('interval_type')"
                        placeholder="Selectionner un mode">
                        <option value="" data-placeholder="true">Selectionner un mode</option>
                        <option value="mileage" {{ old('interval_type') === 'mileage' ? 'selected' : '' }}>Kilometrage</option>
                        <option value="time" {{ old('interval_type') === 'time' ? 'selected' : '' }}>Temps</option>
                        <option value="both" {{ old('interval_type') === 'both' ? 'selected' : '' }}>Kilometrage + Temps</option>
                    </x-slim-select>

                    <x-input
                        type="number"
                        name="interval_value_km"
                        label="Intervalle kilometrique (km)"
                        icon="chart-bar"
                        :value="old('interval_value_km')"
                        :error="$errors->first('interval_value_km')"
                        min="1"
                        placeholder="Ex: 10000"
                        helpText="Laissez vide si non utilise." />

                    <x-input
                        type="number"
                        name="interval_value_days"
                        label="Intervalle temporel (jours)"
                        icon="calendar"
                        :value="old('interval_value_days')"
                        :error="$errors->first('interval_value_days')"
                        min="1"
                        placeholder="Ex: 180"
                        helpText="Laissez vide si non utilise." />
                </x-field-group>
            </x-form-section>

            <x-form-section
                title="Point de depart"
                icon="lucide:calendar-clock"
                subtitle="Base de calcul de la prochaine operation generee par la planification.">
                <x-field-group :columns="2">
                    <x-datepicker
                        name="last_maintenance_date"
                        label="Date de derniere maintenance"
                        :value="old('last_maintenance_date')"
                        :error="$errors->first('last_maintenance_date')"
                        placeholder="JJ/MM/AAAA"
                        helpText="Optionnel, utile pour initialiser les prochaines echeances." />

                    <x-input
                        type="number"
                        name="last_maintenance_mileage"
                        label="Kilometrage de derniere maintenance"
                        icon="chart-bar"
                        :value="old('last_maintenance_mileage')"
                        :error="$errors->first('last_maintenance_mileage')"
                        min="0"
                        placeholder="Ex: 56000"
                        helpText="Optionnel, en kilometres." />
                </x-field-group>
            </x-form-section>

            <x-form-section
                title="Activation"
                icon="lucide:shield-check"
                subtitle="Controlez l'etat de la planification a la creation.">
                <div class="grid grid-cols-1 gap-4">
                    <label class="inline-flex items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="mt-1 rounded border-gray-300 text-[#0c90ee] focus:ring-[#0c90ee]/20"
                            {{ old('is_active', '1') ? 'checked' : '' }}>
                        <span>
                            <span class="block text-sm font-medium text-gray-700">Activer immediatement</span>
                            <span class="block text-xs text-gray-500 mt-0.5">La planification pourra generer des operations des son prochain cycle.</span>
                        </span>
                    </label>
                </div>
            </x-form-section>

            <div class="relative pl-14">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="px-6 py-4">
                        <div class="flex flex-wrap items-center justify-end gap-3">
                            <a
                                href="{{ route('admin.maintenance.schedules.index') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-gray-700">
                                Annuler
                            </a>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg border border-[#0c90ee] bg-[#0c90ee] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:border-[#0a7fd1] hover:bg-[#0a7fd1]">
                                Creer la planification
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const typeSelect = document.querySelector('select[name="maintenance_type_id"]');
        const intervalKmInput = document.querySelector('input[name="interval_value_km"]');
        const intervalDaysInput = document.querySelector('input[name="interval_value_days"]');

        if (!typeSelect || !intervalKmInput || !intervalDaysInput) return;

        const shouldFill = (input) => input.value === '' || input.value === null;

        typeSelect.addEventListener('change', () => {
            const option = typeSelect.options[typeSelect.selectedIndex];
            if (!option) return;

            const intervalKm = option.dataset.intervalKm;
            const intervalDays = option.dataset.intervalDays;

            if (intervalKm && parseInt(intervalKm, 10) > 0 && shouldFill(intervalKmInput)) {
                intervalKmInput.value = intervalKm;
            }

            if (intervalDays && parseInt(intervalDays, 10) > 0 && shouldFill(intervalDaysInput)) {
                intervalDaysInput.value = intervalDays;
            }
        });
    });
</script>
@endpush
