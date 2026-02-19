<?php

use App\Controller\ApiController;
use App\Controller\ViewController;
use Slim\App;

return function (App $app): void {
    // Define routes
    $app->get('/', [ViewController::class, 'index']);
    
    $app->get('/pegel', [ApiController::class, 'getAll']);
};
