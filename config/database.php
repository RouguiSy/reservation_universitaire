<?php

declare(strict_types=1);

return [
    'driver'    => $_ENV['DB_DRIVER'] ?? 'mysql',
    'host'      => $_ENV['DB_HOST'] ?? 'mysql',
    'port'      => $_ENV['DB_PORT'] ?? '3306',
    'database'  => $_ENV['DB_DATABASE'] ?? 'reservation_salles',
    'username'  => $_ENV['DB_USERNAME'] ?? 'app_user',
    'password'  => $_ENV['DB_PASSWORD'] ?? 'app_password',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
];
