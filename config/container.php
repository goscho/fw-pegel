<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Views\PhpRenderer;

$builder = new ContainerBuilder();

$builder->addDefinitions([
    PDO::class => function () {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'] ?? getenv('DB_HOST'),
            $_ENV['DB_NAME'] ?? getenv('DB_NAME')
        );

        return new PDO(
            $dsn,
            $_ENV['DB_USER'] ?? getenv('DB_USER'),
            $_ENV['DB_PASS'] ?? getenv('DB_PASS'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    },
    PhpRenderer::class => function () {

        $renderer = new PhpRenderer(__DIR__ . '/../src/view');

        //$renderer->setLayout('layout.php');

        return $renderer;
    }
]);

return $builder->build();
