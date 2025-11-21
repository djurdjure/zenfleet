<?php

/**
 * 🧪 TEST REPRODUCTION - Erreur validation dates 20/11/2025
 *
 * Reproduit exactement le problème rencontré par l'utilisateur:
 * - Date début: 20/11/2025 18:30
 * - Date fin: 20/11/2025 22:00
 * - Erreur: "La date de début doit être antérieure à la date de fin"
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🧪 TEST REPRODUCTION - Validation Dates 20/11/2025         ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Simuler exactement ce que fait AssignmentForm.php

echo "📋 ÉTAPE 1 : Simulation du formulaire utilisateur\n";
echo "─────────────────────────────────────────────────────────────\n";

// Format français comme dans le formulaire
$start_date = '20/11/2025';
$start_time = '18:30';
$end_date = '20/11/2025';
$end_time = '22:00';

echo "Format français (UI):\n";
echo "  start_date: $start_date\n";
echo "  start_time: $start_time\n";
echo "  end_date:   $end_date\n";
echo "  end_time:   $end_time\n\n";

// Conversion ISO (méthode convertToISO)
function convertToISO(string $date): string
{
    if (empty($date)) {
        return '';
    }

    // Si déjà au format ISO, retourner tel quel
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return $date;
    }

    // Convertir du format français vers ISO
    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $date, $matches)) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        $year = $matches[3];

        // Validation de la date
        if (checkdate((int)$month, (int)$day, (int)$year)) {
            return "$year-$month-$day";
        }
    }

    // Si échec, retourner la valeur originale
    return $date;
}

echo "📋 ÉTAPE 2 : Conversion ISO (combineDateTime)\n";
echo "─────────────────────────────────────────────────────────────\n";

$startDateISO = convertToISO($start_date);
$endDateISO = convertToISO($end_date);

$start_datetime = $startDateISO . ' ' . $start_time;
$end_datetime = $endDateISO . ' ' . $end_time;

echo "Strings datetime:\n";
echo "  start_datetime: $start_datetime\n";
echo "  end_datetime:   $end_datetime\n\n";

echo "📋 ÉTAPE 3 : Conversion Carbon (saveAssignment)\n";
echo "─────────────────────────────────────────────────────────────\n";

$start_carbon = Carbon::parse($start_datetime);
$end_carbon = Carbon::parse($end_datetime);

echo "Objets Carbon:\n";
echo "  start_carbon: " . $start_carbon->toIso8601String() . "\n";
echo "  end_carbon:   " . $end_carbon->toIso8601String() . "\n";
echo "  start timestamp: " . $start_carbon->timestamp . "\n";
echo "  end timestamp:   " . $end_carbon->timestamp . "\n";
echo "  timezone: " . $start_carbon->timezone->getName() . "\n\n";

echo "📋 ÉTAPE 4 : Comparaison (validateBusinessRules)\n";
echo "─────────────────────────────────────────────────────────────\n";

$comparison = $start_carbon < $end_carbon;
$diff_seconds = $end_carbon->diffInSeconds($start_carbon, false);

echo "Résultat comparaison:\n";
echo "  start < end: " . ($comparison ? '✅ TRUE' : '❌ FALSE') . "\n";
echo "  start >= end: " . (!$comparison ? '❌ TRUE (ERREUR!)' : '✅ FALSE') . "\n";
echo "  Différence: $diff_seconds secondes (" . ($diff_seconds / 3600) . " heures)\n\n";

if ($start_carbon >= $end_carbon) {
    echo "❌ ERREUR REPRODUITE !\n";
    echo "   La validation rejetterait cette affectation\n";
    echo "   Message: La date de début doit être antérieure à la date de fin\n\n";
} else {
    echo "✅ VALIDATION DEVRAIT PASSER\n";
    echo "   Les dates sont correctes\n\n";
}

echo "📋 ÉTAPE 5 : Vérification debug timezone\n";
echo "─────────────────────────────────────────────────────────────\n";

echo "Configuration Laravel:\n";
echo "  App timezone: " . config('app.timezone') . "\n";
echo "  DB timezone: " . config('database.connections.pgsql.timezone', 'default') . "\n";
echo "  PHP timezone: " . date_default_timezone_get() . "\n\n";

echo "📋 ÉTAPE 6 : Test avec différentes méthodes de parsing\n";
echo "─────────────────────────────────────────────────────────────\n";

// Méthode 1: Parse simple
$s1 = Carbon::parse($start_datetime);
$e1 = Carbon::parse($end_datetime);
echo "Méthode 1 (parse simple):\n";
echo "  Comparaison: " . ($s1 < $e1 ? '✅ start < end' : '❌ start >= end') . "\n";

// Méthode 2: CreateFromFormat
$s2 = Carbon::createFromFormat('Y-m-d H:i', $start_datetime);
$e2 = Carbon::createFromFormat('Y-m-d H:i', $end_datetime);
echo "Méthode 2 (createFromFormat):\n";
echo "  Comparaison: " . ($s2 < $e2 ? '✅ start < end' : '❌ start >= end') . "\n";

// Méthode 3: Parse avec timezone explicite
$s3 = Carbon::parse($start_datetime, config('app.timezone'));
$e3 = Carbon::parse($end_datetime, config('app.timezone'));
echo "Méthode 3 (parse avec timezone):\n";
echo "  Comparaison: " . ($s3 < $e3 ? '✅ start < end' : '❌ start >= end') . "\n\n";

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                     DIAGNOSTIC TERMINÉ                       ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "📝 CONCLUSION:\n";
if ($start_carbon >= $end_carbon) {
    echo "   ❌ Le bug est REPRODUIT avec ce script\n";
    echo "   ❌ La comparaison Carbon échoue alors qu'elle devrait passer\n";
    echo "   🔍 Analyse des causes possibles:\n";
    echo "      - Problème de timezone?\n";
    echo "      - Problème de parsing?\n";
    echo "      - Problème de comparaison?\n";
} else {
    echo "   ✅ Les dates se comparent correctement\n";
    echo "   🔍 Le problème doit être ailleurs:\n";
    echo "      - Autre validation dans le code?\n";
    echo "      - Problème dans le formulaire Livewire?\n";
    echo "      - Cache Laravel?\n";
}

echo "\n";
