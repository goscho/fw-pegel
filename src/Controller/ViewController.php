<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class ViewController
{

    function __construct(private \Slim\Views\PhpRenderer $renderer) {}


    function index(Request $request, Response $response): Response
    {
        return $this->renderer->render($response, 'home.view.php');
    }
}
