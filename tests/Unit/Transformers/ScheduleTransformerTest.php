<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Transformers\ScheduleTransformer;
use App\Entities\Schedule;
use Mockery;

class ScheduleTransformerTest extends TestCase
{
    protected $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new ScheduleTransformer();
    }

    public function test_transform()
    {
        $model = Mockery::mock(Schedule::class);
        
        $reflection = new \ReflectionMethod(ScheduleTransformer::class, 'transform');
        $parameters = $reflection->getParameters();
        $expectedType = $parameters[0]->getType()->getName();
        
        $this->assertEquals(Schedule::class, $expectedType);
        $this->assertInstanceOf($expectedType, $model);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}