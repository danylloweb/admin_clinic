<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\SchedulesController;
use App\Services\ScheduleService;
use Mockery;
use Illuminate\Http\Request;
use App\Validators\ScheduleValidator;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ScheduleAssignmentRequest;
use App\Http\Requests\ScheduleHasRequest;

class SchedulesControllerTest extends TestCase
{
    protected $controller;
    protected $service;
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(ScheduleService::class);
        $this->validator = Mockery::mock(ScheduleValidator::class);
        $this->controller = new SchedulesController($this->service, $this->validator);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(SchedulesController::class, $this->controller);
    }

    public function test_select_professional_returns_json_response(): void
    {
        $requestData = ['professional_id' => 1, 'schedule_id' => 1];
        $expectedResponse = ['status' => 'success'];
        
        $request = Mockery::mock(ScheduleAssignmentRequest::class);
        $request->shouldReceive('all')->once()->andReturn($requestData);
        
        $this->service->shouldReceive('selectProfessional')
            ->once()
            ->with($requestData)
            ->andReturn($expectedResponse);
            
        $response = $this->controller->selectProfessional($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function test_confirm_scheduling_returns_json_response(): void
    {
        $requestData = ['schedule_id' => 1];
        $expectedResponse = ['status' => 'confirmed'];
        
        $request = Mockery::mock(ScheduleHasRequest::class);
        $request->shouldReceive('all')->once()->andReturn($requestData);
        
        $this->service->shouldReceive('comfirmScheduling')
            ->once()
            ->with($requestData)
            ->andReturn($expectedResponse);
            
        $response = $this->controller->comfirmScheduling($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function test_in_progress_scheduling_returns_json_response(): void
    {
        $requestData = ['schedule_id' => 1];
        $expectedResponse = ['status' => 'in_progress'];
        
        $request = Mockery::mock(ScheduleHasRequest::class);
        $request->shouldReceive('all')->once()->andReturn($requestData);
        
        $this->service->shouldReceive('inProgressScheduling')
            ->once()
            ->with($requestData)
            ->andReturn($expectedResponse);
            
        $response = $this->controller->inProgressScheduling($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function test_complete_scheduling_returns_json_response(): void
    {
        $requestData = ['schedule_id' => 1];
        $expectedResponse = ['status' => 'completed'];
        
        $request = Mockery::mock(ScheduleHasRequest::class);
        $request->shouldReceive('all')->once()->andReturn($requestData);
        
        $this->service->shouldReceive('completeScheduling')
            ->once()
            ->with($requestData)
            ->andReturn($expectedResponse);
            
        $response = $this->controller->completeScheduling($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function test_cancel_scheduling_returns_json_response(): void
    {
        $requestData = ['schedule_id' => 1];
        $expectedResponse = ['status' => 'cancelled'];
        
        $request = Mockery::mock(ScheduleHasRequest::class);
        $request->shouldReceive('all')->once()->andReturn($requestData);
        
        $this->service->shouldReceive('cancelScheduling')
            ->once()
            ->with($requestData)
            ->andReturn($expectedResponse);
            
        $response = $this->controller->cancelScheduling($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}