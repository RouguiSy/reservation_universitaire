<?php

declare(strict_types=1);

use App\Repository\SalleRepositoryInterface;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\EloquentReservationRepository;
use App\Service\CreerReservationService;
use App\Service\AnnulerReservationService;
use App\Controller\SalleController;
use App\Controller\ReservationController;

return [
    // Repositories
    SalleRepositoryInterface::class => \DI\autowire(EloquentSalleRepository::class),
    ReservationRepositoryInterface::class => \DI\autowire(EloquentReservationRepository::class),

    // Services
    CreerReservationService::class => \DI\autowire()
        ->constructorParameter('salleRepository', \DI\get(SalleRepositoryInterface::class))
        ->constructorParameter('reservationRepository', \DI\get(ReservationRepositoryInterface::class)),

    AnnulerReservationService::class => \DI\autowire()
        ->constructorParameter('reservationRepository', \DI\get(ReservationRepositoryInterface::class)),

    \App\Security\CsrfService::class => \DI\autowire(\App\Security\CsrfService::class),

    // Controleurs
    SalleController::class => \DI\autowire()
        ->constructorParameter('salleRepository', \DI\get(SalleRepositoryInterface::class)),

    ReservationController::class => \DI\autowire()
        ->constructorParameter('salleRepository', \DI\get(SalleRepositoryInterface::class))
        ->constructorParameter('reservationRepository', \DI\get(ReservationRepositoryInterface::class))
        ->constructorParameter('creerService', \DI\get(CreerReservationService::class))
        ->constructorParameter('annulerService', \DI\get(AnnulerReservationService::class)),
];
