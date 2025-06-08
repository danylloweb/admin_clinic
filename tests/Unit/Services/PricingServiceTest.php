<?php

namespace Tests\Unit\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\PricingRepository;
use App\Services\PricingService;
use Tests\TestCase;
use Mockery;
use App\Integrations\OmniServiceQuotationIntegration;

class PricingServiceTest extends TestCase
{
    protected $pricingService;
    protected $repository;
    protected $omniServiceQuotationIntegration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(PricingRepository::class);
        $this->omniServiceQuotationIntegration = Mockery::mock(OmniServiceQuotationIntegration::class);
        $this->pricingService = new PricingService(
            $this->repository,
            $this->omniServiceQuotationIntegration
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_return_all_pricing_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'service_id' => 1,
                    'price' => 150.00,
                    'start_date' => '2024-01-01',
                    'end_date' => '2024-12-31',
                    'active' => true
                ],
                [
                    'id' => 2,
                    'service_id' => 2,
                    'price' => 200.00,
                    'start_date' => '2024-01-01',
                    'end_date' => '2024-12-31',
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

        $result = $this->pricingService->all($limit);
        
        $this->assertEquals($expectedResult, $result);
    }
} 