<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\OrderItemsController;
use App\Services\OrderItemService;
use Mockery;
use Illuminate\Http\Request;
use App\Validators\OrderItemValidator;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\ScheduleServiceRequest;
use App\Services\NotificationTransactionLogService;
use Illuminate\Http\JsonResponse;

class OrderItemsControllerTest extends TestCase
{
    protected $controller;
    protected $service;
    protected $validator;
    protected $notificationTransactionLogService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(OrderItemService::class);
        $this->validator = Mockery::mock(OrderItemValidator::class);
        $this->notificationTransactionLogService = Mockery::mock(NotificationTransactionLogService::class);
        $this->controller = new OrderItemsController(
            $this->service,
            $this->validator,
            $this->notificationTransactionLogService
        );
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(OrderItemsController::class, $this->controller);
    }

    public function test_index_returns_cached_items(): void
    {
        $request = Mockery::mock(Request::class);
        $request->query = Mockery::mock('stdClass');
        $request->query->shouldReceive('get')->with('limit', 15)->andReturn(15);
        $request->shouldReceive('fullUrl')->andReturn('http://test.com');

        $cache = Mockery::mock('cache');
        $cache->shouldReceive('tags')->with('ordersItems')->andReturnSelf();
        $cache->shouldReceive('remember')->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

        Cache::shouldReceive('store')->with('redis')->andReturn($cache);

        $this->service->shouldReceive('all')
            ->with(15)
            ->andReturn(['data' => []]);

        $response = $this->controller->index($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => []], json_decode($response->getContent(), true));
    }

    public function test_schedule_service_returns_success(): void
    {
        $requestData = [
            'service_id' => 1,
            'schedule_date' => '2024-03-20'
        ];
        
        $expectedResponse = [
            'status' => 'scheduled',
            'message' => 'Service scheduled successfully'
        ];

        $request = new ScheduleServiceRequest();
        $request->merge($requestData);

        $this->service->shouldReceive('scheduleService')
            ->with($requestData)
            ->once()
            ->andReturn($expectedResponse);

        $response = $this->controller->scheduleService($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}