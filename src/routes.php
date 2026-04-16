<?php

use App\Controller\PegelApiController;
use App\Controller\RainfallApiController;
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
        // Pegel API routes
        $group->get('/pegel', [PegelApiController::class, 'getLatest']);
        $group->get('/pegel/history', [PegelApiController::class, 'getHistory']);
        $group->post('/pegel', [PegelApiController::class, 'addValue'])
            ->add(ValidateApiKeyHeader::class);

        // Rainfall API routes
        $group->get('/rainfall', [RainfallApiController::class, 'getLatest']);
        $group->get('/rainfall/history', [RainfallApiController::class, 'getHistory']);
        $group->post('/rainfall', [RainfallApiController::class, 'addValue'])
            ->add(ValidateApiKeyHeader::class);

    })->add(AddJsonResponseHeader::class);
};
