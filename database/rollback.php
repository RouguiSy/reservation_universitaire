#!/usr/bin/env php
<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$capsule = require_once dirname(__DIR__) . '/config/database.php';

$migrations = glob(dirname(__DIR__) . '/database/migrations/*.php');

foreach (array_reverse($migrations) as $migration) {
    $instance = require $migration;
    $instance->down();
    echo "Rollback execute : " . basename($migration) . "\n";
}
