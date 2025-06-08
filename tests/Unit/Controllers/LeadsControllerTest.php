<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\LeadsController;
use App\Services\LeadService;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LeadsStoreRequest;

class LeadsControllerTest extends TestCase
{
    protected $controller;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(LeadService::class);
        $this->controller = new LeadsController($this->service);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(LeadsController::class, $this->controller);
    }

    public function test_store_lead_returns_json_response(): void
    {
        $requestData = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $expectedData = ['id' => 1] + $requestData;

        $request = Mockery::mock(LeadsStoreRequest::class);
        $request->shouldReceive('all')->once()->andReturn($requestData);

        $this->service
            ->shouldReceive('create')
            ->once()
            ->with($requestData)
            ->andReturn($expectedData);

        $response = $this->controller->storeLead($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($expectedData, json_decode($response->getContent(), true));
    }

    public function test_index_lead_returns_paginated_json_response(): void
    {
        $paginatedData = [
            'data' => [
                ['id' => 1, 'name' => 'John Doe'],
                ['id' => 2, 'name' => 'Jane Doe']
            ],
            'total' => 2,
            'per_page' => 15,
            'current_page' => 1,
            'last_page' => 1,
            'next_page_url' => null,
            'prev_page_url' => null,
            'from' => 1,
            'to' => 2
        ];

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('get')
            ->with('limit', 15)
            ->once()
            ->andReturn(15);

        $this->service
            ->shouldReceive('all')
            ->once()
            ->with(15)
            ->andReturn($paginatedData);

        $response = $this->controller->indexLead($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals([
            'data' => $paginatedData['data'],
            'meta' => [
                'pagination' => [
                    'total' => $paginatedData['total'],
                    'count' => count($paginatedData['data']),
                    'per_page' => $paginatedData['per_page'],
                    'current_page' => $paginatedData['current_page'],
                    'total_pages' => $paginatedData['last_page'],
                    'links' => [
                        'next' => $paginatedData['next_page_url']
                    ]
                ]
            ]
        ], json_decode($response->getContent(), true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}