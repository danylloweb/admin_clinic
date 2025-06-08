<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\ProfessionalRatingsController;
use App\Services\ProfessionalRatingService;
use Mockery;
use Illuminate\Http\Request;
use App\Validators\ProfessionalRatingValidator;

class ProfessionalRatingsControllerTest extends TestCase
{
    protected $controller;
    protected $service;
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(ProfessionalRatingService::class);
        $this->validator = Mockery::mock(ProfessionalRatingValidator::class);
        $this->controller = new ProfessionalRatingsController($this->service, $this->validator);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(ProfessionalRatingsController::class, $this->controller);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}