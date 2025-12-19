<?php

use App\Enums\DriverStatusEnum;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verifying DriverStatusEnum...\n";

// 1. Verify Case
try {
    $status = DriverStatusEnum::EN_FORMATION;
    echo "✅ Case EN_FORMATION exists.\n";
} catch (\Throwable $e) {
    echo "❌ Case EN_FORMATION missing.\n";
    exit(1);
}

// 2. Verify Label
if ($status->label() === 'En formation') {
    echo "✅ Label is correct.\n";
} else {
    echo "❌ Label is incorrect: " . $status->label() . "\n";
}

// 3. Verify Icon
if ($status->icon() === 'graduation-cap') {
    echo "✅ Icon is correct (graduation-cap).\n";
} else {
    echo "❌ Icon is incorrect: " . $status->icon() . "\n";
}

// 4. Verify Other Icons
$conge = DriverStatusEnum::EN_CONGE;
if ($conge->icon() === 'palmtree') {
    echo "✅ Conge Icon is correct (palmtree).\n";
} else {
    echo "❌ Conge Icon is incorrect: " . $conge->icon() . "\n";
}

$mission = DriverStatusEnum::EN_MISSION;
if ($mission->icon() === 'truck') {
    echo "✅ Mission Icon is correct (truck).\n";
} else {
    echo "❌ Mission Icon is incorrect: " . $mission->icon() . "\n";
}

// 5. Verify Transitions
$disponible = DriverStatusEnum::DISPONIBLE;
if (in_array(DriverStatusEnum::EN_FORMATION, $disponible->allowedTransitions())) {
    echo "✅ Transition DISPONIBLE -> EN_FORMATION allowed.\n";
} else {
    echo "❌ Transition DISPONIBLE -> EN_FORMATION NOT allowed.\n";
}

if (in_array(DriverStatusEnum::DISPONIBLE, $status->allowedTransitions())) {
    echo "✅ Transition EN_FORMATION -> DISPONIBLE allowed.\n";
} else {
    echo "❌ Transition EN_FORMATION -> DISPONIBLE NOT allowed.\n";
}

echo "Verification complete.\n";
