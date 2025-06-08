<?php

namespace Tests\Unit\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByServiceConfigTypeCriteria;
use App\Repositories\ServiceConfigurationRepository;
use App\Services\ServiceConfigurationService;
use Tests\TestCase;
use Mockery;

class ServiceConfigurationServiceTest extends TestCase
{
    protected $serviceConfigurationService;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ServiceConfigurationRepository::class);
        $this->serviceConfigurationService = new ServiceConfigurationService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_create_service_configuration()
    {
        $data = [
            'service_id' => 1,
            'provider_id' => 1,
            'config_key' => 'duration',
            'config_value' => '60',
            'is_active' => true,
            'metadata' => json_encode([
                'unit' => 'minutes',
                'type' => 'numeric'
            ])
        ];

        $expectedResult = array_merge($data, ['id' => 1]);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->serviceConfigurationService->create($data);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_return_all_configurations_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'service_id' => 1,
                    'config_key' => 'duration',
                    'config_value' => '60'
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

        $result = $this->serviceConfigurationService->all($limit);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_configuration_by_id()
    {
        $id = 1;
        $expectedResult = [
            'id' => $id,
            'service_id' => 1,
            'provider_id' => 1,
            'config_key' => 'duration',
            'config_value' => '60',
            'is_active' => true
        ];

        $this->repository->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->serviceConfigurationService->find($id);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_update_configuration()
    {
        $id = 1;
        $data = [
            'config_value' => '90',
            'is_active' => true,
            'metadata' => json_encode([
                'unit' => 'minutes',
                'type' => 'numeric',
                'updated_at' => '2024-03-01'
            ])
        ];

        $expectedResult = array_merge(['id' => $id], $data);

        $this->repository->shouldReceive('update')
            ->once()
            ->with($data, $id)
            ->andReturn($expectedResult);

        $result = $this->serviceConfigurationService->update($data, $id);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_configurations_by_service()
    {
        $serviceId = 1;
        $expectedResult = [
            [
                'id' => 1,
                'service_id' => $serviceId,
                'config_key' => 'duration',
                'config_value' => '60'
            ],
            [
                'id' => 2,
                'service_id' => $serviceId,
                'config_key' => 'price',
                'config_value' => '100.00'
            ]
        ];

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(['service_id' => $serviceId])
            ->andReturn($expectedResult);

        $result = $this->serviceConfigurationService->findWhere(['service_id' => $serviceId]);
        $this->assertEquals($expectedResult, $result);
    }
} 