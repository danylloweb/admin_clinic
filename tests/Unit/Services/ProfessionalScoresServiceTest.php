<?php

namespace Tests\Unit\Services;

use App\Repositories\ProfessionalScoresRepository;
use App\Services\ProfessionalScoresService;
use Tests\TestCase;
use Mockery;

class ProfessionalScoresServiceTest extends TestCase
{
    protected $professionalScoresService;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ProfessionalScoresRepository::class);
        $this->professionalScoresService = new ProfessionalScoresService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_create_professional_score()
    {
        $data = [
            'professional_id' => 1,
            'provider_id' => 1,
            'score' => json_encode([
                'technical' => 4.5,
                'punctuality' => 5.0,
                'communication' => 4.8,
                'overall' => 4.8
            ])
        ];

        $expectedResult = array_merge($data, ['id' => 1]);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->professionalScoresService->create($data);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_return_all_scores_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'professional_id' => 1,
                    'provider_id' => 1,
                    'score' => json_encode([
                        'technical' => 4.5,
                        'punctuality' => 5.0,
                        'overall' => 4.8
                    ])
                ],
                [
                    'id' => 2,
                    'professional_id' => 2,
                    'provider_id' => 1,
                    'score' => json_encode([
                        'technical' => 4.0,
                        'punctuality' => 4.5,
                        'overall' => 4.3
                    ])
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
            ->andReturn($this->repository);

        $this->repository->shouldReceive('paginate')
            ->once()
            ->with($limit)
            ->andReturn($expectedResult);

        $result = $this->professionalScoresService->all($limit);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_create_score_with_minimal_required_data()
    {
        $data = [
            'professional_id' => 1,
            'provider_id' => 1,
            'score' => json_encode(['overall' => 4.5])
        ];

        $expectedResult = array_merge($data, ['id' => 1]);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->professionalScoresService->create($data);
        $this->assertEquals($expectedResult, $result);
    }
} 