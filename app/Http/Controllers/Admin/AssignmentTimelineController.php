<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignmentTimelineController extends Controller
{
    /**
     * Affiche la vue timeline des affectations
     */
    public function index(Request $request)
    {
        // Récupération des paramètres de vue
        $view = $request->get('view', 'month'); // month, week, day
        $date = $request->get('date', now()->format('Y-m-d'));

        try {
            $currentDate = Carbon::parse($date);
        } catch (\Exception $e) {
            $currentDate = now();
        }

        // Calcul de la période selon la vue
        [$startDate, $endDate] = $this->calculatePeriod($view, $currentDate);

        // Récupération des véhicules avec leurs affectations
        $vehicles = Vehicle::with([
            'assignments' => function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_datetime', [$startDate, $endDate])
                      ->orWhereBetween('end_datetime', [$startDate, $endDate])
                      ->orWhere(function ($subQ) use ($startDate, $endDate) {
                          $subQ->where('start_datetime', '<=', $startDate)
                               ->where(function ($endQ) use ($endDate) {
                                   $endQ->where('end_datetime', '>=', $endDate)
                                        ->orWhereNull('end_datetime');
                               });
                      });
                })
                ->with(['driver:id,first_name,last_name,personal_phone,photo_path'])
                ->orderBy('start_datetime');
            }
        ])
        ->whereHas('vehicleStatus', function ($query) {
            $query->where('name', '!=', 'inactive');
        })
        ->orderBy('brand')
        ->orderBy('model')
        ->get();

        // Récupération de toutes les affectations pour la période
        $assignments = Assignment::with([
            'vehicle:id,brand,model,registration_plate,current_mileage,status',
            'driver:id,first_name,last_name,personal_phone,photo_path'
        ])
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_datetime', [$startDate, $endDate])
                  ->orWhereBetween('end_datetime', [$startDate, $endDate])
                  ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                      $subQuery->where('start_datetime', '<=', $startDate)
                               ->where(function ($endQuery) use ($endDate) {
                                   $endQuery->where('end_datetime', '>=', $endDate)
                                            ->orWhereNull('end_datetime');
                               });
                  });
        })
        ->orderBy('start_datetime')
        ->get();

        // Calcul des statistiques
        $stats = $this->calculateStats($assignments);

        return view('admin.assignments.timeline', [
            'vehicles' => $vehicles,
            'assignments' => $assignments,
            'currentView' => $view,
            'currentDate' => $currentDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalAssignments' => $assignments->count(),
            'activeAssignments' => $stats['active'],
            'completedAssignments' => $stats['completed'],
            'upcomingAssignments' => $stats['upcoming'],
        ]);
    }

    /**
     * API pour récupérer les données de la timeline
     */
    public function getData(Request $request)
    {
        $view = $request->get('view', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));

        try {
            $currentDate = Carbon::parse($date);
        } catch (\Exception $e) {
            $currentDate = now();
        }

        [$startDate, $endDate] = $this->calculatePeriod($view, $currentDate);

        // Récupération des véhicules avec leurs affectations
        $vehicles = Vehicle::with([
            'assignments' => function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_datetime', [$startDate, $endDate])
                      ->orWhereBetween('end_datetime', [$startDate, $endDate])
                      ->orWhere(function ($subQ) use ($startDate, $endDate) {
                          $subQ->where('start_datetime', '<=', $startDate)
                               ->where(function ($endQ) use ($endDate) {
                                   $endQ->where('end_datetime', '>=', $endDate)
                                        ->orWhereNull('end_datetime');
                               });
                      });
                })
                ->with(['driver:id,first_name,last_name,personal_phone,photo_path'])
                ->orderBy('start_datetime');
            }
        ])
        ->whereHas('vehicleStatus', function ($query) {
            $query->where('name', '!=', 'inactive');
        })
        ->orderBy('brand')
        ->orderBy('model')
        ->get();

        // Récupération des affectations
        $assignments = Assignment::with([
            'vehicle:id,brand,model,registration_plate,current_mileage,status',
            'driver:id,first_name,last_name,personal_phone,photo_path'
        ])
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_datetime', [$startDate, $endDate])
                  ->orWhereBetween('end_datetime', [$startDate, $endDate])
                  ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                      $subQuery->where('start_datetime', '<=', $startDate)
                               ->where(function ($endQuery) use ($endDate) {
                                   $endQuery->where('end_datetime', '>=', $endDate)
                                            ->orWhereNull('end_datetime');
                               });
                  });
        })
        ->orderBy('start_datetime')
        ->get();

        // Calcul des statistiques
        $stats = $this->calculateStats($assignments);

        // Génération des colonnes temporelles
        $timeColumns = $this->generateTimeColumns($view, $currentDate);

        return response()->json([
            'vehicles' => $vehicles,
            'assignments' => $assignments,
            'timeColumns' => $timeColumns,
            'stats' => $stats,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'view' => $view,
                'current' => $currentDate->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Recherche dans les affectations
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $view = $request->get('view', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));

        try {
            $currentDate = Carbon::parse($date);
        } catch (\Exception $e) {
            $currentDate = now();
        }

        [$startDate, $endDate] = $this->calculatePeriod($view, $currentDate);

        if (empty($query)) {
            return $this->getData($request);
        }

        // Recherche dans les véhicules et conducteurs
        $vehicles = Vehicle::with([
            'assignments' => function ($assignmentQuery) use ($startDate, $endDate, $query) {
                $assignmentQuery->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_datetime', [$startDate, $endDate])
                      ->orWhereBetween('end_datetime', [$startDate, $endDate])
                      ->orWhere(function ($subQ) use ($startDate, $endDate) {
                          $subQ->where('start_datetime', '<=', $startDate)
                               ->where(function ($endQ) use ($endDate) {
                                   $endQ->where('end_datetime', '>=', $endDate)
                                        ->orWhereNull('end_datetime');
                               });
                      });
                })
                ->whereHas('driver', function ($driverQuery) use ($query) {
                    $driverQuery->where('first_name', 'like', "%{$query}%")
                               ->orWhere('last_name', 'like', "%{$query}%");
                })
                ->with(['driver:id,first_name,last_name,personal_phone,photo_path'])
                ->orderBy('start_datetime');
            }
        ])
        ->where(function ($vehicleQuery) use ($query) {
            $vehicleQuery->where('brand', 'like', "%{$query}%")
                        ->orWhere('model', 'like', "%{$query}%")
                        ->orWhere('registration_plate', 'like', "%{$query}%");
        })
        ->orWhereHas('assignments.driver', function ($driverQuery) use ($query) {
            $driverQuery->where('first_name', 'like', "%{$query}%")
                       ->orWhere('last_name', 'like', "%{$query}%");
        })
        ->whereHas('vehicleStatus', function ($query) {
            $query->where('name', '!=', 'inactive');
        })
        ->orderBy('brand')
        ->orderBy('model')
        ->get();

        // Filtrer les véhicules qui ont des affectations correspondantes
        $vehicles = $vehicles->filter(function ($vehicle) {
            return $vehicle->assignments->count() > 0;
        });

        // Récupération des affectations filtrées
        $assignments = Assignment::with([
            'vehicle:id,brand,model,registration_plate,current_mileage,status',
            'driver:id,first_name,last_name,personal_phone,photo_path'
        ])
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_datetime', [$startDate, $endDate])
                  ->orWhereBetween('end_datetime', [$startDate, $endDate])
                  ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                      $subQuery->where('start_datetime', '<=', $startDate)
                               ->where(function ($endQuery) use ($endDate) {
                                   $endQuery->where('end_datetime', '>=', $endDate)
                                            ->orWhereNull('end_datetime');
                               });
                  });
        })
        ->where(function ($assignmentQuery) use ($query) {
            $assignmentQuery->whereHas('vehicle', function ($vehicleQuery) use ($query) {
                $vehicleQuery->where('brand', 'like', "%{$query}%")
                           ->orWhere('model', 'like', "%{$query}%")
                           ->orWhere('registration_plate', 'like', "%{$query}%");
            })
            ->orWhereHas('driver', function ($driverQuery) use ($query) {
                $driverQuery->where('first_name', 'like', "%{$query}%")
                           ->orWhere('last_name', 'like', "%{$query}%");
            });
        })
        ->orderBy('start_datetime')
        ->get();

        $stats = $this->calculateStats($assignments);
        $timeColumns = $this->generateTimeColumns($view, $currentDate);

        return response()->json([
            'vehicles' => $vehicles,
            'assignments' => $assignments,
            'timeColumns' => $timeColumns,
            'stats' => $stats,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'view' => $view,
                'current' => $currentDate->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Calcule la période selon la vue
     */
    private function calculatePeriod($view, Carbon $currentDate)
    {
        switch ($view) {
            case 'week':
                $startDate = $currentDate->copy()->startOfWeek();
                $endDate = $currentDate->copy()->endOfWeek();
                break;

            case 'day':
                $startDate = $currentDate->copy()->startOfDay();
                $endDate = $currentDate->copy()->endOfDay();
                break;

            case 'month':
            default:
                $startDate = $currentDate->copy()->startOfMonth();
                $endDate = $currentDate->copy()->endOfMonth();
                break;
        }

        return [$startDate, $endDate];
    }

    /**
     * Calcule les statistiques des affectations
     */
    private function calculateStats($assignments)
    {
        $now = now();
        $active = 0;
        $completed = 0;
        $upcoming = 0;

        foreach ($assignments as $assignment) {
            if ($assignment->end_datetime) {
                $completed++;
            } elseif (Carbon::parse($assignment->start_datetime) > $now) {
                $upcoming++;
            } else {
                $active++;
            }
        }

        return [
            'active' => $active,
            'completed' => $completed,
            'upcoming' => $upcoming,
            'total' => $assignments->count()
        ];
    }

    /**
     * Génère les colonnes temporelles selon la vue
     */
    private function generateTimeColumns($view, Carbon $currentDate)
    {
        $columns = [];

        switch ($view) {
            case 'month':
                $startOfMonth = $currentDate->copy()->startOfMonth();
                $endOfMonth = $currentDate->copy()->endOfMonth();
                $daysInMonth = $endOfMonth->day;

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = $startOfMonth->copy()->day($day);
                    $columns[] = [
                        'key' => $date->format('Y-m-d'),
                        'label' => $date->format('D'),
                        'value' => $day,
                        'isToday' => $date->isToday(),
                        'isWeekend' => $date->isWeekend()
                    ];
                }
                break;

            case 'week':
                $startOfWeek = $currentDate->copy()->startOfWeek();

                for ($i = 0; $i < 7; $i++) {
                    $date = $startOfWeek->copy()->addDays($i);
                    $columns[] = [
                        'key' => $date->format('Y-m-d'),
                        'label' => $date->format('D'),
                        'value' => $date->day,
                        'isToday' => $date->isToday(),
                        'isWeekend' => $date->isWeekend()
                    ];
                }
                break;

            case 'day':
                for ($hour = 6; $hour <= 22; $hour++) {
                    $columns[] = [
                        'key' => sprintf('%02d:00', $hour),
                        'label' => $hour < 12 ? 'AM' : 'PM',
                        'value' => sprintf('%02dh', $hour),
                        'isCurrentHour' => $currentDate->isToday() && $currentDate->hour == $hour
                    ];
                }
                break;
        }

        return $columns;
    }

    /**
     * Exporte les données de la timeline
     */
    public function export(Request $request)
    {
        $view = $request->get('view', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));
        $format = $request->get('format', 'csv'); // csv, excel, pdf

        try {
            $currentDate = Carbon::parse($date);
        } catch (\Exception $e) {
            $currentDate = now();
        }

        [$startDate, $endDate] = $this->calculatePeriod($view, $currentDate);

        $assignments = Assignment::with([
            'vehicle:id,brand,model,registration_plate',
            'driver:id,first_name,last_name,personal_phone'
        ])
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_datetime', [$startDate, $endDate])
                  ->orWhereBetween('end_datetime', [$startDate, $endDate])
                  ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                      $subQuery->where('start_datetime', '<=', $startDate)
                               ->where(function ($endQuery) use ($endDate) {
                                   $endQuery->where('end_datetime', '>=', $endDate)
                                            ->orWhereNull('end_datetime');
                               });
                  });
        })
        ->orderBy('start_datetime')
        ->get();

        // TODO: Implémenter l'export selon le format demandé
        // Pour l'instant, retourner les données JSON
        return response()->json([
            'assignments' => $assignments,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'view' => $view
            ],
            'exported_at' => now()->toISOString()
        ]);
    }
}
