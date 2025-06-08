<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\OrderRepositoryEloquent;
use App\Entities\Order;
use App\Validators\OrderValidator;
use App\Presenters\OrderPresenter;
use Mockery;
use Illuminate\Container\Container;

class OrderRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new OrderRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(Order::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(OrderValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(OrderPresenter::class, $result);
    }

    public function test_modify_order_status_by_item_status()
    {
        // Create a mock Order with items collection
        $order = Mockery::mock(Order::class)->makePartial();
        $items = collect([
            (object)['status' => 3],
            (object)['status' => 3]
        ]);
        
        $order->items = $items;
        $order->shouldReceive('save')->once()->andReturn(true);
        
        // Mock the repository methods
        $repository = Mockery::mock(OrderRepositoryEloquent::class)->makePartial();
        $repository->shouldReceive('skipPresenter')->andReturn($repository);
        $repository->shouldReceive('with')->with('items')->andReturn($repository);
        $repository->shouldReceive('find')->with(1, ['id', 'status'])->andReturn($order);
        
        // Execute the method
        $repository->modifyOrderStatusByItemStatus(1, 3, 3);
        
        // Assert the status was set correctly
        $this->assertEquals(3, $order->status);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}