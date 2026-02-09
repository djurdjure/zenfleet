<?php

namespace Tests\Feature\Admin;

use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StatusAnalyticsApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_normalized_chart_payload_from_daily_stats_api(): void
    {
        $organization = Organization::factory()->create();

        $user = User::factory()->create([
            'organization_id' => $organization->id,
        ]);

        StatusHistory::query()->create([
            'statusable_type' => \App\Models\Vehicle::class,
            'statusable_id' => 123,
            'from_status' => 'parking',
            'to_status' => 'affecte',
            'change_type' => 'manual',
            'organization_id' => $organization->id,
            'changed_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.analytics.api.daily-stats', [
            'entity_type' => 'vehicle',
            'start_date' => now()->subDays(7)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'payload' => [
                    'meta' => [
                        'version',
                        'source',
                        'tenant_id',
                        'scope_role',
                        'period',
                        'filters',
                        'timezone',
                        'currency',
                        'generated_at',
                    ],
                    'chart' => [
                        'id',
                        'type',
                        'height',
                        'ariaLabel',
                    ],
                    'labels',
                    'series',
                    'options',
                ],
            ])
            ->assertJsonPath('payload.meta.version', '1.0')
            ->assertJsonPath('payload.meta.tenant_id', $organization->id)
            ->assertJsonPath('payload.chart.id', 'status-daily-changes-api');
    }
}
