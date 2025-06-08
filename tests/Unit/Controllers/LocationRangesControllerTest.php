<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\LocationRangesController;
use App\Services\LocationRangeService;
use Mockery;
use Illuminate\Http\Request;
use App\Validators\LocationRangeValidator;

class LocationRangesControllerTest extends TestCase
{
    protected $controller;
    protected $service;
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(LocationRangeService::class);
        $this->validator = Mockery::mock(LocationRangeValidator::class);
        $this->controller = new LocationRangesController($this->service, $this->validator);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(LocationRangesController::class, $this->controller);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}