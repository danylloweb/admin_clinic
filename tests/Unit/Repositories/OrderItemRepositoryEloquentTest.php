<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\OrderItemRepositoryEloquent;
use App\Entities\OrderItem;
use App\Validators\OrderItemValidator;
use App\Presenters\OrderItemPresenter;
use Mockery;
use Illuminate\Container\Container;

class OrderItemRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new OrderItemRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(OrderItem::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(OrderItemValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(OrderItemPresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}