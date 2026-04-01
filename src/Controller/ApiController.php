<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PegelService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;
use TypeError;

class ApiController
{

    function __construct(
        private PegelService $pegelService
    ) {}


    function getLatest(Request $request, Response $response): Response
    {
        $values = null;
        try {
            $values = $this->pegelService->getLatest();
        } catch (TypeError $e) {
            error_log('no data available for sensor: ' . $e->getMessage());
            throw new HttpNotFoundException($request, 'No data found for the sensor');
        } catch (\Exception $e) {
            throw new HttpInternalServerErrorException($request, 'Database error: ' . $e->getMessage(), $e);
        }
        $result = ['data' => $values];
        $response->getBody()->write(json_encode($result));
        return $response;
    }

    function addValue(Request $request, Response $response): Response
    {
        $payload = $request->getParsedBody();

        // Validate required fields
        if (!isset($payload['value'])) {
            throw new HttpBadRequestException($request, 'Missing required field: value');
        }
        if (!isset($payload['recorded_at'])) {
            throw new HttpBadRequestException($request, 'Missing required field: recorded_at');
        }

        // Validate value is numeric
        if (!is_numeric($payload['value'])) {
            throw new HttpBadRequestException($request, 'Field value must be numeric');
        }

        $value = (float)$payload['value'];

        // Validate value range (0-5 meters for water level)
        if ($value < 0 || $value > 5) {
            throw new HttpBadRequestException($request, 'Field value must be between 0 and 5');
        }

        // Validate recorded_at format (ISO 8601 e.g. 2026-02-26T22:33:57.072Z)
        $recordedAt = $payload['recorded_at'];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?Z$/', $recordedAt)) {
            throw new HttpBadRequestException($request, 'Field recorded_at must be ISO 8601 with Z (e.g. 2026-02-26T22:33:57.072Z)');
        }

        // Parse to DateTime and convert to database format
        try {
            $dateTime = new \DateTime($recordedAt);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, 'Field recorded_at is not a valid datetime');
        }

        $recordedAt = $dateTime->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');

        try {
            $this->pegelService->addValue($value, $recordedAt);
            return $response->withStatus(201);
        } catch (\Exception $e) {
            throw new HttpInternalServerErrorException($request, 'Database error: ' . $e->getMessage());
        }
    }

    function getHistory(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['from'])) {
            throw new HttpBadRequestException($request, 'Missing required query parameter: from');
        }
        if (!isset($queryParams['to'])) {
            throw new HttpBadRequestException($request, 'Missing required query parameter: to');
        }

        $from = $queryParams['from'];
        $to = $queryParams['to'];

        $iso8601Pattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?Z$/';

        if (!preg_match($iso8601Pattern, $from)) {
            throw new HttpBadRequestException($request, 'Parameter from must be ISO 8601 with Z (e.g. 2026-02-26T22:33:57.072Z)');
        }
        if (!preg_match($iso8601Pattern, $to)) {
            throw new HttpBadRequestException($request, 'Parameter to must be ISO 8601 with Z (e.g. 2026-02-26T22:33:57.072Z)');
        }

        try {
            $fromDateTime = new \DateTime($from);
            $toDateTime = new \DateTime($to);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, 'Invalid datetime value');
        }

        if ($fromDateTime >= $toDateTime) {
            throw new HttpBadRequestException($request, 'Parameter from must be before to');
        }

        $fromDb = $fromDateTime->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        $toDb = $toDateTime->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');

        try {
            $values = $this->pegelService->getHistory($fromDb, $toDb);
        } catch (\Exception $e) {
            throw new HttpInternalServerErrorException($request, 'Database error: ' . $e->getMessage(), $e);
        }

        $values = array_map(function ($row) {
            $row['recorded_at'] = (new \DateTime($row['recorded_at'], new \DateTimeZone('UTC')))
                ->format('Y-m-d\TH:i:s\Z');
            return $row;
        }, $values);

        $result = ['data' => $values];
        $response->getBody()->write(json_encode($result));
        return $response;
    }
}
