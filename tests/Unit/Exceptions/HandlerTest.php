<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\Handler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;

class HandlerTest extends TestCase
{
    private Handler $handler;
    private $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(Handler::class);
        $this->request = new \Illuminate\Http\Request();
    }

    public function testRenderValidationException()
    {
        // Create a mock validator
        $validator = $this->createMock(Validator::class);
        $messageBag = new MessageBag(['field' => ['Error message']]);
        
        $validator->expects($this->any())
            ->method('getMessageBag')
            ->willReturn($messageBag);

        // Mock the errors() method
        $validator->expects($this->any())
            ->method('errors')
            ->willReturn($messageBag);

        // Create ValidationException
        $exception = new ValidationException($validator);

        // Call render method
        $response = $this->handler->render($this->request, $exception);

        // Assert response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        
        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['error']);
        $this->assertEquals('Error message', $content['message']);
        $this->assertEquals(['Error message'], $content['errors']);
    }

    public function testRenderValidatorException()
    {
        // Create message bag
        $messageBag = new MessageBag(['field' => ['Error message']]);
        
        // Create ValidatorException
        $exception = $this->createMock(ValidatorException::class);
        $exception->expects($this->any())
            ->method('getMessageBag')
            ->willReturn($messageBag);

        // Call render method with request instance
        $response = $this->handler->render($this->request, $exception);

        // Assert response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        
        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['error']);
        $this->assertEquals('Error message', $content['message']);
        $this->assertEquals(['Error message'], $content['errors']);
    }

    public function testUnauthenticated()
    {
        // Create AuthenticationException
        $exception = new AuthenticationException();

        // Call unauthenticated method with request instance
        $response = $this->handler->unauthenticated($this->request, $exception);

        // Assert response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        
        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['error']);
        $this->assertEquals('unauthenticated', $content['message']);
    }

    public function testParseMessages()
    {
        $messageBag = new MessageBag([
            'field1' => ['Error 1'],
            'field2' => ['Error 2']
        ]);

        $method = new \ReflectionMethod(Handler::class, 'parseMessages');
        $method->setAccessible(true);

        $result = $method->invoke($this->handler, $messageBag);

        $this->assertEquals(['Error 1', 'Error 2'], $result);
    }
} 