<?php

namespace Tests\Unit\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\ProfessionalRepository;
use App\Services\ProfessionalService;
use Tests\TestCase;
use Mockery;

class ProfessionalServiceTest extends TestCase
{
    protected $professionalService;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ProfessionalRepository::class);
        $this->professionalService = new ProfessionalService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_return_all_professionals_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '5511999999999',
                    'active' => true
                ],
                [
                    'id' => 2,
                    'name' => 'Jane Doe',
                    'email' => 'jane@example.com',
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

        $result = $this->professionalService->all($limit);
        
        $this->assertEquals($expectedResult, $result);
    }
} 