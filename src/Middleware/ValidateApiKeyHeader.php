<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpUnauthorizedException;

class ValidateApiKeyHeader
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $apiKey = $request->getHeaderLine('X-API-Key');
        if ($apiKey !== $_ENV['API_KEY']) {
            throw new HttpUnauthorizedException($request, 'Invalid API key provided');
        }

        $response = $handler->handle($request);

        return $response;
    }
}
