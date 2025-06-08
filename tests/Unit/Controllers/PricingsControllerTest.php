<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\PricingsController;
use App\Services\PricingService;
use Mockery;
use Illuminate\Http\Request;
use App\Validators\PricingValidator;

class PricingsControllerTest extends TestCase
{
    protected $controller;
    protected $service;
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(PricingService::class);
        $this->validator = Mockery::mock(PricingValidator::class);
        $this->controller = new PricingsController($this->service, $this->validator);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(PricingsController::class, $this->controller);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}