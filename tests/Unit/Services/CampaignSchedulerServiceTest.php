<?php

namespace Tests\Unit\Services;

use App\Services\CampaignSchedulerService;
use Carbon\Carbon;
use Tests\TestCase;

class CampaignSchedulerServiceTest extends TestCase
{
    private CampaignSchedulerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CampaignSchedulerService::class);
    }

    public function test_calculate_scheduling_stats_with_100_patients()
    {
        $stats = $this->service->calculateSchedulingStats(100);

        $this->assertEquals(100, $stats['total_patients']);
        $this->assertGreaterThan(0, $stats['window_seconds']);
        $this->assertGreaterThan(0, $stats['interval_seconds']);
        $this->assertTrue($stats['randomization_enabled']);
    }

    public function test_calculate_scheduling_stats_with_1000_patients()
    {
        $stats = $this->service->calculateSchedulingStats(1000);

        $this->assertEquals(1000, $stats['total_patients']);
        $this->assertGreaterThan(0, $stats['interval_seconds']);
    }

    public function test_calculate_scheduling_stats_with_0_patients()
    {
        $stats = $this->service->calculateSchedulingStats(0);

        $this->assertEquals(0, $stats['total_patients']);
    }

    public function test_interval_respects_min_delay_config()
    {
        $this->app['config']->set('campaign.send.min_delay_seconds', 60);
        $this->app['config']->set('campaign.send.max_delay_seconds', 120);

        $stats = $this->service->calculateSchedulingStats(10000);

        // Com 10000 pacientes em 12 horas, o intervalo seria muito pequeno
        // Deve ser limitado ao mínimo configurado
        $this->assertGreaterThanOrEqual(60, $stats['interval_seconds']);
    }

    public function test_window_start_and_end_are_valid()
    {
        $stats = $this->service->calculateSchedulingStats(100);

        $this->assertNotNull($stats['window_start']);
        $this->assertNotNull($stats['window_end']);

        $start = Carbon::parse($stats['window_start']);
        $end = Carbon::parse($stats['window_end']);

        $this->assertTrue($end->isAfter($start));
    }
}

