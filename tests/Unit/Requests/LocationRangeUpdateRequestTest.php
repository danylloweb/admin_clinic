<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\LocationRangeUpdateRequest;
use Illuminate\Validation\Rule;

class LocationRangeUpdateRequestTest extends TestCase
{
    protected $request;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new LocationRangeUpdateRequest;
    }

    public function test_request_can_be_instantiated(): void
    {
        $this->assertInstanceOf(LocationRangeUpdateRequest::class, $this->request);
    }

    public function test_request_has_rules_method(): void
    {
        $rules = $this->request->rules();
        $this->assertIsArray($rules);
    }

    public function test_request_has_authorize_method(): void
    {
        $authorize = $this->request->authorize();
        $this->assertIsBool($authorize);
    }

    public function test_request_has_messages_method(): void
    {
        $hasMessagesMethod = method_exists($this->request, 'messages');
        $this->assertTrue($hasMessagesMethod, 'Request should have messages method');
        
        if ($hasMessagesMethod) {
            $messages = $this->request->messages();
            $this->assertIsArray($messages, 'Messages method should return an array');
        }
    }
}