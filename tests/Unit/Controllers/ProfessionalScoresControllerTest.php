<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\ProfessionalScoresController;
use App\Services\ProfessionalScoresService;
use Mockery;
use Illuminate\Http\Request;
use App\Validators\ProfessionalScoresValidator;

class ProfessionalScoresControllerTest extends TestCase
{
    protected $controller;
    protected $service;
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(ProfessionalScoresService::class);
        $this->validator = Mockery::mock(ProfessionalScoresValidator::class);
        $this->controller = new ProfessionalScoresController($this->service, $this->validator);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(ProfessionalScoresController::class, $this->controller);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}