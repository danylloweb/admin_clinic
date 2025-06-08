<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\LocationRangeRepositoryEloquent;
use App\Entities\LocationRange;
use App\Validators\LocationRangeValidator;
use App\Presenters\LocationRangePresenter;
use Mockery;
use Illuminate\Container\Container;

class LocationRangeRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new LocationRangeRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(LocationRange::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(LocationRangeValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(LocationRangePresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}