<?php

use App\Controller\ApiController;
use Slim\App;

return function (App $app): void {
    // Define routes
    $app->get('/pegel', [ApiController::class, 'getAll']);
};
