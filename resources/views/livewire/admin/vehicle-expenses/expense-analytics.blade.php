<div class="space-y-6">
    {{-- En-tete --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Analytics des dépenses</h1>
            <p class="text-gray-600">Analyse détaillée de vos dépenses de flotte</p>
        </div>

        <div class="flex items-center gap-3">
            <button
                wire:click="generateReport"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white transition hover:bg-indigo-700"
            >
                <x-iconify icon="solar:document-text-bold" class="h-5 w-5" />
                <span>Rapport PDF</span>
            </button>

            <div
                class="relative"
                x-data="{
                    open: false,
                    styles: '',
                    direction: 'down',
                    align: 'right',
                    toggle() {
                        if (this.open) {
                            this.close();
                            return;
                        }

                        this.open = true;
                        this.$nextTick(() => {
                            this.updatePosition();
                            requestAnimationFrame(() => this.updatePosition());
                        });
                    },
                    close() {
                        this.open = false;
                    },
                    updatePosition() {
                        if (!this.$refs.trigger || !this.$refs.menu) {
                            return;
                        }

                        const rect = this.$refs.trigger.getBoundingClientRect();
                        const menuHeight = this.$refs.menu.offsetHeight || 180;
                        const menuWidth = this.$refs.menu.offsetWidth || 192;
                        const padding = 12;
                        const spaceBelow = window.innerHeight - rect.bottom;
                        const spaceAbove = rect.top;
                        const shouldOpenUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;

                        this.direction = shouldOpenUp ? 'up' : 'down';

                        let top = shouldOpenUp ? (rect.top - menuHeight - 8) : (rect.bottom + 8);
                        if (top + menuHeight > window.innerHeight - padding) {
                            top = window.innerHeight - padding - menuHeight;
                        }
                        if (top < padding) {
                            top = padding;
                        }

                        let left = this.align === 'right' ? (rect.right - menuWidth) : rect.left;
                        if (left + menuWidth > window.innerWidth - padding) {
                            left = window.innerWidth - padding - menuWidth;
                        }
                        if (left < padding) {
                            left = padding;
                        }

                        this.styles = `position: fixed; top: ${top}px; left: ${left}px; width: ${menuWidth}px; z-index: 9999;`;
                    }
                }"
                x-init="$watch('open', value => {
                    if (value) {
                        $nextTick(() => {
                            this.updatePosition();
                            requestAnimationFrame(() => this.updatePosition());
                        });
                    }
                })"
                @keydown.escape.window="close()"
                @scroll.window="open && updatePosition()"
                @resize.window="open && updatePosition()"
            >
                <button
                    @click="toggle()"
                    x-ref="trigger"
                    class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-white transition hover:bg-gray-700"
                >
                    <x-iconify icon="solar:export-bold" class="h-5 w-5" />
                    <span>Exporter</span>
                </button>

                <template x-teleport="body">
                    <div
                        x-show="open"
                        x-ref="menu"
                        @click.outside="close()"
                        x-transition
                        :style="styles"
                        class="z-[9999] rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                        x-cloak
                    >
                        <button
                            wire:click="exportData('csv')"
                            @click="close()"
                            class="block w-full px-4 py-2 text-left hover:bg-gray-100"
                        >
                            Export CSV
                        </button>
                        <button
                            wire:click="exportData('excel')"
                            @click="close()"
                            class="block w-full px-4 py-2 text-left hover:bg-gray-100"
                        >
                            Export Excel
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <x-card>
        <div class="space-y-4 p-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div>
                    <x-input-label value="Période" />
                    <x-select wire:model.live="period" class="w-full">
                        <option value="month">Ce mois</option>
                        <option value="quarter">Ce trimestre</option>
                        <option value="year">Cette année</option>
                        <option value="custom">Personnalisé</option>
                    </x-select>
                </div>

                @if ($period === 'custom')
                    <div>
                        <x-input-label value="Date début" />
                        <x-input type="date" wire:model.live="startDate" class="w-full" />
                    </div>
                    <div>
                        <x-input-label value="Date fin" />
                        <x-input type="date" wire:model.live="endDate" class="w-full" />
                    </div>
                @endif

                <div>
                    <x-input-label value="Véhicule" />
                    <x-select wire:model.live="vehicle_id" class="w-full">
                        <option value="">Tous les véhicules</option>
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }}</option>
                        @endforeach
                    </x-select>
                </div>

                <div>
                    <x-input-label value="Catégorie" />
                    <x-select wire:model.live="category" class="w-full">
                        <option value="">Toutes les catégories</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </x-select>
                </div>

                <div>
                    <x-input-label value="Groupe" />
                    <x-select wire:model.live="expense_group_id" class="w-full">
                        <option value="">Tous les groupes</option>
                        @foreach ($expenseGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </x-select>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Onglets --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button
                wire:click="$set('viewMode', 'dashboard')"
                class="border-b-2 px-1 py-2 text-sm font-medium {{ $viewMode === 'dashboard' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
            >
                Dashboard
            </button>
            <button
                wire:click="$set('viewMode', 'tco')"
                class="border-b-2 px-1 py-2 text-sm font-medium {{ $viewMode === 'tco' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
            >
                TCO Véhicules
            </button>
            <button
                wire:click="$set('viewMode', 'trends')"
                class="border-b-2 px-1 py-2 text-sm font-medium {{ $viewMode === 'trends' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
            >
                Tendances
            </button>
            <button
                wire:click="$set('viewMode', 'suppliers')"
                class="border-b-2 px-1 py-2 text-sm font-medium {{ $viewMode === 'suppliers' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
            >
                Fournisseurs
            </button>
            <button
                wire:click="$set('viewMode', 'budgets')"
                class="border-b-2 px-1 py-2 text-sm font-medium {{ $viewMode === 'budgets' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
            >
                Budgets
            </button>
        </nav>
    </div>

    {{-- Contenu selon le mode d'affichage --}}
    @switch($viewMode)
        @case('dashboard')
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                @if ($dashboardStats)
                    <x-card class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total dépenses</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($dashboardStats['total_amount'] ?? 0, 2) }} DZD</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $dashboardStats['expense_count'] ?? 0 }} dépenses</p>
                            </div>
                            <div class="rounded-lg bg-blue-100 p-3">
                                <x-iconify icon="solar:wallet-bold-duotone" class="h-6 w-6 text-blue-600" />
                            </div>
                        </div>
                    </x-card>

                    <x-card class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Moyenne/véhicule</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($dashboardStats['avg_per_vehicle'] ?? 0, 2) }} DZD</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $dashboardStats['vehicle_count'] ?? 0 }} véhicules</p>
                            </div>
                            <div class="rounded-lg bg-green-100 p-3">
                                <x-iconify icon="solar:car-bold-duotone" class="h-6 w-6 text-green-600" />
                            </div>
                        </div>
                    </x-card>

                    <x-card class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Top catégorie</p>
                                <p class="text-xl font-bold text-gray-900">{{ $dashboardStats['top_category']['name'] ?? 'N/A' }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ number_format($dashboardStats['top_category']['amount'] ?? 0, 0) }} DZD</p>
                            </div>
                            <div class="rounded-lg bg-indigo-100 p-3">
                                <x-iconify icon="solar:tag-bold-duotone" class="h-6 w-6 text-indigo-600" />
                            </div>
                        </div>
                    </x-card>

                    <x-card class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Score conformité</p>
                                <p class="text-2xl font-bold {{ $complianceScore >= 80 ? 'text-green-600' : ($complianceScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">{{ $complianceScore }}%</p>
                                <p class="mt-1 text-xs text-gray-500">Documents &amp; approbations</p>
                            </div>
                            <div class="rounded-lg bg-purple-100 p-3">
                                <x-iconify icon="solar:shield-check-bold-duotone" class="h-6 w-6 text-purple-600" />
                            </div>
                        </div>
                    </x-card>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @if ($pieChartData)
                    <x-card>
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-semibold">Répartition par catégorie</h3>
                            <div class="h-64">
                                @php
                                    $categoryPiePayload = [
                                        'meta' => [
                                            'source' => 'expense.analytics.dashboard.category-breakdown',
                                            'period' => $period,
                                            'filters' => [
                                                'start_date' => $startDate,
                                                'end_date' => $endDate,
                                                'vehicle_id' => $vehicle_id ?: null,
                                                'category' => $category ?: null,
                                                'expense_group_id' => $expense_group_id ?: null,
                                            ],
                                        ],
                                        'chart' => [
                                            'id' => 'expense-category-pie',
                                            'type' => 'donut',
                                            'height' => 260,
                                            'ariaLabel' => 'Repartition des depenses par categorie',
                                        ],
                                        'labels' => $pieChartData['labels'] ?? [],
                                        'series' => $pieChartData['datasets'][0]['data'] ?? [],
                                        'options' => [],
                                    ];
                                @endphp
                                <x-charts.widget
                                    id="categoryPieChart"
                                    :payload="$categoryPiePayload"
                                    wire-ignore
                                />
                            </div>
                        </div>
                    </x-card>
                @endif

                @if ($chartData)
                    <x-card>
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-semibold">Évolution mensuelle</h3>
                            <div class="h-64">
                                @php
                                    $monthlyTrendPayload = [
                                        'meta' => [
                                            'source' => 'expense.analytics.dashboard.monthly-trend',
                                            'period' => $period,
                                            'filters' => [
                                                'start_date' => $startDate,
                                                'end_date' => $endDate,
                                                'vehicle_id' => $vehicle_id ?: null,
                                                'category' => $category ?: null,
                                                'expense_group_id' => $expense_group_id ?: null,
                                            ],
                                        ],
                                        'chart' => [
                                            'id' => 'expense-monthly-trend',
                                            'type' => 'line',
                                            'height' => 260,
                                            'ariaLabel' => 'Evolution mensuelle des depenses',
                                        ],
                                        'labels' => $chartLabels ?? [],
                                        'series' => collect($chartData['datasets'] ?? [])
                                            ->map(fn ($dataset) => [
                                                'name' => $dataset['label'] ?? 'Serie',
                                                'data' => $dataset['data'] ?? [],
                                            ])
                                            ->values()
                                            ->all(),
                                        'options' => [],
                                    ];
                                @endphp
                                <x-charts.widget
                                    id="monthlyTrendChart"
                                    :payload="$monthlyTrendPayload"
                                    wire-ignore
                                />
                            </div>
                        </div>
                    </x-card>
                @endif
            </div>

            @if ($vehicleCosts && count($vehicleCosts) > 0)
                <x-card>
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">Top 10 véhicules par coût</h3>
                        <div class="space-y-3">
                            @foreach ($vehicleCosts as $vehicleCost)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <x-iconify icon="solar:car-bold" class="h-5 w-5 text-gray-400" />
                                        <div>
                                            <p class="font-medium">{{ $vehicleCost['license_plate'] ?? 'N/A' }}</p>
                                            <p class="text-sm text-gray-500">{{ $vehicleCost['brand'] ?? '' }} {{ $vehicleCost['model'] ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">{{ number_format($vehicleCost['total_cost'] ?? 0, 2) }} DZD</p>
                                        <p class="text-sm text-gray-500">{{ $vehicleCost['expense_count'] ?? 0 }} dépenses</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </x-card>
            @endif
            @break

        @case('tco')
            @if ($tcoData)
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <x-card class="p-4">
                        <p class="text-sm font-medium text-gray-600">TCO Total Flotte</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($tcoData['total_tco'] ?? 0, 2) }} DZD</p>
                    </x-card>
                    <x-card class="p-4">
                        <p class="text-sm font-medium text-gray-600">TCO Moyen/Véhicule</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($tcoData['avg_tco'] ?? 0, 2) }} DZD</p>
                    </x-card>
                    <x-card class="p-4">
                        <p class="text-sm font-medium text-gray-600">Coût/km moyen</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($tcoData['avg_cost_per_km'] ?? 0, 2) }} DZD/km</p>
                    </x-card>
                </div>

                @if ($chartLabels && is_array($chartData) && !array_key_exists('datasets', $chartData))
                    <x-card>
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-semibold">TCO par véhicule</h3>
                            <div class="h-96">
                                @php
                                    $tcoPayload = [
                                        'meta' => [
                                            'source' => 'expense.analytics.tco.by-vehicle',
                                            'period' => $period,
                                            'filters' => [
                                                'start_date' => $startDate,
                                                'end_date' => $endDate,
                                                'vehicle_id' => $vehicle_id ?: null,
                                                'category' => $category ?: null,
                                                'expense_group_id' => $expense_group_id ?: null,
                                            ],
                                        ],
                                        'chart' => [
                                            'id' => 'expense-tco-vehicles',
                                            'type' => 'bar',
                                            'height' => 360,
                                            'ariaLabel' => 'TCO par vehicule',
                                        ],
                                        'labels' => $chartLabels ?? [],
                                        'series' => [[
                                            'name' => 'TCO (DZD)',
                                            'data' => array_values($chartData ?? []),
                                        ]],
                                        'options' => [
                                            'plotOptions' => ['bar' => ['borderRadius' => 6, 'columnWidth' => '48%']],
                                            'yaxis' => ['min' => 0],
                                        ],
                                    ];
                                @endphp
                                <x-charts.widget
                                    id="tcoChart"
                                    :payload="$tcoPayload"
                                    wire-ignore
                                />
                            </div>
                        </div>
                    </x-card>
                @endif
            @endif
            @break

        @case('trends')
            <div class="space-y-6">
                @if ($trends)
                    <x-card>
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-semibold">Tendances des dépenses</h3>
                            <div class="h-96">
                                @php
                                    $trendsPayload = [
                                        'meta' => [
                                            'source' => 'expense.analytics.trends.monthly',
                                            'period' => $period,
                                            'filters' => [
                                                'start_date' => $startDate,
                                                'end_date' => $endDate,
                                                'vehicle_id' => $vehicle_id ?: null,
                                                'category' => $category ?: null,
                                                'expense_group_id' => $expense_group_id ?: null,
                                            ],
                                        ],
                                        'chart' => [
                                            'id' => 'expense-trends-monthly',
                                            'type' => 'line',
                                            'height' => 360,
                                            'ariaLabel' => 'Tendances mensuelles des depenses',
                                        ],
                                        'labels' => collect($trends['monthly']['periods'] ?? [])->pluck('label')->values()->all(),
                                        'series' => [
                                            [
                                                'name' => 'Montant (DZD)',
                                                'data' => collect($trends['monthly']['periods'] ?? [])->pluck('total_amount')->values()->all(),
                                            ],
                                            [
                                                'name' => 'Nombre de depenses',
                                                'data' => collect($trends['monthly']['periods'] ?? [])->pluck('expense_count')->values()->all(),
                                            ],
                                        ],
                                        'options' => [
                                            'stroke' => ['curve' => 'smooth', 'width' => 3],
                                            'yaxis' => ['min' => 0],
                                        ],
                                    ];
                                @endphp
                                <x-charts.widget
                                    id="trendsChart"
                                    :payload="$trendsPayload"
                                    wire-ignore
                                />
                            </div>
                        </div>
                    </x-card>
                @endif

                @if ($predictions)
                    <x-card>
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-semibold">Prédictions (3 prochains mois)</h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                @foreach ($predictions['monthly'] ?? [] as $month => $prediction)
                                    <div class="rounded-lg border p-4">
                                        <p class="text-sm text-gray-600">{{ $month }}</p>
                                        <p class="text-xl font-bold">{{ number_format($prediction['amount'] ?? 0, 2) }} DZD</p>
                                        <p class="text-sm text-gray-500">± {{ number_format($prediction['confidence'] ?? 0, 0) }}%</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </x-card>
                @endif
            </div>
            @break

        @case('suppliers')
            @if ($supplierAnalysis)
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <x-card>
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-semibold">Top fournisseurs</h3>
                            @if (isset($supplierAnalysis['top_suppliers']))
                                <div class="space-y-3">
                                    @foreach ($supplierAnalysis['top_suppliers'] as $supplier)
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium">{{ $supplier['name'] ?? 'N/A' }}</p>
                                                <p class="text-sm text-gray-500">{{ $supplier['expense_count'] ?? 0 }} factures</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold">{{ number_format($supplier['total_amount'] ?? 0, 2) }} DZD</p>
                                                <p class="text-sm {{ ($supplier['payment_delay'] ?? 0) > 30 ? 'text-red-500' : 'text-green-500' }}">Délai: {{ $supplier['payment_delay'] ?? 0 }}j</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </x-card>

                    @if (isset($supplierAnalysis['top_suppliers']))
                        <x-card>
                            <div class="p-6">
                                <h3 class="mb-4 text-lg font-semibold">Répartition par fournisseur</h3>
                                <div class="h-64">
                                    @php
                                        $supplierPayload = [
                                            'meta' => [
                                                'source' => 'expense.analytics.suppliers.distribution',
                                                'period' => $period,
                                                'filters' => [
                                                    'start_date' => $startDate,
                                                    'end_date' => $endDate,
                                                    'vehicle_id' => $vehicle_id ?: null,
                                                    'category' => $category ?: null,
                                                    'expense_group_id' => $expense_group_id ?: null,
                                                ],
                                            ],
                                            'chart' => [
                                                'id' => 'expense-suppliers-distribution',
                                                'type' => 'donut',
                                                'height' => 260,
                                                'ariaLabel' => 'Repartition des depenses par fournisseur',
                                            ],
                                            'labels' => collect($supplierAnalysis['top_suppliers'] ?? [])->pluck('name')->values()->all(),
                                            'series' => collect($supplierAnalysis['top_suppliers'] ?? [])->pluck('total_amount')->map(fn ($v) => (float) $v)->values()->all(),
                                            'options' => [],
                                        ];
                                    @endphp
                                    <x-charts.widget
                                        id="supplierChart"
                                        :payload="$supplierPayload"
                                        wire-ignore
                                    />
                                </div>
                            </div>
                        </x-card>
                    @endif
                </div>
            @endif
            @break

        @case('budgets')
            @if ($budgetAnalysis)
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <x-card class="p-4">
                            <p class="text-sm font-medium text-gray-600">Budget total alloué</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($budgetAnalysis['total_allocated'] ?? 0, 2) }} DZD</p>
                        </x-card>
                        <x-card class="p-4">
                            <p class="text-sm font-medium text-gray-600">Budget utilisé</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($budgetAnalysis['total_used'] ?? 0, 2) }} DZD</p>
                        </x-card>
                        <x-card class="p-4">
                            <p class="text-sm font-medium text-gray-600">Budget restant</p>
                            <p class="text-2xl font-bold {{ ($budgetAnalysis['total_remaining'] ?? 0) < 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($budgetAnalysis['total_remaining'] ?? 0, 2) }} DZD</p>
                        </x-card>
                    </div>

                    @if (isset($budgetAnalysis['groups']) && count($budgetAnalysis['groups']) > 0)
                        <x-card>
                            <div class="p-6">
                                <h3 class="mb-4 text-lg font-semibold">Utilisation des budgets par groupe</h3>
                                <div class="h-80">
                                    @php
                                        $budgetPayload = [
                                            'meta' => [
                                                'source' => 'expense.analytics.budgets.usage',
                                                'period' => $period,
                                                'filters' => [
                                                    'start_date' => $startDate,
                                                    'end_date' => $endDate,
                                                    'vehicle_id' => $vehicle_id ?: null,
                                                    'category' => $category ?: null,
                                                    'expense_group_id' => $expense_group_id ?: null,
                                                ],
                                            ],
                                            'chart' => [
                                                'id' => 'expense-budgets-usage',
                                                'type' => 'bar',
                                                'height' => 300,
                                                'ariaLabel' => 'Utilisation des budgets par groupe',
                                            ],
                                            'labels' => collect($budgetAnalysis['groups'] ?? [])->pluck('name')->values()->all(),
                                            'series' => [
                                                [
                                                    'name' => 'Alloue',
                                                    'data' => collect($budgetAnalysis['groups'] ?? [])->pluck('budget_allocated')->values()->all(),
                                                ],
                                                [
                                                    'name' => 'Utilise',
                                                    'data' => collect($budgetAnalysis['groups'] ?? [])->pluck('budget_used')->values()->all(),
                                                ],
                                            ],
                                            'options' => [
                                                'plotOptions' => ['bar' => ['borderRadius' => 6, 'columnWidth' => '55%']],
                                                'yaxis' => ['min' => 0],
                                            ],
                                        ];
                                    @endphp
                                    <x-charts.widget
                                        id="budgetUsageChart"
                                        :payload="$budgetPayload"
                                        wire-ignore
                                    />
                                </div>

                                <div class="mt-6 space-y-4">
                                    @foreach ($budgetAnalysis['groups'] as $group)
                                        <div>
                                            <div class="mb-1 flex items-center justify-between">
                                                <span class="text-sm font-medium">{{ $group['name'] ?? 'N/A' }}</span>
                                                <span class="text-sm text-gray-500">
                                                    {{ number_format($group['budget_used'] ?? 0, 0) }} / {{ number_format($group['budget_allocated'] ?? 0, 0) }} DZD
                                                </span>
                                            </div>
                                            <div class="h-2.5 w-full rounded-full bg-gray-200">
                                                <div
                                                    class="h-2.5 rounded-full {{ ($group['usage_percentage'] ?? 0) > 90 ? 'bg-red-600' : (($group['usage_percentage'] ?? 0) > 75 ? 'bg-yellow-600' : 'bg-green-600') }}"
                                                    style="width: {{ min(100, $group['usage_percentage'] ?? 0) }}%"
                                                ></div>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">{{ number_format($group['usage_percentage'] ?? 0, 1) }}% utilisé</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </x-card>
                    @endif
                </div>
            @endif
            @break
    @endswitch

    {{-- Métriques avancées --}}
    <div class="mt-6">
        <button wire:click="toggleAdvancedMetrics" class="text-sm text-primary-600 hover:text-primary-700">
            {{ $showAdvancedMetrics ? 'Masquer' : 'Afficher' }} les métriques avancées
        </button>

        @if ($showAdvancedMetrics && $efficiencyMetrics)
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <x-card class="p-4">
                    <p class="text-sm font-medium text-gray-600">Efficacité carburant</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($efficiencyMetrics['fuel_efficiency'] ?? 0, 2) }} L/100km</p>
                </x-card>
                <x-card class="p-4">
                    <p class="text-sm font-medium text-gray-600">Coût maintenance/km</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($efficiencyMetrics['maintenance_cost_per_km'] ?? 0, 2) }} DZD</p>
                </x-card>
                <x-card class="p-4">
                    <p class="text-sm font-medium text-gray-600">Temps arrêt moyen</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($efficiencyMetrics['avg_downtime'] ?? 0, 1) }} jours</p>
                </x-card>
                <x-card class="p-4">
                    <p class="text-sm font-medium text-gray-600">ROI maintenance</p>
                    <p class="text-xl font-bold {{ ($efficiencyMetrics['maintenance_roi'] ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($efficiencyMetrics['maintenance_roi'] ?? 0, 1) }}%</p>
                </x-card>
            </div>
        @endif
    </div>
</div>
