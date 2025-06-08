<?php

namespace Tests\Unit\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\NetPromoterScoreRepository;
use App\Services\NetPromoterScoreService;
use Tests\TestCase;
use Mockery;
use App\Repositories\ScheduleRepository;

class NetPromoterScoreServiceTest extends TestCase
{
    protected $netPromoterScoreService;
    protected $repository;
    protected $scheduleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(NetPromoterScoreRepository::class);
        $this->scheduleRepository = Mockery::mock(ScheduleRepository::class);
        $this->netPromoterScoreService = new NetPromoterScoreService($this->repository, $this->scheduleRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_return_all_nps_scores_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'score' => 9,
                    'comment' => 'Great service!',
                    'user_id' => 1,
                    'created_at' => '2024-01-01 10:00:00'
                ],
                [
                    'id' => 2,
                    'score' => 8,
                    'comment' => 'Very satisfied',
                    'user_id' => 2,
                    'created_at' => '2024-01-02 11:00:00'
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

        $result = $this->netPromoterScoreService->all($limit);
        
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_create_nps_score_successfully()
    {
        $scheduleData = (object)[
            'id' => 1,
            'order_id' => 100,
            'provider_id' => 50,
            'status' => 'completed'
        ];

        $inputData = [
            'schedule_id' => 1,
            'rating' => 9,
            'comment' => 'Excellent service!'
        ];

        $expectedCreateData = [
            'order_id' => 100,
            'schedule_id' => 1,
            'provider_id' => 50,
            'rating' => 9,
            'comment' => 'Excellent service!'
        ];

        $expectedResult = [
            'id' => 1,
            'order_id' => 100,
            'schedule_id' => 1,
            'provider_id' => 50,
            'rating' => 9,
            'comment' => 'Excellent service!'
        ];

        $this->scheduleRepository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->scheduleRepository);

        $this->scheduleRepository->shouldReceive('findWhere')
            ->once()
            ->with([
                'id' => 1,
                'status' => 6 // ScheduleStatusEnum::COMPLETED->value
            ])
            ->andReturn(collect([$scheduleData]));

        $this->repository->shouldReceive('create')
            ->once()
            ->with($expectedCreateData)
            ->andReturn($expectedResult);

        $result = $this->netPromoterScoreService->create($inputData);
        
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_not_create_nps_score_when_schedule_not_completed()
    {
        $inputData = [
            'schedule_id' => 1,
            'rating' => 9,
            'comment' => 'Excellent service!'
        ];

        $expectedResult = ['message' => 'Agendamento não pode ser avaliado.'];

        $this->scheduleRepository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->scheduleRepository);

        $this->scheduleRepository->shouldReceive('findWhere')
            ->once()
            ->with([
                'id' => 1,
                'status' => 6 // ScheduleStatusEnum::COMPLETED->value
            ])
            ->andReturn(collect([]));

        $result = $this->netPromoterScoreService->create($inputData);
        
        $this->assertEquals($expectedResult, $result);
    }
} 