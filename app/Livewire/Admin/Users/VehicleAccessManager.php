<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleAccessManager extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public User $user;
    public $search = '';
    public $filter = 'all'; // all, assigned, unassigned
    public int $perPage = 12;

    protected $listeners = ['refresh' => '$refresh'];

    public function mount(User $user)
    {
        $this->user = $user;

        if (!auth()->user()?->can('users.update')) {
            abort(403, 'Accès refusé.');
        }

        if (!auth()->user()?->hasRole('Super Admin') && $this->user->organization_id !== auth()->user()?->organization_id) {
            abort(403, 'Accès refusé.');
        }
        
        // Si l'utilisateur est Super Admin, il a déjà accès à tout
        if ($this->user->hasRole('Super Admin')) {
            // On pourrait rediriger ou afficher un message, mais pour l'instant on laisse l'interface
            // en mode lecture seule ou informative
        }
    }

    private function ensurePermission(string $permission, string $message): bool
    {
        $currentUser = auth()->user();
        if (!$currentUser || !$currentUser->can($permission)) {
            $this->dispatch('toast', ['type' => 'error', 'message' => $message]);
            return false;
        }

        return true;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function toggleAccess($vehicleId)
    {
        if (!$this->ensurePermission('users.update', 'Permission refusée pour modifier les accès.')) {
            return;
        }

        // 🔒 IMPORTANT: Utiliser DB direct pour bypasser le Global Scope
        // Sinon, on ne peut accorder accès qu'aux véhicules déjà accessibles (catch-22)
        $hasAccess = DB::table('user_vehicle')
            ->where('user_id', $this->user->id)
            ->where('vehicle_id', $vehicleId)
            ->where('access_type', 'manual')
            ->exists();

        if ($hasAccess) {
            // Retirer l'accès manuel
            DB::table('user_vehicle')
                ->where('user_id', $this->user->id)
                ->where('vehicle_id', $vehicleId)
                ->where('access_type', 'manual')
                ->delete();
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Accès retiré avec succès']);
        } else {
            // Accorder l'accès manuel
            DB::table('user_vehicle')->insert([
                'user_id' => $this->user->id,
                'vehicle_id' => $vehicleId,
                'granted_at' => now(),
                'granted_by' => auth()->id(),
                'access_type' => 'manual',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Accès accordé avec succès']);
        }
    }

    public function grantAll()
    {
        if (!$this->ensurePermission('users.update', 'Permission refusée pour modifier les accès.')) {
            return;
        }

        // 🔒 Récupérer tous les véhicules de l'organisation (bypass scope)
        $vehicles = Vehicle::withoutGlobalScope(\App\Models\Scopes\UserVehicleAccessScope::class)
            ->where('organization_id', $this->user->organization_id)
            ->pluck('id');
        
        // 🔒 Vérifier les accès existants via DB direct
        $existing = DB::table('user_vehicle')
            ->where('user_id', $this->user->id)
            ->where('access_type', 'manual')
            ->pluck('vehicle_id')
            ->toArray();
            
        $toAttach = $vehicles->diff($existing);
        
        if ($toAttach->isNotEmpty()) {
            $records = [];
            foreach ($toAttach as $vehicleId) {
                $records[] = [
                    'user_id' => $this->user->id,
                    'vehicle_id' => $vehicleId,
                    'granted_at' => now(),
                    'granted_by' => auth()->id(),
                    'access_type' => 'manual',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('user_vehicle')->insert($records);
            
            $this->dispatch('toast', ['type' => 'success', 'message' => count($toAttach) . ' véhicules ajoutés']);
        } else {
            $this->dispatch('toast', ['type' => 'info', 'message' => 'L\'utilisateur a déjà accès à tous les véhicules']);
        }
    }

    public function revokeAll()
    {
        if (!$this->ensurePermission('users.update', 'Permission refusée pour modifier les accès.')) {
            return;
        }

        // 🔒 Utiliser DB direct pour bypasser le Global Scope
        $count = DB::table('user_vehicle')
            ->where('user_id', $this->user->id)
            ->where('access_type', 'manual')
            ->count();
        
        if ($count > 0) {
            DB::table('user_vehicle')
                ->where('user_id', $this->user->id)
                ->where('access_type', 'manual')
                ->delete();
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Tous les accès manuels ont été retirés']);
        } else {
            $this->dispatch('toast', ['type' => 'info', 'message' => 'Aucun accès manuel à retirer']);
        }
    }

    public function render()
    {
        // On récupère tous les véhicules de l'organisation de l'utilisateur cible
        // Note: On utilise withoutGlobalScope pour que l'admin qui gère puisse voir tous les véhicules
        // même s'il n'y a pas accès lui-même (selon les règles métier, un admin voit tout dans son org)
        // Mais attention, si l'admin connecté est restreint, il ne devrait voir que ce qu'il peut voir.
        // Ici, on suppose que celui qui gère les accès a le droit de voir tous les véhicules de l'org.
        
        $query = Vehicle::query()
            ->withoutGlobalScope(\App\Models\Scopes\UserVehicleAccessScope::class) // Bypass scope pour voir tous les véhicules assignables
            ->where('organization_id', $this->user->organization_id)
            ->with(['vehicleType', 'users' => function($q) {
                $q->where('user_id', $this->user->id);
            }]);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('registration_plate', 'like', '%' . $this->search . '%')
                  ->orWhere('brand', 'like', '%' . $this->search . '%')
                  ->orWhere('model', 'like', '%' . $this->search . '%')
                  ->orWhere('vehicle_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter === 'assigned') {
            $query->whereHas('users', function($q) {
                $q->where('user_id', $this->user->id);
            });
        } elseif ($this->filter === 'unassigned') {
            $query->whereDoesntHave('users', function($q) {
                $q->where('user_id', $this->user->id);
            });
        }

        $vehicles = $query->paginate($this->perPage);

        return view('livewire.admin.users.vehicle-access-manager', [
            'vehicles' => $vehicles
        ])->extends('layouts.admin.catalyst')->section('content');
    }
}
