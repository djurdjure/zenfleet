<?php

namespace App\Livewire\Admin;

use App\Services\AlertCenterService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Component;

class AlertsIndex extends Component
{
    public string $filterType = '';
    public string $filterPriority = '';
    public string $sortBy = 'priority';
    public string $sortDirection = 'desc';
    public string $groupBy = 'priority';
    public string $lastUpdate = '';
    public array $dismissedAlertKeys = [];

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->can('alerts.view'), 403);
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function refreshData(): void
    {
        $organizationId = (int) auth()->user()->organization_id;
        app(AlertCenterService::class)->clearOrganizationCache($organizationId);
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function silentRefresh(): void
    {
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function focusAlertType(string $type, string $priority = ''): void
    {
        $this->filterType = $type;

        if ($priority !== '') {
            $this->filterPriority = $this->normalizePriority($priority);
        }
    }

    public function dismissAlert(string $alertKey): void
    {
        if (!in_array($alertKey, $this->dismissedAlertKeys, true)) {
            $this->dismissedAlertKeys[] = $alertKey;
        }
    }

    public function resetDismissedAlerts(): void
    {
        $this->dismissedAlertKeys = [];
    }

    public function getDashboardDataProperty(): array
    {
        $organizationId = (int) auth()->user()->organization_id;
        $dashboard = app(AlertCenterService::class)->getDashboardData($organizationId);

        return [
            'alerts' => $this->applyFilters($dashboard['alerts']),
            'criticalAlerts' => $this->applyFilters($dashboard['criticalAlerts'], 'critical'),
            'maintenanceAlerts' => $this->applyFilters($dashboard['maintenanceAlerts'], 'maintenance'),
            'budgetAlerts' => $this->applyFilters($dashboard['budgetAlerts'], 'budget'),
            'repairAlerts' => $this->applyFilters($dashboard['repairAlerts'], 'repair'),
            'stats' => $dashboard['stats'],
            'recentAlerts' => $dashboard['recentAlerts'],
        ];
    }

    public function render()
    {
        $dashboard = $this->dashboardData;
        $actionItems = $this->buildActionItems($dashboard);
        $groupedActionItems = $this->groupActionItems($actionItems);

        return view('livewire.admin.alerts-index', [
            'dashboard' => $dashboard,
            'actionItems' => $actionItems,
            'groupedActionItems' => $groupedActionItems,
        ]);
    }

    private function applyFilters(Collection $alerts, ?string $defaultType = null): Collection
    {
        if ($this->filterType === '' && $this->filterPriority === '') {
            return $alerts;
        }

        return $alerts->filter(function ($alert) use ($defaultType) {
            $type = (string) ($defaultType ?? $alert->type ?? '');
            $priority = (string) ($alert->priority ?? $alert->alert_priority ?? '');

            if ($this->filterType !== '' && $type !== $this->filterType) {
                return false;
            }

            if ($this->filterPriority !== '' && $priority !== $this->filterPriority) {
                return false;
            }

            return true;
        })->values();
    }

    private function buildActionItems(array $dashboard): Collection
    {
        $items = collect();

        foreach ($dashboard['criticalAlerts'] as $alert) {
            $items->push([
                'key' => 'critical:'.$alert->id,
                'type' => 'critical',
                'priority' => $this->normalizePriority((string) ($alert->priority ?? 'critical')),
                'title' => (string) ($alert->title ?? 'Alerte critique'),
                'message' => (string) ($alert->message ?? ''),
                'meta' => 'Conformite flotte',
                'created_at' => $this->normalizeDate($alert->created_at ?? null),
                'action_label' => 'Ouvrir vehicules',
                'action_url' => Route::has('admin.vehicles.index') ? route('admin.vehicles.index') : null,
            ]);
        }

        foreach ($dashboard['maintenanceAlerts'] as $alert) {
            $items->push([
                'key' => 'maintenance:'.$alert->id,
                'type' => 'maintenance',
                'priority' => $this->normalizePriority((string) ($alert->alert_priority ?? $alert->priority ?? 'medium')),
                'title' => trim(((string) ($alert->registration_plate ?? 'Vehicule')).' • '.((string) ($alert->maintenance_type ?? 'Maintenance'))),
                'message' => 'Echeance: '.$this->formatDate($alert->next_due_date ?? null),
                'meta' => trim(((string) ($alert->brand ?? '')).' '.((string) ($alert->model ?? ''))),
                'created_at' => $this->normalizeDate($alert->created_at ?? null),
                'action_label' => 'Ouvrir operations',
                'action_url' => Route::has('admin.maintenance.operations.index') ? route('admin.maintenance.operations.index') : null,
            ]);
        }

        foreach ($dashboard['budgetAlerts'] as $alert) {
            $utilization = (float) ($alert->utilization_percentage ?? 0);
            $items->push([
                'key' => 'budget:'.$alert->id,
                'type' => 'budget',
                'priority' => $this->normalizePriority((string) ($alert->priority ?? 'medium')),
                'title' => (string) ($alert->scope_description ?? 'Budget'),
                'message' => 'Consommation: '.number_format($utilization, 1).'%',
                'meta' => 'Budget: '.number_format((float) ($alert->budgeted_amount ?? 0), 0).' DA',
                'created_at' => $this->normalizeDate($alert->created_at ?? null),
                'action_label' => 'Ouvrir depenses',
                'action_url' => Route::has('admin.vehicle-expenses.index') ? route('admin.vehicle-expenses.index') : null,
            ]);
        }

        foreach ($dashboard['repairAlerts'] as $alert) {
            $repairId = (int) ($alert->id ?? 0);
            $repairUrl = Route::has('admin.repair-requests.show') && $repairId > 0
                ? route('admin.repair-requests.show', $repairId)
                : (Route::has('admin.repair-requests.index') ? route('admin.repair-requests.index') : null);

            $items->push([
                'key' => 'repair:'.$repairId,
                'type' => 'repair',
                'priority' => $this->normalizePriority((string) ($alert->priority ?? 'medium')),
                'title' => 'Demande #'.$repairId.' • '.((string) ($alert->vehicle ?? 'N/A')),
                'message' => Str::limit((string) ($alert->message ?? ''), 90),
                'meta' => 'Statut: '.((string) ($alert->status ?? 'en_attente')),
                'created_at' => $this->normalizeDate($alert->created_at ?? null),
                'action_label' => 'Ouvrir demande',
                'action_url' => $repairUrl,
            ]);
        }

        $items = $items
            ->reject(fn (array $item) => in_array($item['key'], $this->dismissedAlertKeys, true))
            ->values();

        return $this->sortActionItems($items);
    }

    private function sortActionItems(Collection $items): Collection
    {
        $sorted = $items->values()->all();
        $direction = strtolower($this->sortDirection) === 'asc' ? 1 : -1;

        usort($sorted, function (array $a, array $b) use ($direction): int {
            $left = 0;
            $right = 0;

            if ($this->sortBy === 'type') {
                $order = ['critical' => 1, 'maintenance' => 2, 'budget' => 3, 'repair' => 4];
                $left = $order[$a['type']] ?? 99;
                $right = $order[$b['type']] ?? 99;
            } elseif ($this->sortBy === 'date') {
                $left = $a['created_at']->getTimestamp();
                $right = $b['created_at']->getTimestamp();
            } else {
                $left = $this->priorityWeight($a['priority']);
                $right = $this->priorityWeight($b['priority']);
            }

            if ($left === $right) {
                $left = $a['created_at']->getTimestamp();
                $right = $b['created_at']->getTimestamp();
            }

            return ($left <=> $right) * $direction;
        });

        return collect($sorted)->values();
    }

    private function groupActionItems(Collection $items): array
    {
        if ($this->groupBy === 'none') {
            return [[
                'key' => 'all',
                'label' => 'Toutes les alertes',
                'items' => $items,
            ]];
        }

        $groups = [];

        if ($this->groupBy === 'type') {
            $labels = [
                'critical' => 'Critiques',
                'maintenance' => 'Maintenance',
                'budget' => 'Budget',
                'repair' => 'Reparations',
            ];

            foreach ($labels as $key => $label) {
                $groupItems = $items->where('type', $key)->values();
                if ($groupItems->isNotEmpty()) {
                    $groups[] = ['key' => $key, 'label' => $label, 'items' => $groupItems];
                }
            }

            return $groups;
        }

        $labels = [
            'critical' => 'Criticite critique',
            'urgent' => 'Criticite urgente',
            'high' => 'Criticite haute',
            'medium' => 'Criticite moyenne',
            'low' => 'Criticite faible',
        ];

        foreach ($labels as $key => $label) {
            $groupItems = $items->where('priority', $key)->values();
            if ($groupItems->isNotEmpty()) {
                $groups[] = ['key' => $key, 'label' => $label, 'items' => $groupItems];
            }
        }

        return $groups;
    }

    private function priorityWeight(string $priority): int
    {
        return match ($priority) {
            'critical' => 5,
            'urgent' => 4,
            'high' => 3,
            'medium' => 2,
            default => 1,
        };
    }

    private function normalizePriority(string $priority): string
    {
        $value = strtolower(trim($priority));

        return match ($value) {
            'overdue' => 'critical',
            'critical', 'urgent', 'high', 'medium', 'low' => $value,
            default => 'medium',
        };
    }

    private function normalizeDate(mixed $date): Carbon
    {
        if ($date instanceof Carbon) {
            return $date;
        }

        if ($date === null || $date === '') {
            return now();
        }

        return Carbon::parse($date);
    }

    private function formatDate(mixed $date): string
    {
        if ($date === null || $date === '') {
            return 'N/A';
        }

        return Carbon::parse($date)->format('d/m/Y');
    }
}
