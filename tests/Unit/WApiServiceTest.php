<?php

namespace Tests\Unit;

use App\Services\WApiService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class WApiServiceTest extends TestCase
{
    public function testSendTextUsesExpectedEndpointAndJsonPayload(): void
    {
        $history = [];
        $service = $this->makeService([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'messageId' => 'abc-123',
                'status' => 'queued',
            ])),
        ], $history);

        $response = $service->sendText('5511999999999', 'Olá, WApi!');

        $this->assertSame('abc-123', $response['messageId']);
        $this->assertSame('queued', $response['status']);
        $this->assertSame(200, $response['status_code']);

        $this->assertCount(1, $history);

        $request = $history[0]['request'];
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('https://wapi-api.intello.com.br/v1/instances/minha-instancia/send/text', (string) $request->getUri());
        $this->assertSame('secret-key', $request->getHeaderLine('x-api-key'));
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));

        $payload = json_decode((string) $request->getBody(), true);
        $this->assertSame([
            'phone' => '5511999999999',
            'text' => 'Olá, WApi!',
        ], $payload);
    }

    public function testSendImageUsesImageUrlInJsonPayload(): void
    {
        $history = [];
        $service = $this->makeService([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'messageId' => 'img-456',
                'status' => 'sent',
            ])),
        ], $history);

        $response = $service->sendImage('5511999999999', 'https://cdn.example.com/photo.jpg');

        $this->assertSame('img-456', $response['messageId']);
        $this->assertSame('sent', $response['status']);
        $this->assertSame(200, $response['status_code']);

        $this->assertCount(1, $history);

        $request = $history[0]['request'];
        $payload = json_decode((string) $request->getBody(), true);

        $this->assertSame([
            'phone' => '5511999999999',
            'image' => 'https://cdn.example.com/photo.jpg',
        ], $payload);
    }

    private function makeService(array $responses, array &$history): WApiService
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $client = new Client([
            'handler' => $stack,
        ]);

        return new WApiService(
            $client,
            'https://wapi-api.intello.com.br',
            'minha-instancia',
            'secret-key'
        );
    }
}
