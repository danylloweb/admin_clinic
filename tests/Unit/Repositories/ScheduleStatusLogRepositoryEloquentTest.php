<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\ScheduleStatusLogRepositoryEloquent;
use App\Entities\ScheduleStatusLog;
use App\Validators\ScheduleStatusLogValidator;
use App\Presenters\ScheduleStatusLogPresenter;
use Mockery;
use Illuminate\Container\Container;

class ScheduleStatusLogRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new ScheduleStatusLogRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(ScheduleStatusLog::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(ScheduleStatusLogValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(ScheduleStatusLogPresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}