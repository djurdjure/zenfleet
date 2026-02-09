<?php

namespace Tests\Feature\Views;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChartsWidgetComponentTest extends TestCase
{
    #[Test]
    public function it_renders_unified_chart_payload_contract(): void
    {
        $view = $this->blade('<x-charts.widget id="fleet-chart" type="bar" :labels="$labels" :series="$series" :options="$options" />', [
            'labels' => ['Lun', 'Mar'],
            'series' => [['name' => 'Depenses', 'data' => [1200, 980]]],
            'options' => ['stroke' => ['width' => 3]],
        ]);

        $view->assertSee('data-chart-payload=', false);
        $view->assertSee('"version":"1.0"', false);
        $view->assertSee('"id":"fleet-chart"', false);
        $view->assertSee('"type":"bar"', false);
        $view->assertSee('"labels":["Lun","Mar"]', false);
    }
}
