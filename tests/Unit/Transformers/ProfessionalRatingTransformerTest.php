<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Transformers\ProfessionalRatingTransformer;
use App\Entities\ProfessionalRating;
use Mockery;

class ProfessionalRatingTransformerTest extends TestCase
{
    protected $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new ProfessionalRatingTransformer();
    }

    public function test_transform()
    {
        $model = Mockery::mock(ProfessionalRating::class);
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