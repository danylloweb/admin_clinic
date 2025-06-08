<?php

namespace Tests\Unit\Services;

use App\Entities\NotificationTransactionLog;
use App\Integrations\OmniServiceBffIntegration;
use App\Services\NotificationTransactionLogService;
use Mockery;
use Tests\TestCase;

class NotificationTransactionLogServiceTest extends TestCase
{
    private NotificationTransactionLogService $service;
    private NotificationTransactionLog $repository;
    private OmniServiceBffIntegration $bffIntegration;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = Mockery::mock(NotificationTransactionLog::class);
        $this->bffIntegration = Mockery::mock(OmniServiceBffIntegration::class);
        $this->service = new NotificationTransactionLogService($this->repository, $this->bffIntegration);
    }

    public function test_resend_successfully()
    {
        // Arrange
        $mockLog = Mockery::mock(NotificationTransactionLog::class)->makePartial();
        $mockLogReplicated = Mockery::mock(NotificationTransactionLog::class)->makePartial();
        $notificationPayload = ['test' => 'payload'];
        $notificationResponse = ['success' => true];

        $this->repository->shouldReceive('find')->andReturn($mockLog);
        $mockLog->shouldReceive('replicate')->andReturn($mockLogReplicated);
        $mockLog->shouldReceive('getAttribute')->with('notification_payload')->andReturn($notificationPayload);
        $this->bffIntegration->shouldReceive('send')
            ->with('POST', '/v1/service-hub-api/resend-transaction', ['payload' => $notificationPayload])
            ->andReturn($notificationResponse);
        $mockLogReplicated->shouldReceive('setAttribute')->with('notification_response', $notificationResponse);
        $mockLogReplicated->shouldReceive('save');

        // Act
        $result = $this->service->resend('123');

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Notification sent successfully', $result['message']);
        $this->assertEquals($mockLogReplicated, $result['data']);
    }

    public function test_resend_fails_when_log_not_found()
    {
        // Arrange
        $this->repository->shouldReceive('find')->andReturn(null);

        // Act
        $result = $this->service->resend('123');

        // Assert
        $this->assertFalse($result['success']);
    }

    public function test_resend_fails_when_exception_occurs()
    {
        // Arrange
        $mockLog = Mockery::mock(NotificationTransactionLog::class);
        $this->repository->shouldReceive('find')->andReturn($mockLog);
        $mockLog->shouldReceive('replicate')->andThrow(new \Exception('Test error'));

        // Act
        $result = $this->service->resend('123');

        // Assert
        $this->assertFalse($result['success']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 