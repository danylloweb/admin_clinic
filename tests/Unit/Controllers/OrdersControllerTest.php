<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\OrdersController;
use App\Services\OrderService;
use Mockery;
use Illuminate\Http\Request;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderEsternalIdRequest;
use App\Http\Requests\ScheduleHasCustomerRequest;
use App\Validators\OrderValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class OrdersControllerTest extends TestCase
{
    protected $controller;
    protected $service;
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(OrderService::class);
        $this->validator = Mockery::mock(OrderValidator::class);
        $this->controller = new OrdersController($this->service, $this->validator);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(OrdersController::class, $this->controller);
    }

    public function test_index_returns_json_response(): void
    {
        $cacheMock = Mockery::mock('cache');
        Cache::shouldReceive('store')
            ->with('redis')
            ->once()
            ->andReturn($cacheMock);
            
        $cacheMock->shouldReceive('tags')
            ->with('orders')
            ->once()
            ->andReturnSelf();
            
        $cacheMock->shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $request = new Request(['limit' => 10]);

        $this->service->shouldReceive('all')->with(10)->andReturn(['order1', 'order2']);

        $response = $this->controller->index($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['order1', 'order2'], $response->getData(true));
    }

    public function test_processStore_creates_order(): void
    {
        $request = Mockery::mock(OrderCreateRequest::class);
        $request->shouldReceive('all')->andReturn(['data' => 'value']);
        $this->service->shouldReceive('create')->with(['data' => 'value'])->andReturn(['success' => true]);

        $response = $this->controller->processStore($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->getData(true));
    }

    public function test_paymentConfirmation_returns_order(): void
    {
        $request = Mockery::mock(OrderEsternalIdRequest::class);
        $request->shouldReceive('all')->andReturn(['external_id' => '123']);
        $this->service->shouldReceive('paymentConfirmation')->with(['external_id' => '123'])
            ->andReturn(['order' => 'details']);

        $response = $this->controller->paymentConfirmation($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['order' => 'details'], $response->getData(true));
    }

    public function test_verifyNotification_returns_order(): void
    {
        $request = Mockery::mock(ScheduleHasCustomerRequest::class);
        $request->shouldReceive('all')->andReturn(['notification' => 'data']);
        $this->service->shouldReceive('verifyNotification')->with(['notification' => 'data'])
            ->andReturn(['order' => 'verified']);

        $response = $this->controller->verifyNotification($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => ['order' => 'verified']], $response->getData(true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
