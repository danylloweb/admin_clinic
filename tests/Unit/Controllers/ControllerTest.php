<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\Controller;
use App\Services\Service;
use Mockery;
use Illuminate\Http\Request;
use Exception;

class ControllerTest extends TestCase
{
    protected $controller;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(Service::class);
        $this->controller = new Controller();
        $this->controller->setService($this->service);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $controller = new Controller();
        $this->assertInstanceOf(Controller::class, $controller);
    }

    public function test_index_returns_all_records(): void
    {
        $expectedData = ['data1', 'data2'];
        
        $mockService = \Mockery::mock(Service::class);
        $mockService->shouldReceive('all')
            ->once()
            ->with(15)
            ->andReturn($expectedData);

        $controller = new Controller();
        $controller->setService($mockService);

        $request = new Request();
        $response = $controller->index($request);

        $this->assertEquals($expectedData, $response->getData(true));
        $this->assertEquals(200, $response->status());
    }

    public function test_index_handles_exception(): void
    {
        $this->service->shouldReceive('all')
            ->once()
            ->andThrow(new Exception('Error message'));

        $request = new Request();
        $response = $this->controller->index($request);

        $this->assertEquals(['error' => 'true', 'message' => 'Error message'], $response->getData(true));
        $this->assertEquals(422, $response->status());
    }

    public function test_show_returns_specific_record(): void
    {
        $expectedData = ['id' => 1, 'name' => 'Test'];
        $this->service->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($expectedData);

        $response = $this->controller->show(1);

        $this->assertEquals($expectedData, $response->getData(true));
        $this->assertEquals(200, $response->status());
    }

    public function test_show_handles_exception(): void
    {
        $this->service->shouldReceive('find')
            ->once()
            ->andThrow(new Exception('Record not found'));

        $response = $this->controller->show(1);

        $this->assertEquals(['error' => 'true', 'message' => 'Record not found'], $response->getData(true));
        $this->assertEquals(404, $response->status());
    }

    public function test_store_creates_new_record(): void
    {
        $inputData = ['name' => 'Test'];
        $expectedData = ['id' => 1, 'name' => 'Test'];
        
        $this->service->shouldReceive('create')
            ->once()
            ->with($inputData)
            ->andReturn($expectedData);

        $request = new Request();
        $request->merge($inputData);
        
        $response = $this->controller->store($request);

        $this->assertEquals($expectedData, $response->getData(true));
        $this->assertEquals(200, $response->status());
    }

    public function test_store_handles_exception(): void
    {
        $inputData = ['name' => 'Test'];
        
        $this->service->shouldReceive('create')
            ->once()
            ->andThrow(new Exception('Validation failed'));

        $request = new Request();
        $request->merge($inputData);
        
        $response = $this->controller->store($request);

        $this->assertEquals(['error' => 'true', 'message' => 'Validation failed'], $response->getData(true));
        $this->assertEquals(422, $response->status());
    }

    public function test_update_modifies_existing_record(): void
    {
        $inputData = ['name' => 'Updated'];
        $expectedData = ['id' => 1, 'name' => 'Updated'];
        
        $this->service->shouldReceive('update')
            ->once()
            ->with($inputData, 1)
            ->andReturn($expectedData);

        $request = new Request();
        $request->merge($inputData);
        
        $response = $this->controller->update($request, 1);

        $this->assertEquals($expectedData, $response->getData(true));
        $this->assertEquals(200, $response->status());
    }

    public function test_update_handles_exception(): void
    {
        $inputData = ['name' => 'Updated'];
        
        $this->service->shouldReceive('update')
            ->once()
            ->andThrow(new Exception('Update failed'));

        $request = new Request();
        $request->merge($inputData);
        
        $response = $this->controller->update($request, 1);

        $this->assertEquals(['error' => 'true', 'message' => 'Update failed'], $response->getData(true));
        $this->assertEquals(422, $response->status());
    }

    public function test_restore_recovers_deleted_record(): void
    {
        $expectedData = ['id' => 1, 'name' => 'Restored'];
        
        $this->service->shouldReceive('restore')
            ->once()
            ->with(1)
            ->andReturn($expectedData);

        $response = $this->controller->restore(1);

        $this->assertEquals($expectedData, $response->getData(true));
        $this->assertEquals(200, $response->status());
    }

    public function test_restore_handles_exception(): void
    {
        $this->service->shouldReceive('restore')
            ->once()
            ->andThrow(new Exception('Restore failed'));

        $response = $this->controller->restore(1);

        $this->assertEquals(['error' => 'true', 'message' => 'Restore failed'], $response->getData(true));
        $this->assertEquals(404, $response->status());
    }

    public function test_destroy_removes_record(): void
    {
        $expectedData = ['success' => true];
        
        $this->service->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn($expectedData);

        $response = $this->controller->destroy(1);

        $this->assertEquals($expectedData, $response->getData(true));
        $this->assertEquals(200, $response->status());
    }

    public function test_destroy_handles_exception(): void
    {
        $this->service->shouldReceive('delete')
            ->once()
            ->andThrow(new Exception('Delete failed'));

        $response = $this->controller->destroy(1);

        $this->assertEquals(['error' => 'true', 'message' => 'Delete failed'], $response->getData(true));
        $this->assertEquals(404, $response->status());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}