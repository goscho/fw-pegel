<?php

namespace App\Controller;

use App\Service\PegelService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class ApiController
{

    function __construct(
        private PegelService $pegelService
    ) {}


    function getAll(Request $request, Response $response): Response
    {
        $values = $this->pegelService->getAll();
        $response->getBody()->write(json_encode($values));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
