<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PegelService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class ApiController
{

    function __construct(
        private PegelService $pegelService
    ) {}


    function getLatest(Request $request, Response $response): Response
    {
        $values = $this->pegelService->getLatest();
        $result = ['data' => $values];
        $response->getBody()->write(json_encode($result));
        return $response;
    }
}
