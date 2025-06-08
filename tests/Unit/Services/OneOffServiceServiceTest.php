<?php

namespace Tests\Unit\Services;

use App\Entities\OneOffService;
use App\Services\OneOffServiceService;
use Tests\TestCase;
use Mockery;

class OneOffServiceServiceTest extends TestCase
{
    protected $oneOffServiceService;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(OneOffService::class);
        $this->oneOffServiceService = new OneOffServiceService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_create_new_one_off_service()
    {
        $data = [
            'name' => 'Special Cleaning Service',
            'description' => 'Deep cleaning for specific areas',
            'price' => 150.00,
            'duration' => 120,
            'active' => true
        ];

        $expectedResult = array_merge($data, ['_id' => '507f1f77bcf86cd799439011']);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->oneOffServiceService->create($data);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_return_all_one_off_services_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    '_id' => '507f1f77bcf86cd799439011',
                    'name' => 'Special Cleaning Service',
                    'description' => 'Deep cleaning for specific areas',
                    'price' => 150.00,
                    'duration' => 120,
                    'active' => true
                ],
                [
                    '_id' => '507f1f77bcf86cd799439012',
                    'name' => 'Emergency Plumbing',
                    'description' => 'Quick response plumbing service',
                    'price' => 200.00,
                    'duration' => 60,
                    'active' => true
                ]
            ],
            'total' => 2,
            'per_page' => 20,
            'current_page' => 1
        ];

        $this->repository->shouldReceive('paginate')
            ->once()
            ->with($limit)
            ->andReturn($expectedResult);

        $result = $this->oneOffServiceService->all($limit);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_create_one_off_service_with_minimal_required_data()
    {
        $data = [
            'name' => 'Basic Service',
            'price' => 100.00,
            'duration' => 60
        ];

        $expectedResult = array_merge($data, [
            '_id' => '507f1f77bcf86cd799439013',
            'active' => true,
            'description' => null
        ]);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->oneOffServiceService->create($data);
        $this->assertEquals($expectedResult, $result);
    }
} 