<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\ScheduleRepositoryEloquent;
use App\Entities\Schedule;
use App\Validators\ScheduleValidator;
use App\Presenters\SchedulePresenter;
use Mockery;
use Illuminate\Container\Container;

class ScheduleRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new ScheduleRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(Schedule::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(ScheduleValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(SchedulePresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}