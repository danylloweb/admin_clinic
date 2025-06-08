<?php

namespace Tests\Unit\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\LocationRangeRepository;
use App\Services\LocationRangeService;
use Tests\TestCase;
use Mockery;

class LocationRangeServiceTest extends TestCase
{
    protected $locationRangeService;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(LocationRangeRepository::class);
        $this->locationRangeService = new LocationRangeService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_return_all_location_ranges_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'start_range' => 0,
                    'end_range' => 10,
                    'price' => 50.00
                ],
                [
                    'id' => 2,
                    'start_range' => 11,
                    'end_range' => 20,
                    'price' => 75.00
                ]
            ],
            'total' => 2,
            'per_page' => 20,
            'current_page' => 1
        ];

        $this->repository->shouldReceive('resetCriteria')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('pushCriteria')
            ->once()
            ->with(Mockery::type(AppRequestCriteria::class))
            ->andReturn($this->repository);

        $this->repository->shouldReceive('paginate')
            ->once()
            ->with($limit)
            ->andReturn($expectedResult);

        $result = $this->locationRangeService->all($limit);
        
        $this->assertEquals($expectedResult, $result);
    }
} 