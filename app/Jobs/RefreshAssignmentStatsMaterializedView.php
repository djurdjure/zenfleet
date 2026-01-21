<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshAssignmentStatsMaterializedView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 300;

    public function handle(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $view = DB::selectOne("SELECT to_regclass('assignment_stats_daily') as regclass");

        if (!$view || $view->regclass === null) {
            return;
        }

        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY assignment_stats_daily');

        Log::info('[RefreshAssignmentStatsMaterializedView] Materialized view refreshed');
    }
}
