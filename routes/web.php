<?php

declare(strict_types=1);

use FastRoute\RouteCollector;

return function (RouteCollector $r): void {
    $r->addRoute('GET', '/', 'App\\Controller\\HomeController@index');
    $r->addRoute('GET', '/login', 'App\\Controller\\AuthController@login');
    $r->addRoute('POST', '/login', 'App\\Controller\\AuthController@authenticate');
    $r->addRoute('GET', '/logout', 'App\\Controller\\AuthController@logout');
    $r->addRoute('GET', '/dashboard', 'App\\Controller\\DashboardController@index');
    $r->addRoute('GET', '/salles', 'App\\Controller\\SalleController@index');
    $r->addRoute('GET', '/salles/create', 'App\\Controller\\SalleController@create');
    $r->addRoute('POST', '/salles/store', 'App\\Controller\\SalleController@store');
    $r->addRoute(['GET', 'POST'], '/salles/toggle/{id:\\d+}', 'App\\Controller\\SalleController@toggle');
    $r->addRoute(['GET', 'POST'], '/salles/delete/{id:\\d+}', 'App\\Controller\\SalleController@delete');
    $r->addRoute('GET', '/reservations', 'App\\Controller\\ReservationController@index');
    $r->addRoute('GET', '/reservations/create', 'App\\Controller\\ReservationController@create');
    $r->addRoute('POST', '/reservations/store', 'App\\Controller\\ReservationController@store');
    $r->addRoute(['GET', 'POST'], '/reservations/cancel/{id:\\d+}', 'App\\Controller\\ReservationController@cancel');
};
