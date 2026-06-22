<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Views\PhpRenderer;

$builder = new ContainerBuilder();

$builder->addDefinitions([
    PDO::class => function () {
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
        $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306;
        $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
        $user = $_ENV['DB_USER'] ?? getenv('DB_USER');
        $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS');

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $host,
            (int) $port,
            $name
        );

        return new PDO(
            $dsn,
            $user,
            $pass,
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
