<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\ApiTestCase;

class PostValueTest extends ApiTestCase
{
    private function getApiKey(): string
    {
        return $_ENV['API_KEY'] ?? getenv('API_KEY') ?: '';
    }

    public function testCreatesValueWithValidApiKey(): void
    {
        $response = $this->post('/api/pegel', [
            'value' => 2.5,
            'recorded_at' => '2026-01-01T12:00:00Z',
        ], [
            'X-API-Key' => $this->getApiKey(),
        ]);

        $this->assertEquals(201, $response->getStatusCode());

        $stmt = $this->pdo->query('SELECT value, recorded_at FROM sensor_data WHERE sensor_id = 1');
        $record = $stmt->fetch();

        $this->assertEquals(2.5, (float)$record['value']);
        $this->assertEquals('2026-01-01 12:00:00', $record['recorded_at']);
    }

    public function testReturns401WhenApiKeyMissing(): void
    {
        $response = $this->post('/api/pegel', [
            'value' => 2.5,
            'recorded_at' => '2026-01-01T12:00:00Z',
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testReturns401WhenApiKeyInvalid(): void
    {
        $response = $this->post('/api/pegel', [
            'value' => 2.5,
            'recorded_at' => '2026-01-01T12:00:00Z',
        ], [
            'X-API-Key' => 'wrong-api-key',
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testReturns400WhenValueMissing(): void
    {
        $response = $this->post('/api/pegel', [
            'recorded_at' => '2026-01-01T12:00:00Z',
        ], [
            'X-API-Key' => $this->getApiKey(),
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testReturns400WhenRecordedAtMissing(): void
    {
        $response = $this->post('/api/pegel', [
            'value' => 2.5,
        ], [
            'X-API-Key' => $this->getApiKey(),
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testReturns400WhenValueNotNumeric(): void
    {
        $response = $this->post('/api/pegel', [
            'value' => 'not-a-number',
            'recorded_at' => '2026-01-01T12:00:00Z',
        ], [
            'X-API-Key' => $this->getApiKey(),
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testReturns400WhenValueOutOfRange(): void
    {
        $response = $this->post('/api/pegel', [
            'value' => 10.0,
            'recorded_at' => '2026-01-01T12:00:00Z',
        ], [
            'X-API-Key' => $this->getApiKey(),
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testReturns400WhenRecordedAtInvalidFormat(): void
    {
        $response = $this->post('/api/pegel', [
            'value' => 2.5,
            'recorded_at' => '2026-01-01',
        ], [
            'X-API-Key' => $this->getApiKey(),
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }
}
