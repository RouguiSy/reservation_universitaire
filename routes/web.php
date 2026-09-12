<?php

declare(strict_types=1);

use FastRoute\RouteCollector;
use App\Controller\HomeController;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\SalleController;
use App\Controller\ReservationController;

return function (RouteCollector $r): void {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/login', [AuthController::class, 'login']);
    $r->addRoute('POST', '/login', [AuthController::class, 'authenticate']);
    $r->addRoute('GET', '/logout', [AuthController::class, 'logout']);
    $r->addRoute('GET', '/dashboard', [DashboardController::class, 'index']);

    $r->addRoute('GET', '/salles', [SalleController::class, 'index']);
    $r->addRoute('GET', '/salles/create', [SalleController::class, 'create']);
    $r->addRoute('POST', '/salles', [SalleController::class, 'store']);
    $r->addRoute('POST', '/salles/store', [SalleController::class, 'store']);
    $r->addRoute('GET', '/salles/{id:\d+}', [SalleController::class, 'show']);
    $r->addRoute('GET', '/salles/{id:\d+}/edit', [SalleController::class, 'edit']);
    $r->addRoute('POST', '/salles/{id:\d+}/edit', [SalleController::class, 'update']);
    $r->addRoute('GET', '/salles/toggle/{id:\d+}', [SalleController::class, 'toggle']);
    $r->addRoute('POST', '/salles/toggle/{id:\d+}', [SalleController::class, 'toggle']);
    $r->addRoute('GET', '/salles/delete/{id:\d+}', [SalleController::class, 'delete']);
    $r->addRoute('POST', '/salles/delete/{id:\d+}', [SalleController::class, 'delete']);

    $r->addRoute('GET', '/reservations', [ReservationController::class, 'index']);
    $r->addRoute('GET', '/reservations/create', [ReservationController::class, 'create']);
    $r->addRoute('POST', '/reservations', [ReservationController::class, 'store']);
    $r->addRoute('POST', '/reservations/store', [ReservationController::class, 'store']);
    $r->addRoute('GET', '/reservations/{id:\d+}', [ReservationController::class, 'show']);
    $r->addRoute('POST', '/reservations/{id:\d+}/cancel', [ReservationController::class, 'cancel']);
    $r->addRoute('GET', '/reservations/cancel/{id:\d+}', [ReservationController::class, 'cancel']);
    $r->addRoute('POST', '/reservations/cancel/{id:\d+}', [ReservationController::class, 'cancel']);
};
