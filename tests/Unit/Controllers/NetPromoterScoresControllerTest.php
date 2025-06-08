<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\NetPromoterScoresController;
use App\Services\NetPromoterScoreService;
use Mockery;
use Illuminate\Http\Request;
use App\Validators\NetPromoterScoreValidator;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\NetPromoterScoreCreateRequest;

class NetPromoterScoresControllerTest extends TestCase
{
    protected $controller;
    protected $service;
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(NetPromoterScoreService::class);
        $this->validator = Mockery::mock(NetPromoterScoreValidator::class);
        $this->controller = new NetPromoterScoresController($this->service, $this->validator);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(NetPromoterScoresController::class, $this->controller);
    }

    public function test_process_store_calls_store_method(): void
    {
        $request = Mockery::mock(NetPromoterScoreCreateRequest::class);

        $this->service->shouldReceive('store')->with($request);

        $response = $this->controller->processStore($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}