<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Transformers\OrderTransformer;
use App\Entities\Order;
use Mockery;

class OrderTransformerTest extends TestCase
{
    protected $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new OrderTransformer();
    }

    public function test_transform()
    {
        $model = Mockery::mock(Order::class);
        
        $reflection = new \ReflectionMethod(OrderTransformer::class, 'transform');
        $parameters = $reflection->getParameters();
        $expectedType = $parameters[0]->getType()->getName();
        
        $this->assertEquals(Order::class, $expectedType);
        $this->assertInstanceOf($expectedType, $model);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}