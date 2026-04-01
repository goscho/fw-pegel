<?php

declare(strict_types=1);

use DI\Bridge\Slim\Bridge;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Create DI Container
$container = require __DIR__ . '/../config/container.php';

// Create App using PHP-DI bridge
$app = Bridge::create($container);

// Add error middleware
$displayErrorDetails = $_ENV['ENVIRONMENT'] === 'DEVELOPMENT'; // Set to false in production
$app->addErrorMiddleware($displayErrorDetails, true, true);

// Add body parsing middleware so JSON/form bodies are populated
$app->addBodyParsingMiddleware();

// Register routes
(require __DIR__ . '/../src/routes.php')($app);


$app->run();
