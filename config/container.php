<?php

declare(strict_types=1);

use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;

require_once dirname(__DIR__) . '/config/database.php';

$builder = new ContainerBuilder();
$builder->addDefinitions([
    Capsule::class => static fn (): Capsule => require dirname(__DIR__) . '/config/database.php',
    SalleRepositoryInterface::class => DI\autowire(EloquentSalleRepository::class),
    ReservationRepositoryInterface::class => DI\autowire(EloquentReservationRepository::class),
    CreerReservationService::class => DI\autowire(),
    AnnulerReservationService::class => DI\autowire(),
]);

return $builder->build();