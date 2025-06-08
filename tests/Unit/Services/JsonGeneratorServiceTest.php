<?php

namespace Tests\Unit\Services;

use App\Entities\OneOffService;
use App\Services\JsonGeneratorService;
use App\Dto\VariantDTO;
use Tests\TestCase;
use Mockery;
use App\Services\SkuIteratorService;
use App\Services\PricingService;
use App\Enums\ServiceIdEnum;

class JsonGeneratorServiceTest extends TestCase
{
    protected $jsonGeneratorService;
    protected $repository;
    protected $skuIteratorService;
    protected $pricingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(OneOffService::class);
        $this->skuIteratorService = Mockery::mock(SkuIteratorService::class);
        $this->pricingService = Mockery::mock(PricingService::class);
        $this->jsonGeneratorService = new JsonGeneratorService(
            $this->repository,
            $this->skuIteratorService,
            $this->pricingService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_generate_json_with_variants()
    {
        $this->repository->shouldReceive('find')
            ->with(1)
            ->andReturn((object)[
                'id' => 1,
                'name' => 'Test Service',
                'description' => 'Test Description',
                'serviceConfiguration' => (object)[
                    'id' => 1,
                    'name' => 'Test Configuration',
                    'variants' => [
                        (object)[
                            'id' => 1,
                            'name' => 'Size',
                            'values' => [
                                (object)['id' => 1, 'value' => 'Small'],
                                (object)['id' => 2, 'value' => 'Medium']
                            ]
                        ],
                        (object)[
                            'id' => 2,
                            'name' => 'Color',
                            'values' => [
                                (object)['id' => 3, 'value' => 'Red'],
                                (object)['id' => 4, 'value' => 'Blue']
                            ]
                        ]
                    ]
                ]
            ]);

        $this->repository->shouldReceive('create')
            ->andReturn((object)[
                'id' => 1,
                'name' => 'Test Service',
                'description' => 'Test Description'
            ]);

        $this->skuIteratorService->shouldReceive('getChunk')
            ->with(4)
            ->andReturn(['SKU001', 'SKU002', 'SKU003', 'SKU004']);

        $this->pricingService->shouldReceive('updatePriceByRefId')
            ->andReturn(['success' => true, 'message' => 'Price updated successfully']);

        $data = [
            'service_ref_id' => ServiceIdEnum::MOUNTING->value,
            'name' => 'Test Service',
            'description' => 'Test Description',
            'base_price' => 100,
            'variants_attributes' => [
                [
                    'name' => 'Size',
                    'values' => ['Small', 'Medium']
                ],
                [
                    'name' => 'Color',
                    'values' => ['Red', 'Blue']
                ]
            ]
        ];

        $result = $this->jsonGeneratorService->generateJson($data);

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertEquals('SKU001', $result[0]['variant']['value']['data']);
        $this->assertEquals('small_Size_red_Color', $result[0]['variant']['value']['scope']);
    }

    public function test_it_should_return_all_items_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'name' => 'Product 1',
                    'enabled' => true,
                    'variants' => []
                ],
                [
                    'name' => 'Product 2',
                    'enabled' => true,
                    'variants' => []
                ]
            ],
            'total' => 2,
            'per_page' => 20,
            'current_page' => 1,
        ];

        $this->repository->shouldReceive('paginate')
            ->once()
            ->with($limit)
            ->andReturn($expectedResult);

        $result = $this->jsonGeneratorService->all($limit);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_generate_json_with_minimal_data()
    {
        $this->repository->shouldReceive('find')
            ->with(1)
            ->andReturn((object)[
                'id' => 1,
                'name' => 'Test Service',
                'description' => 'Test Description',
                'serviceConfiguration' => (object)[
                    'id' => 1,
                    'name' => 'Test Configuration',
                    'variants' => []
                ]
            ]);

        $this->repository->shouldReceive('create')
            ->andReturn((object)[
                'id' => 1,
                'name' => 'Test Service',
                'description' => 'Test Description'
            ]);

        $this->skuIteratorService->shouldReceive('getChunk')
            ->with(1)
            ->andReturn(['SKU001']);

        $this->pricingService->shouldReceive('updatePriceByRefId')
            ->andReturn(['success' => true, 'message' => 'Price updated successfully']);

        $data = [
            'service_ref_id' => ServiceIdEnum::MOUNTING->value,
            'name' => 'Test Service',
            'description' => 'Test Description',
            'base_price' => 100,
            'variants_attributes' => []
        ];

        $result = $this->jsonGeneratorService->generateJson($data);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('SKU001', $result[0]['variant']['value']['data']);
        $this->assertEquals('', $result[0]['variant']['value']['scope']);
    }
} 