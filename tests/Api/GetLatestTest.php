<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\ApiTestCase;

class GetLatestTest extends ApiTestCase
{
    public function testReturnsLatestValueWhenDataExists(): void
    {
        $this->seedSensorData([
            ['value' => 1.5, 'recorded_at' => '2026-01-01 10:00:00'],
            ['value' => 2.3, 'recorded_at' => '2026-01-01 12:00:00'],
            ['value' => 1.8, 'recorded_at' => '2026-01-01 11:00:00'],
        ]);

        $response = $this->get('/api/pegel');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        $body = $this->getResponseBody($response);

        $this->assertArrayHasKey('data', $body);
        $this->assertEquals(2.3, $body['data']['value']);
        $this->assertEquals('2026-01-01 12:00:00', $body['data']['recorded_at']);
    }

    public function testReturns404WhenNoDataExists(): void
    {
        $response = $this->get('/api/pegel');

        $this->assertEquals(404, $response->getStatusCode());
    }
}
