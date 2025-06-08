<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\ProviderRepositoryEloquent;
use App\Entities\Provider;
use App\Validators\ProviderValidator;
use App\Presenters\ProviderPresenter;
use Mockery;
use Illuminate\Container\Container;

class ProviderRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new ProviderRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(Provider::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(ProviderValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(ProviderPresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}