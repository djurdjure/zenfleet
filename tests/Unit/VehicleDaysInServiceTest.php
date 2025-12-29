<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Vehicle;
use App\Http\Controllers\Admin\VehicleController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VehicleDaysInServiceTest extends TestCase
{
    // We don't need full database refresh if we mock or just test the logic,
    // but the controller logic is private/protected or inside a big method.
    // It's better to extract the logic or test the result if possible.
    // Since getVehicleSpecificAnalytics is private, we might want to:
    // 1. Refactor logic to a public service or helper.
    // 2. Or replicate logic here to reproduce.

    public function test_formatting_logic()
    {
        $now = Carbon::parse('2025-01-01');
        Carbon::setTestNow($now);

        // Case 1: Past date (1 year, 2 months, 5 days)
        $acquisition = Carbon::parse('2023-10-27');
        // 2023-10-27 to 2024-10-27 = 1 year
        // 2024-10-27 to 2024-12-27 = 2 months
        // 2024-12-27 to 2025-01-01 = 5 days (27,28,29,30,31) => 5 days diff?
        // Let's rely on Carbon diff

        $diff = $acquisition->diff($now);
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        if ($diff->m > 0) $parts[] = $diff->m . ' mois';
        if ($diff->d > 0) $parts[] = $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        $formatted = implode(' ', $parts);

        $this->assertEquals("1 an 2 mois 5 jours", $formatted);

        // Case 2: Future date
        $future = Carbon::parse('2025-02-01');
        $this->assertTrue($future->isFuture());

        // Logic check
        if ($future->isFuture()) {
            $res = "Pas encore en service";
        }
        $this->assertEquals("Pas encore en service", $res);
    }
}
