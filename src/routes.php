<?php

use App\Controller\ApiController;
use App\Controller\ViewController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as RouteCollectorProxy;
use App\Middleware\AddJsonResponseHeader;
use App\Middleware\ValidateApiKeyHeader;

return function (App $app): void {
    // UI routes
    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/', [ViewController::class, 'index']);
    });

    // API routes
    $app->group('/api', function (RouteCollectorProxy $group) {
        $group->get('/pegel', [ApiController::class, 'getLatest']);
        $group->post('/pegel', [ApiController::class, 'addValue'])
            ->add(ValidateApiKeyHeader::class);
    })->add(AddJsonResponseHeader::class);
};
