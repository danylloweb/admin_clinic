<?php

namespace Tests\Unit\Services;

use App\Entities\Lead;
use App\Services\LeadService;
use Tests\TestCase;
use Mockery;

class LeadServiceTest extends TestCase
{
    protected $leadService;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(Lead::class);
        $this->leadService = new LeadService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_create_new_lead()
    {
        $data = [
            'name' => 'John Doe',
            'phone' => '5511999999999',
            'email' => 'john@example.com',
            'notification_sms' => true,
            'notification_email' => true,
            'notification_whatsapp' => false,
            'quotation' => ['item1' => 100, 'item2' => 200],
        ];

        $expectedResult = array_merge($data, ['_id' => '507f1f77bcf86cd799439011']);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->leadService->create($data);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_return_all_leads_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    '_id' => '507f1f77bcf86cd799439011',
                    'name' => 'John Doe',
                    'phone' => '5511999999999',
                    'email' => 'john@example.com',
                ],
                [
                    '_id' => '507f1f77bcf86cd799439012',
                    'name' => 'Jane Doe',
                    'phone' => '5511999999998',
                    'email' => 'jane@example.com',
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

        $result = $this->leadService->all($limit);
        $this->assertEquals($expectedResult, $result);
    }


    public function test_it_should_create_lead_with_minimal_required_data()
    {
        $data = [
            'name' => 'John Doe',
            'phone' => '5511999999999',
            'email' => 'john@example.com',
        ];

        $expectedResult = array_merge($data, [
            '_id' => '507f1f77bcf86cd799439011',
            'notification_sms' => false,
            'notification_email' => false,
            'notification_whatsapp' => false,
        ]);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->leadService->create($data);
        $this->assertEquals($expectedResult, $result);
    }
} 