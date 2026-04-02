<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\ApiTestCase;

class GetHistoryTest extends ApiTestCase
{
    public function testReturnsDataForValidDateRange(): void
    {
        $this->seedSensorData([
            ['value' => 1.5, 'recorded_at' => '2026-01-01 10:00:00'],
            ['value' => 2.3, 'recorded_at' => '2026-01-01 12:00:00'],
            ['value' => 1.8, 'recorded_at' => '2026-01-01 14:00:00'],
        ]);

        $response = $this->get('/api/pegel/history', [
            'from' => '2026-01-01T09:00:00Z',
            'to' => '2026-01-01T15:00:00Z',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        $body = $this->getResponseBody($response);

        $this->assertArrayHasKey('data', $body);
        $this->assertCount(3, $body['data']);

        $this->assertEquals(1.5, $body['data'][0]['value']);
        $this->assertEquals(2.3, $body['data'][1]['value']);
        $this->assertEquals(1.8, $body['data'][2]['value']);
    }

    public function testResponseDoesNotExceedMaxItemsLimit(): void
    {
        $records = [];
        for ($i = 0; $i < 2600; $i++) {
            $records[] = [
                'value' => 1.0 + ($i * 0.001),
                'recorded_at' => sprintf('2026-01-01 %02d:%02d:%02d', (int)($i / 3600), (int)(($i % 3600) / 60), $i % 60),
            ];
        }
        $this->seedSensorData($records);

        $response = $this->get('/api/pegel/history', [
            'from' => '2026-01-01T00:00:00Z',
            'to' => '2026-01-02T00:00:00Z',
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $body = $this->getResponseBody($response);

        $this->assertArrayHasKey('data', $body);
        $this->assertLessThanOrEqual(2500, count($body['data']));
    }

    public function testReturns400WhenFromParameterMissing(): void
    {
        $response = $this->get('/api/pegel/history', [
            'to' => '2026-01-01T15:00:00Z',
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testReturns400WhenToParameterMissing(): void
    {
        $response = $this->get('/api/pegel/history', [
            'from' => '2026-01-01T09:00:00Z',
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testReturns400ForInvalidDateFormat(): void
    {
        $response = $this->get('/api/pegel/history', [
            'from' => '2026-01-01',
            'to' => '2026-01-02',
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testReturns400WhenFromIsAfterTo(): void
    {
        $response = $this->get('/api/pegel/history', [
            'from' => '2026-01-02T00:00:00Z',
            'to' => '2026-01-01T00:00:00Z',
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testReturnsEmptyArrayWhenNoDataInRange(): void
    {
        $this->seedSensorData([
            ['value' => 1.5, 'recorded_at' => '2026-01-01 10:00:00'],
        ]);

        $response = $this->get('/api/pegel/history', [
            'from' => '2026-02-01T00:00:00Z',
            'to' => '2026-02-02T00:00:00Z',
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $body = $this->getResponseBody($response);

        $this->assertArrayHasKey('data', $body);
        $this->assertCount(0, $body['data']);
    }
}
