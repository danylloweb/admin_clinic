<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\PricingRepositoryEloquent;
use App\Entities\Pricing;
use App\Validators\PricingValidator;
use App\Presenters\PricingPresenter;
use Mockery;
use Illuminate\Container\Container;

class PricingRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new PricingRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(Pricing::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(PricingValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(PricingPresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}