<?php

declare(strict_types=1);

use App\Application;
use App\Exception\ExceptionHandler;
use App\Middleware\ErrorHandlerMiddleware;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\EloquentUserRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\CsrfService;
use App\Service\AnnulerReservationService;
use App\Service\AnnulerReservationServiceInterface;
use App\Service\AuthService;
use App\Service\AuthServiceInterface;
use App\Service\CreerReservationService;
use App\Service\CreerReservationServiceInterface;
use App\Service\DashboardService;
use App\Service\DashboardServiceInterface;
use App\Service\ReservationService;
use App\Service\ReservationServiceInterface;
use App\Service\SalleService;
use App\Service\SalleServiceInterface;
use App\Session\SessionManager;
use App\Session\SessionManagerInterface;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\View\HtmlRenderer;
use App\View\JsonRenderer;
use App\View\Negotiation\FormatNegotiator;
use App\View\Negotiation\FormatNegotiatorInterface;
use App\View\RendererFactory;
use App\View\RendererFactoryInterface;
use App\View\RendererInterface;
use App\View\ViewRenderer;
use App\Router\Router;
use App\Router\RouterInterface;
use FastRoute\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

use function DI\autowire;
use function DI\factory;

return [

    Capsule::class => factory(static function (): Capsule {
        $config = require __DIR__ . '/database.php';
        return \App\Database\DatabaseFactory::create($config);
    }),

    SalleRepositoryInterface::class => autowire(EloquentSalleRepository::class),
    ReservationRepositoryInterface::class => autowire(EloquentReservationRepository::class),
    UserRepositoryInterface::class => autowire(EloquentUserRepository::class),

    SalleValidator::class => autowire(SalleValidator::class),
    ReservationValidator::class => autowire(ReservationValidator::class),

    CreerReservationServiceInterface::class => autowire(CreerReservationService::class),
    CreerReservationService::class => autowire(CreerReservationService::class),
    AnnulerReservationServiceInterface::class => autowire(AnnulerReservationService::class),
    AnnulerReservationService::class => autowire(AnnulerReservationService::class),
    SalleServiceInterface::class => autowire(SalleService::class),
    SalleService::class => autowire(SalleService::class),
    ReservationServiceInterface::class => autowire(ReservationService::class),
    ReservationService::class => autowire(ReservationService::class),
    AuthServiceInterface::class => autowire(AuthService::class),
    AuthService::class => autowire(AuthService::class),
    DashboardServiceInterface::class => autowire(DashboardService::class),
    DashboardService::class => autowire(DashboardService::class),

    SessionManagerInterface::class => autowire(SessionManager::class),
    SessionManager::class => autowire(SessionManager::class),
    CsrfService::class => autowire(CsrfService::class),

    ExceptionHandler::class => autowire(ExceptionHandler::class),
    ErrorHandlerMiddleware::class => autowire(ErrorHandlerMiddleware::class),

    FormatNegotiatorInterface::class => autowire(FormatNegotiator::class)
        ->constructorParameter('defaultFormat', $_ENV['APP_RESPONSE_FORMAT'] ?? 'html'),
    RendererFactoryInterface::class => autowire(RendererFactory::class),
    HtmlRenderer::class => autowire(HtmlRenderer::class),
    JsonRenderer::class => autowire(JsonRenderer::class),
    ViewRenderer::class => autowire(ViewRenderer::class),
    RendererInterface::class => autowire(ViewRenderer::class),

    RouterInterface::class => autowire(Router::class),
    Router::class => autowire(Router::class),

    Dispatcher::class => factory(static function (): Dispatcher {
        $routesCallable = require dirname(__DIR__) . '/routes/web.php';
        return \FastRoute\simpleDispatcher($routesCallable);
    }),

    Application::class => autowire(Application::class),
];
