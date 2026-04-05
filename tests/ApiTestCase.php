<?php

declare(strict_types=1);

namespace Tests;

use DI\Bridge\Slim\Bridge;
use PDO;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;

abstract class ApiTestCase extends TestCase
{
    protected App $app;
    protected PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $container = require __DIR__ . '/../config/container.php';
        $this->app = Bridge::create($container);

        $displayErrorDetails = true;
        $this->app->addErrorMiddleware($displayErrorDetails, true, true);
        $this->app->addBodyParsingMiddleware();

        (require __DIR__ . '/../src/routes.php')($this->app);

        $this->pdo = $container->get(PDO::class);

        $this->truncateTables();
    }

    protected function tearDown(): void
    {
        $this->truncateTables();
        parent::tearDown();
    }

    protected function truncateTables(): void
    {
        $this->pdo->exec('DELETE FROM sensor_data');
    }

    protected function seedSensorData(array $records): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sensor_data (sensor_id, value, recorded_at) VALUES (:sensor_id, :value, :recorded_at)'
        );

        foreach ($records as $record) {
            $stmt->execute([
                'sensor_id' => $record['sensor_id'] ?? 1,
                'value' => $record['value'],
                'recorded_at' => $record['recorded_at'],
            ]);
        }
    }

    protected function get(string $uri, array $queryParams = [], array $headers = []): ResponseInterface
    {
        if (!empty($queryParams)) {
            $uri .= '?' . http_build_query($queryParams);
        }

        $request = (new ServerRequestFactory())->createServerRequest('GET', $uri);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $this->app->handle($request);
    }

    protected function post(string $uri, array $body = [], array $headers = []): ResponseInterface
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', $uri);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withParsedBody($body);

        return $this->app->handle($request);
    }

    protected function getResponseBody(ResponseInterface $response): array
    {
        $response->getBody()->rewind();
        $content = $response->getBody()->getContents();
        return json_decode($content, true) ?? [];
    }
}
