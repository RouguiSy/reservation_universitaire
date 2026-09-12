#!/usr/bin/env php
<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/container.php');
$container = $builder->build();
$capsule = $container->get(Capsule::class);

$migrations = glob(dirname(__DIR__) . '/database/migrations/*.php');

foreach ($migrations as $migration) {
    $instance = require $migration;
    $instance->up();
    echo "Migration executee : " . basename($migration) . "\n";
}
