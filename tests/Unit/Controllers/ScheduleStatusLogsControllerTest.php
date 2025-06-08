<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\ScheduleStatusLogsController;
use App\Repositories\ScheduleStatusLogRepository;
use Mockery;
use Illuminate\Http\Request;
use App\Validators\ScheduleStatusLogValidator;

class ScheduleStatusLogsControllerTest extends TestCase
{
    protected $controller;
    protected $repository;
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ScheduleStatusLogRepository::class);
        $this->validator = Mockery::mock(ScheduleStatusLogValidator::class);
        $this->controller = new ScheduleStatusLogsController($this->repository, $this->validator);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(ScheduleStatusLogsController::class, $this->controller);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}