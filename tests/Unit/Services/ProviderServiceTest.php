<?php

namespace Tests\Unit\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\ProviderRepository;
use App\Services\ProviderService;
use Tests\TestCase;
use Mockery;

class ProviderServiceTest extends TestCase
{
    protected $providerService;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ProviderRepository::class);
        $this->providerService = new ProviderService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_return_all_providers_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Provider One',
                    'document' => '12345678901',
                    'email' => 'provider1@example.com',
                    'phone' => '5511999999999',
                    'active' => true
                ],
                [
                    'id' => 2,
                    'name' => 'Provider Two',
                    'document' => '12345678902',
                    'email' => 'provider2@example.com',
                    'phone' => '5511999999998',
                    'active' => true
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

        $result = $this->providerService->all($limit);
        
        $this->assertEquals($expectedResult, $result);
    }
} 