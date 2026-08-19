<?php

namespace Tests\Unit\Services;

use App\Services\CampaignProgressService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CampaignProgressServiceTest extends TestCase
{
    private CampaignProgressService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CampaignProgressService::class);
        Cache::flush();
    }

    public function test_get_progress_returns_default_state()
    {
        $progress = $this->service->getProgress(1);

        $this->assertEquals(1, $progress['campaign_id']);
        $this->assertEquals(0, $progress['sent']);
        $this->assertEquals(0, $progress['failed']);
        $this->assertEquals(0, $progress['processed']);
        $this->assertFalse($progress['running']);
        $this->assertFalse($progress['finished']);
    }

    public function test_initialize_campaign()
    {
        $campaignId = 1;
        $total = 100;

        $state = $this->service->initialize($campaignId, $total);

        $this->assertEquals($campaignId, $state['campaign_id']);
        $this->assertEquals($total, $state['total']);
        $this->assertEquals(0, $state['sent']);
        $this->assertTrue($state['running']);
        $this->assertFalse($state['finished']);
        $this->assertNotNull($state['started_at']);
    }

    public function test_record_sent_increments_counters()
    {
        $campaignId = 1;
        $patientId = 100;

        $this->service->initialize($campaignId, 1);
        $this->service->recordSent($campaignId, $patientId);

        $progress = $this->service->getProgress($campaignId);

        $this->assertEquals(1, $progress['sent']);
        $this->assertEquals(1, $progress['processed']);
        $this->assertEquals($patientId, $progress['last_patient_id']);
        $this->assertNotNull($progress['updated_at']);
    }

    public function test_record_failed_increments_failed_counter()
    {
        $campaignId = 1;
        $patientId = 100;

        $this->service->initialize($campaignId, 1);
        $this->service->recordFailed($campaignId, $patientId, 'Test error');

        $progress = $this->service->getProgress($campaignId);

        $this->assertEquals(1, $progress['failed']);
        $this->assertEquals(1, $progress['processed']);
        $this->assertEquals($patientId, $progress['last_patient_id']);
    }

    public function test_mark_as_finished()
    {
        $campaignId = 1;

        $this->service->initialize($campaignId, 3);
        $this->service->recordSent($campaignId, 1);
        $this->service->recordSent($campaignId, 2);
        $this->service->recordSent($campaignId, 3);

        $this->service->markAsFinished($campaignId);

        $progress = $this->service->getProgress($campaignId);

        $this->assertTrue($progress['finished']);
        $this->assertFalse($progress['running']);
        $this->assertNotNull($progress['finished_at']);
    }

    public function test_multiple_increments()
    {
        $campaignId = 1;

        $this->service->initialize($campaignId, 10);

        for ($i = 1; $i <= 10; $i++) {
            $this->service->recordSent($campaignId, $i);
        }

        $progress = $this->service->getProgress($campaignId);

        $this->assertEquals(10, $progress['sent']);
        $this->assertEquals(10, $progress['processed']);
        $this->assertEquals(10, $progress['last_patient_id']);
    }

    public function test_mixed_sent_and_failed()
    {
        $campaignId = 1;

        $this->service->initialize($campaignId, 5);

        $this->service->recordSent($campaignId, 1);
        $this->service->recordFailed($campaignId, 2, 'Error 1');
        $this->service->recordSent($campaignId, 3);
        $this->service->recordFailed($campaignId, 4, 'Error 2');
        $this->service->recordSent($campaignId, 5);

        $progress = $this->service->getProgress($campaignId);

        $this->assertEquals(3, $progress['sent']);
        $this->assertEquals(2, $progress['failed']);
        $this->assertEquals(5, $progress['processed']);
        $this->assertEquals(5, $progress['last_patient_id']);
    }
}

