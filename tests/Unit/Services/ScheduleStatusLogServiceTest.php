<?php

namespace Tests\Unit\Services;

use App\Criterias\AppRequestCriteria;
use App\Enums\ScheduleStatusEnum;
use App\Repositories\ScheduleStatusLogRepository;
use App\Services\ScheduleStatusLogService;
use Tests\TestCase;
use Mockery;

class ScheduleStatusLogServiceTest extends TestCase
{
    protected $scheduleStatusLogService;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ScheduleStatusLogRepository::class);
        $this->scheduleStatusLogService = new ScheduleStatusLogService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_create_status_log()
    {
        $data = [
            'schedule_id' => 1,
            'status' => ScheduleStatusEnum::CONFIRMED->value,
            'author' => 'system',
            'log' => 'Schedule confirmed automatically',
            'metadata' => json_encode(['source' => 'automated_check'])
        ];

        $expectedResult = array_merge($data, ['id' => 1]);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->scheduleStatusLogService->create($data);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_return_all_status_logs_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'schedule_id' => 1,
                    'status' => ScheduleStatusEnum::SCHEDULING_PENDING->value,
                    'author' => 'system',
                    'log' => 'Schedule created',
                    'created_at' => '2024-03-01 10:00:00'
                ],
                [
                    'id' => 2,
                    'schedule_id' => 1,
                    'status' => ScheduleStatusEnum::CONFIRMED->value,
                    'author' => 'admin',
                    'log' => 'Schedule confirmed by admin',
                    'created_at' => '2024-03-01 10:15:00'
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
            ->withAnyArgs()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('paginate')
            ->once()
            ->with($limit)
            ->andReturn($expectedResult);

        $result = $this->scheduleStatusLogService->all($limit);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_status_log_by_id()
    {
        $id = 1;
        $expectedResult = [
            'id' => $id,
            'schedule_id' => 1,
            'status' => ScheduleStatusEnum::CONFIRMED->value,
            'author' => 'system',
            'log' => 'Schedule confirmed'
        ];

        $this->repository->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->scheduleStatusLogService->find($id);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_logs_by_schedule()
    {
        $scheduleId = 1;
        $expectedResult = [
            [
                'id' => 1,
                'schedule_id' => $scheduleId,
                'status' => ScheduleStatusEnum::SCHEDULING_PENDING->value,
                'author' => 'system',
                'log' => 'Schedule created'
            ],
            [
                'id' => 2,
                'schedule_id' => $scheduleId,
                'status' => ScheduleStatusEnum::CONFIRMED->value,
                'author' => 'admin',
                'log' => 'Schedule confirmed'
            ]
        ];

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with(['schedule_id' => $scheduleId])
            ->andReturn($expectedResult);

        $result = $this->scheduleStatusLogService->findWhere(['schedule_id' => $scheduleId]);
        $this->assertEquals($expectedResult, $result);
    }


    public function test_it_should_create_log_with_minimal_required_data()
    {
        $data = [
            'schedule_id' => 1,
            'status' => ScheduleStatusEnum::CONFIRMED->value,
            'author' => 'system'
        ];

        $expectedResult = array_merge($data, [
            'id' => 1,
            'log' => null,
            'metadata' => null
        ]);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->scheduleStatusLogService->create($data);
        $this->assertEquals($expectedResult, $result);
    }
} 