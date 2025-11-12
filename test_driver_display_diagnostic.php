<?php

/**
 * 🔍 DIAGNOSTIC ULTRA-PRO - AFFICHAGE CHAUFFEURS
 * 
 * Script de diagnostic pour identifier pourquoi les chauffeurs
 * ne s'affichent pas dans la liste des véhicules
 * 
 * @version 1.0-Ultra-Pro
 * @date 2025-11-11
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Vehicle;
use App\Models\Assignment;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║     🔍 DIAGNOSTIC AFFICHAGE CHAUFFEURS - ULTRA-PRO                   ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

try {
    // 1. Vérifier la structure des tables
    echo "📊 ANALYSE STRUCTURE BASE DE DONNÉES\n";
    echo "────────────────────────────────────────\n";
    
    // Vérifier les colonnes de la table assignments
    $assignmentColumns = DB::select("
        SELECT column_name, data_type, is_nullable 
        FROM information_schema.columns 
        WHERE table_name = 'assignments'
        ORDER BY ordinal_position
    ");
    
    echo "✅ Colonnes table 'assignments':\n";
    foreach ($assignmentColumns as $col) {
        echo "   - {$col->column_name} ({$col->data_type}) " . ($col->is_nullable === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
    echo "\n";
    
    // 2. Analyser les assignments actives
    echo "📈 ANALYSE ASSIGNMENTS ACTIVES\n";
    echo "────────────────────────────────────────\n";
    
    $totalAssignments = Assignment::count();
    $activeAssignments = Assignment::where('status', 'active')->count();
    $assignmentsWithDriver = Assignment::whereNotNull('driver_id')->count();
    $activeWithDriver = Assignment::where('status', 'active')->whereNotNull('driver_id')->count();
    
    echo "Total assignments: {$totalAssignments}\n";
    echo "Assignments actives: {$activeAssignments}\n";
    echo "Assignments avec driver_id: {$assignmentsWithDriver}\n";
    echo "Actives avec driver_id: {$activeWithDriver}\n\n";
    
    // 3. Vérifier les relations Driver -> User
    echo "🔗 ANALYSE RELATIONS DRIVER -> USER\n";
    echo "────────────────────────────────────────\n";
    
    $totalDrivers = Driver::count();
    $driversWithUser = Driver::whereNotNull('user_id')->count();
    $driversWithoutUser = Driver::whereNull('user_id')->count();
    
    echo "Total drivers: {$totalDrivers}\n";
    echo "Drivers avec user_id: {$driversWithUser}\n";
    echo "Drivers SANS user_id: {$driversWithoutUser} ⚠️\n\n";
    
    // 4. Test de la requête utilisée dans le contrôleur
    echo "🚗 TEST REQUÊTE CONTRÔLEUR\n";
    echo "────────────────────────────────────────\n";
    
    $vehicles = Vehicle::with([
        'vehicleType',
        'depot',
        'vehicleStatus',
        'assignments' => function ($query) {
            $query->where('status', 'active')
                  ->where('start_datetime', '<=', now())
                  ->where(function($q) {
                      $q->whereNull('end_datetime')
                        ->orWhere('end_datetime', '>=', now());
                  })
                  ->with('driver.user')
                  ->limit(1);
        }
    ])
    ->where('is_archived', false)
    ->limit(5)
    ->get();
    
    foreach ($vehicles as $vehicle) {
        echo "\n🚙 Véhicule: {$vehicle->registration_plate}\n";
        
        // Vérifier si les assignments sont chargées
        if (!$vehicle->relationLoaded('assignments')) {
            echo "   ❌ Relation 'assignments' NON chargée!\n";
            continue;
        }
        
        $assignment = $vehicle->assignments->first();
        
        if (!$assignment) {
            echo "   ⚠️ Aucune assignment active trouvée\n";
            continue;
        }
        
        echo "   ✅ Assignment ID: {$assignment->id}\n";
        echo "   - Status: {$assignment->status}\n";
        echo "   - Driver ID: " . ($assignment->driver_id ?? 'NULL') . "\n";
        
        if (!$assignment->driver_id) {
            echo "   ❌ Assignment sans driver_id!\n";
            continue;
        }
        
        if (!$assignment->relationLoaded('driver')) {
            echo "   ❌ Relation 'driver' NON chargée!\n";
            continue;
        }
        
        $driver = $assignment->driver;
        
        if (!$driver) {
            echo "   ❌ Driver non trouvé malgré driver_id={$assignment->driver_id}\n";
            continue;
        }
        
        echo "   ✅ Driver trouvé: ID={$driver->id}\n";
        echo "   - User ID: " . ($driver->user_id ?? 'NULL') . "\n";
        
        if (!$driver->user_id) {
            echo "   ❌ Driver sans user_id!\n";
            continue;
        }
        
        if (!$driver->relationLoaded('user')) {
            echo "   ❌ Relation 'user' NON chargée!\n";
            continue;
        }
        
        $user = $driver->user;
        
        if (!$user) {
            echo "   ❌ User non trouvé malgré user_id={$driver->user_id}\n";
            continue;
        }
        
        echo "   ✅ User trouvé: {$user->name} ({$user->email})\n";
    }
    
    // 5. Requête SQL directe pour validation
    echo "\n📝 VALIDATION SQL DIRECTE\n";
    echo "────────────────────────────────────────\n";
    
    $sqlResults = DB::select("
        SELECT 
            v.registration_plate,
            a.id as assignment_id,
            a.status as assignment_status,
            a.driver_id,
            d.id as driver_exists,
            d.user_id,
            u.name as user_name,
            u.email as user_email
        FROM vehicles v
        LEFT JOIN assignments a ON v.id = a.vehicle_id 
            AND a.status = 'active'
            AND a.start_datetime <= NOW()
            AND (a.end_datetime IS NULL OR a.end_datetime >= NOW())
        LEFT JOIN drivers d ON a.driver_id = d.id
        LEFT JOIN users u ON d.user_id = u.id
        WHERE v.is_archived = false
        LIMIT 10
    ");
    
    echo "Résultats SQL directs:\n";
    foreach ($sqlResults as $row) {
        echo "\nVéhicule: {$row->registration_plate}\n";
        if ($row->assignment_id) {
            echo "  Assignment: {$row->assignment_id} (status: {$row->assignment_status})\n";
            echo "  Driver ID: " . ($row->driver_id ?? 'NULL') . "\n";
            echo "  User: " . ($row->user_name ?? 'NON TROUVÉ') . "\n";
        } else {
            echo "  Aucune assignment active\n";
        }
    }
    
    // 6. Recommandations
    echo "\n🔧 RECOMMANDATIONS DE CORRECTION\n";
    echo "════════════════════════════════════\n";
    
    if ($driversWithoutUser > 0) {
        echo "⚠️ PROBLÈME DÉTECTÉ: {$driversWithoutUser} drivers sans user_id!\n";
        echo "   Solution: Associer chaque driver à un user ou créer les users manquants.\n\n";
    }
    
    if ($activeAssignments > $activeWithDriver) {
        $diff = $activeAssignments - $activeWithDriver;
        echo "⚠️ PROBLÈME DÉTECTÉ: {$diff} assignments actives sans driver_id!\n";
        echo "   Solution: Mettre à jour les assignments pour inclure driver_id.\n\n";
    }
    
    // Proposition de correction SQL
    if ($driversWithoutUser > 0 || ($activeAssignments > $activeWithDriver)) {
        echo "📝 REQUÊTES SQL DE CORRECTION SUGGÉRÉES:\n";
        echo "─────────────────────────────────────────\n";
        
        if ($driversWithoutUser > 0) {
            echo "-- Lister les drivers sans user:\n";
            echo "SELECT id, first_name, last_name, email FROM drivers WHERE user_id IS NULL;\n\n";
            
            echo "-- Créer un user pour chaque driver orphelin:\n";
            echo "INSERT INTO users (name, email, password, organization_id, created_at, updated_at)\n";
            echo "SELECT \n";
            echo "  CONCAT(first_name, ' ', last_name),\n";
            echo "  email,\n";
            echo "  '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',\n";
            echo "  organization_id,\n";
            echo "  NOW(),\n";
            echo "  NOW()\n";
            echo "FROM drivers \n";
            echo "WHERE user_id IS NULL AND email IS NOT NULL;\n\n";
        }
    }
    
    echo "\n✅ Diagnostic terminé avec succès!\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
