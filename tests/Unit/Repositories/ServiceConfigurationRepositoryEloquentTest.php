<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\ServiceConfigurationRepositoryEloquent;
use App\Entities\ServiceConfiguration;
use App\Validators\ServiceConfigurationValidator;
use App\Presenters\ServiceConfigurationPresenter;
use Mockery;
use Illuminate\Container\Container;

class ServiceConfigurationRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new ServiceConfigurationRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(ServiceConfiguration::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(ServiceConfigurationValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(ServiceConfigurationPresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}