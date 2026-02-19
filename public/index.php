<?php

declare(strict_types=1);

use DI\Container;
use DI\Bridge\Slim\Bridge;

require __DIR__ . '/../vendor/autoload.php';

// Create DI Container
$container = new Container();

// Create App using PHP-DI bridge
$app = Bridge::create($container);

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Register routes
(require __DIR__ . '/../src/routes.php')($app);


$app->run();
