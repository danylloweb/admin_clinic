<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\ServiceAvailabilityLocationRepositoryEloquent;
use App\Entities\ServiceAvailabilityLocation;
use App\Validators\ServiceAvailabilityLocationValidator;
use App\Presenters\ServiceAvailabilityLocationPresenter;
use Mockery;
use Illuminate\Container\Container;

class ServiceAvailabilityLocationRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new ServiceAvailabilityLocationRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(ServiceAvailabilityLocation::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(ServiceAvailabilityLocationValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(ServiceAvailabilityLocationPresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}