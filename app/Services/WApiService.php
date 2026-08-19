<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WApiService
{
    public function __construct(
        private readonly ?Client $client = null,
        private readonly ?string $baseUrl = null,
        private readonly ?string $instanceName = null,
        private readonly ?string $apiKey = null,
    ) {
    }

    public function sendText(string $phone, string $text): array
    {
        return $this->send('text', [
            'phone' => $this->formatPhone($phone),
            'text' => $text,
        ]);
    }

    public function sendImage(string $phone, string $imageUrl): array
    {
        return $this->send('image', [
            'phone' => $this->formatPhone($phone),
            'image' => $imageUrl,
        ]);
    }

    private function send(string $type, array $payload): array
    {
        try {
            $response = $this->httpClient()->request('POST', $this->endpoint($type), [
                'headers' => $this->headers(),
                'json' => $this->sanitizePayload($payload),
            ]);

            return $this->decodeResponse((string) $response->getBody(), $response->getStatusCode());
        } catch (GuzzleException $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }

    private function httpClient(): Client
    {
        if ($this->client instanceof Client) {
            return $this->client;
        }

        return new Client([
            'base_uri' => rtrim($this->baseUrl(), '/') . '/',
            'timeout' => 30,
        ]);
    }

    private function endpoint(string $type): string
    {
        return sprintf(
            '%s/v1/instances/%s/send/%s',
            rtrim($this->baseUrl(), '/'),
            rawurlencode($this->instanceName()),
            rawurlencode($type)
        );
    }

    private function headers(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-api-key' => $this->apiKey(),
        ];
    }

    private function sanitizePayload(array $payload): array
    {
        return array_filter(
            $payload,
            static fn ($value) => $value !== null && $value !== ''
        );
    }

    private function decodeResponse(string $content, int $statusCode): array
    {
        $decoded = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded + ['status_code' => $statusCode];
        }

        return [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'status_code' => $statusCode,
            'body' => $content,
        ];
    }

    private function baseUrl(): string
    {
        $value = $this->baseUrl ?? (string) config('services.wapi.base_url', 'https://wapi-api.intello.com.br');

        if (trim($value) === '') {
            throw new \InvalidArgumentException('A URL base da WApi não foi configurada.');
        }

        return trim($value);
    }

    private function instanceName(): string
    {
        $value = $this->instanceName ?? (string) config('services.wapi.instance_name', '');

        if (trim($value) === '') {
            throw new \InvalidArgumentException('O nome da instância da WApi não foi configurado.');
        }

        return trim($value);
    }

    private function apiKey(): string
    {
        $value = $this->apiKey ?? (string) config('services.wapi.x_api_key', '');

        if (trim($value) === '') {
            throw new \InvalidArgumentException('A chave x-api-key da WApi não foi configurada.');
        }

        return trim($value);
    }

    private function formatPhone(string $phone): string
    {
        return "55".preg_replace('/[^0-9]/', '', $phone);
    }
}
