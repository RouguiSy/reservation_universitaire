#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Model\Salle;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$capsule = require_once dirname(__DIR__) . '/config/database.php';

$salles = [
    ['nom' => 'Amphitheatre A', 'batiment' => 'Batiment Principal', 'capacite' => 250, 'type' => 'amphitheatre', 'active' => true],
    ['nom' => 'Amphitheatre B', 'batiment' => 'Batiment Principal', 'capacite' => 200, 'type' => 'amphitheatre', 'active' => true],
    ['nom' => 'Salle B12', 'batiment' => 'Batiment B', 'capacite' => 40, 'type' => 'cours', 'active' => true],
    ['nom' => 'Salle B14', 'batiment' => 'Batiment B', 'capacite' => 35, 'type' => 'cours', 'active' => true],
    ['nom' => 'Laboratoire Chimie', 'batiment' => 'Batiment Sciences', 'capacite' => 24, 'type' => 'laboratoire', 'active' => true],
    ['nom' => 'Laboratoire Physique', 'batiment' => 'Batiment Sciences', 'capacite' => 20, 'type' => 'laboratoire', 'active' => true],
    ['nom' => 'Salle Informatique 1', 'batiment' => 'Batiment Sciences', 'capacite' => 30, 'type' => 'informatique', 'active' => true],
    ['nom' => 'Salle Informatique 2', 'batiment' => 'Batiment Sciences', 'capacite' => 28, 'type' => 'informatique', 'active' => true],
    ['nom' => 'Salle de reunion', 'batiment' => 'Batiment Administration', 'capacite' => 12, 'type' => 'reunion', 'active' => true],
    ['nom' => 'Salle de reunion 2', 'batiment' => 'Batiment Administration', 'capacite' => 8, 'type' => 'reunion', 'active' => false],
];

$crees = 0;

foreach ($salles as $donnees) {
    $salle = Salle::query()->firstOrCreate(
        ['nom' => $donnees['nom'], 'batiment' => $donnees['batiment']],
        $donnees
    );

    if ($salle->wasRecentlyCreated) {
        $crees++;
        echo "Salle creee : {$salle->nom}\n";
    }
}

echo "\nTermine : {$crees} nouvelle(s) salle(s) creee(s) sur " . count($salles) . ".\n";
