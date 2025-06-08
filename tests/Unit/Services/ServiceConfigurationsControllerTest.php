<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Http\Controllers\ServiceConfigurationsController;
use App\Services\ServiceConfigurationService;
use App\Validators\ServiceConfigurationValidator;
use App\Services\JsonGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Mockery;

class ServiceConfigurationsControllerTest extends TestCase
{
    protected $controller;
    protected $service;
    protected $validator;
    protected $jsonGeneratorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(ServiceConfigurationService::class);
        $this->validator = Mockery::mock(ServiceConfigurationValidator::class);
        $this->jsonGeneratorService = Mockery::mock(JsonGeneratorService::class);
        $this->controller = new ServiceConfigurationsController($this->service, $this->validator, $this->jsonGeneratorService);
    }

    public function test_generate_json_returns_json_response()
    {
        $request = new Request(['key' => 'value']);
        $expectedResponse = ['success' => true];

        $this->jsonGeneratorService->shouldReceive('generatejson')
            ->once()
            ->with($request->all())
            ->andReturn($expectedResponse);

        $response = $this->controller->generateJson($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function test_list_one_off_services_returns_paginated_json_response()
    {
        $request = new Request(['limit' => 15]);
        $expectedResponse = [
            'data' => [],
            'total' => 0,
            'per_page' => 15,
            'current_page' => 1,
            'last_page' => 1,
            'next_page_url' => null,
        ];

        $this->jsonGeneratorService->shouldReceive('all')
            ->once()
            ->with($request->get('limit', 15))
            ->andReturn($expectedResponse);

        $response = $this->controller->listOneOffServices($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 