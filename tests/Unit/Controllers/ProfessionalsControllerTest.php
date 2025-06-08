<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\ProfessionalsController;
use App\Services\ProfessionalService;
use Mockery;
use Illuminate\Http\Request;
use App\Validators\ProfessionalValidator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use App\Exceptions\RepositoryException;

class ProfessionalsControllerTest extends TestCase
{
    private const DEFAULT_PER_PAGE = 15;
    
    protected $controller;
    protected $service;
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(ProfessionalService::class);
        $this->validator = Mockery::mock(ProfessionalValidator::class);
        $this->controller = new ProfessionalsController($this->service, $this->validator);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(ProfessionalsController::class, $this->controller);
    }

    public function test_index_returns_cached_professionals(): void
    {
        $paginatedData = [
            'data' => [
                ['id' => 1, 'name' => 'Dr. Smith'],
                ['id' => 2, 'name' => 'Dr. Jones']
            ],
            'total' => 2,
            'per_page' => self::DEFAULT_PER_PAGE,
            'current_page' => 1,
            'last_page' => 1,
            'next_page_url' => null,
            'prev_page_url' => null,
            'from' => 1,
            'to' => 2
        ];

        $request = Mockery::mock(Request::class);
        $request->query = Mockery::mock();
        $request->shouldReceive('fullUrl')
            ->once()
            ->andReturn('http://example.com/professionals');
        
        $request->query->shouldReceive('get')
            ->with('limit', self::DEFAULT_PER_PAGE)
            ->once()
            ->andReturn(self::DEFAULT_PER_PAGE);

        $cacheMock = Mockery::mock('cache');
        Cache::shouldReceive('store')
            ->with('redis')
            ->once()
            ->andReturn($cacheMock);
            
        $cacheMock->shouldReceive('tags')
            ->with('professionals')
            ->once()
            ->andReturnSelf();
            
        $cacheMock->shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->service
            ->shouldReceive('all')
            ->with(self::DEFAULT_PER_PAGE)
            ->andReturn($paginatedData);

        $response = $this->controller->index($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($paginatedData, json_decode($response->getContent(), true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}