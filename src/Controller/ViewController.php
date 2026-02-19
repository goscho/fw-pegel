<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class ViewController
{

    function __construct() {}


    function index(Request $request, Response $response): Response
    {
        $content = $this->render('home');
        $response->getBody()->write($content);
        return $response->withHeader('Content-Type', 'text/html');
    }

    private function render(string $view, array $data = []): string
    {
        ob_start();
        extract($data);
        require __DIR__ . '/../view/' . $view . '.view.php';
        return ob_get_clean();
    }
}
