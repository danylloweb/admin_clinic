<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Transformers\OrderItemScheduleTransformer;
use App\Entities\OrderItem;
use Mockery;

class OrderItemScheduleTransformerTest extends TestCase
{
    protected $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new OrderItemScheduleTransformer();
    }

    public function test_transform()
    {
        $model = Mockery::mock(OrderItem::class);
        
        $reflection = new \ReflectionMethod(OrderItemScheduleTransformer::class, 'transform');
        $parameters = $reflection->getParameters();
        $expectedType = $parameters[0]->getType()->getName();
        
        $this->assertEquals(OrderItem::class, $expectedType);
        $this->assertInstanceOf($expectedType, $model);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}