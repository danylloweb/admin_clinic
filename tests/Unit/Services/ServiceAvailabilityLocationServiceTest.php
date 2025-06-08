<?php

namespace Tests\Unit\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\ServiceAvailabilityLocationRepository;
use App\Services\ServiceAvailabilityLocationService;
use Tests\TestCase;
use Mockery;

class ServiceAvailabilityLocationServiceTest extends TestCase
{
    protected $serviceAvailabilityLocationService;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ServiceAvailabilityLocationRepository::class);
        $this->serviceAvailabilityLocationService = new ServiceAvailabilityLocationService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_create_service_availability_location()
    {
        $data = [
            'service_id' => 1,
            'location_id' => 1,
            'provider_id' => 1,
            'is_available' => true,
            'availability_rules' => json_encode([
                'days' => ['monday', 'tuesday', 'wednesday'],
                'hours' => ['09:00-18:00']
            ])
        ];

        $expectedResult = array_merge($data, ['id' => 1]);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->serviceAvailabilityLocationService->create($data);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_return_all_availabilities_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'service_id' => 1,
                    'location_id' => 1,
                    'provider_id' => 1,
                    'is_available' => true,
                    'availability_rules' => json_encode([
                        'days' => ['monday', 'tuesday'],
                        'hours' => ['09:00-17:00']
                    ])
                ]
            ],
            'total' => 1,
            'per_page' => 20,
            'current_page' => 1
        ];

        $this->repository->shouldReceive('resetCriteria')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('pushCriteria')
            ->withAnyArgs()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('paginate')
            ->once()
            ->with($limit)
            ->andReturn($expectedResult);

        $result = $this->serviceAvailabilityLocationService->all($limit);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_availability_by_id()
    {
        $id = 1;
        $expectedResult = [
            'id' => $id,
            'service_id' => 1,
            'location_id' => 1,
            'provider_id' => 1,
            'is_available' => true
        ];

        $this->repository->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->serviceAvailabilityLocationService->find($id);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_update_availability()
    {
        $id = 1;
        $data = [
            'is_available' => false,
            'availability_rules' => json_encode([
                'days' => ['thursday', 'friday'],
                'hours' => ['10:00-16:00']
            ])
        ];

        $expectedResult = array_merge(['id' => $id], $data);

        $this->repository->shouldReceive('update')
            ->once()
            ->with($data, $id)
            ->andReturn($expectedResult);

        $result = $this->serviceAvailabilityLocationService->update($data, $id);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_availabilities_by_location()
    {
        $locationId = 1;
        $expectedResult = [
            [
                'id' => 1,
                'service_id' => 1,
                'location_id' => $locationId,
                'provider_id' => 1,
                'is_available' => true
            ],
            [
                'id' => 2,
                'service_id' => 2,
                'location_id' => $locationId,
                'provider_id' => 1,
                'is_available' => true
            ]
        ];

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(['location_id' => $locationId])
            ->andReturn($expectedResult);

        $result = $this->serviceAvailabilityLocationService->findWhere(['location_id' => $locationId]);
        $this->assertEquals($expectedResult, $result);
    }
} 