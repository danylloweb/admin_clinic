<?php

namespace Tests\Unit\Services;

use App\Repositories\ProfessionalRatingRepository;
use App\Services\ProfessionalRatingService;
use Tests\TestCase;
use Mockery;
use App\Repositories\ScheduleRepository;
use App\Enums\ScheduleStatusEnum;
use App\Entities\Schedule;
use Illuminate\Database\Eloquent\Collection;

class ProfessionalRatingServiceTest extends TestCase
{
    protected $professionalRatingService;
    protected $repository;
    protected $scheduleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ProfessionalRatingRepository::class);
        $this->scheduleRepository = Mockery::mock(ScheduleRepository::class);
        $this->professionalRatingService = new ProfessionalRatingService($this->repository, $this->scheduleRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_all_should_return_paginated_results(): void
    {
        $expectedResult = ['paginated_data'];
        $limit = 20;

        $this->repository->shouldReceive('resetCriteria')->once()->andReturnSelf();
        $this->repository->shouldReceive('pushCriteria')->times(3)->andReturnSelf();
        $this->repository->shouldReceive('paginate')->once()->with($limit)->andReturn($expectedResult);

        $result = $this->professionalRatingService->all($limit);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_create_should_return_error_message_when_schedule_not_found(): void
    {
        $data = ['schedule_id' => 1, 'stars' => 5, 'comment' => 'Great service'];
        
        $this->scheduleRepository->shouldReceive('skipPresenter')->once()->andReturnSelf();
        $this->scheduleRepository->shouldReceive('findWhere')
            ->once()
            ->with([
                'id' => 1,
                'status' => ScheduleStatusEnum::COMPLETED->value
            ])
            ->andReturn(new Collection());

        $result = $this->professionalRatingService->create($data);

        $this->assertEquals(['message' => 'Agendamento não pode ser avaliado.'], $result);
    }

    public function test_create_should_create_rating_when_schedule_is_valid(): void
    {
        $scheduleId = 1;
        $inputData = [
            'schedule_id' => $scheduleId,
            'stars' => 5,
            'comment' => 'Excellent service'
        ];

        $schedule = new Schedule();
        $schedule->professional_id = 10;
        $schedule->customer_id = 20;
        $schedule->order_id = 30;
        $schedule->order_item_id = 40;
        $schedule->provider_id = 50;

        $expectedCreateData = [
            'professional_id' => 10,
            'customer_id' => 20,
            'order_id' => 30,
            'order_item_id' => 40,
            'schedule_id' => $scheduleId,
            'provider_id' => 50,
            'stars' => 5,
            'comment' => 'Excellent service'
        ];

        $expectedResult = ['rating_data'];

        $this->scheduleRepository->shouldReceive('skipPresenter')->once()->andReturnSelf();
        $this->scheduleRepository->shouldReceive('findWhere')
            ->once()
            ->with([
                'id' => $scheduleId,
                'status' => ScheduleStatusEnum::COMPLETED->value
            ])
            ->andReturn(new Collection([$schedule]));

        $this->repository->shouldReceive('create')
            ->once()
            ->with($expectedCreateData)
            ->andReturn($expectedResult);

        $result = $this->professionalRatingService->create($inputData);

        $this->assertEquals($expectedResult, $result);
    }
} 