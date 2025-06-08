<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\ProvidersController;
use App\Services\ProviderService;
use Mockery;
use Illuminate\Http\Request;
use App\Validators\ProviderValidator;

class ProvidersControllerTest extends TestCase
{
    protected $controller;
    protected $service;
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(ProviderService::class);
        $this->validator = Mockery::mock(ProviderValidator::class);
        $this->controller = new ProvidersController($this->service, $this->validator);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(ProvidersController::class, $this->controller);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}