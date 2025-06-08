<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Transformers\ServiceAvailabilityLocationTransformer;
use App\Entities\ServiceAvailabilityLocation;
use Mockery;

class ServiceAvailabilityLocationTransformerTest extends TestCase
{
    protected $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new ServiceAvailabilityLocationTransformer();
    }

    public function test_transform()
    {
        $model = Mockery::mock(ServiceAvailabilityLocation::class);
        // Configure o mock para retornar valores de teste
        $model->shouldReceive('getAttribute')->andReturnUsing(function ($attribute) {
            return match ($attribute) {
                'id' => 1,
                'created_at', 'updated_at' => now(),
                default => 'test_value',
            };
        });

        $result = $this->transformer->transform($model);

        $this->assertIsArray($result);
        // Adicione mais asserções conforme necessário para validar o formato de saída
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}