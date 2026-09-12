<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Router\Router;
use App\Router\RouterInterface;
use FastRoute\RouteCollector;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/src/helpers.php';
    }

    public function testRouterImplementsRouterInterface(): void
    {
        $router = new Router();
        $this->assertInstanceOf(RouterInterface::class, $router);
    }

    public function testInstantiateControllerWithoutConstructor(): void
    {
        $router = new Router();
        $controller = $router->instantiateController(DummySimpleController::class);
        $this->assertInstanceOf(DummySimpleController::class, $controller);
    }

    public function testInstantiateControllerWithContainerDependency(): void
    {
        $mockContainer = $this->createMock(ContainerInterface::class);
        $dummyService = new \stdClass();

        $mockContainer->expects($this->once())
            ->method('has')
            ->with(\stdClass::class)
            ->willReturn(true);

        $mockContainer->expects($this->once())
            ->method('get')
            ->with(\stdClass::class)
            ->willReturn($dummyService);

        $router = new Router($mockContainer);
        $controller = $router->instantiateController(DummyControllerWithDependency::class);

        $this->assertInstanceOf(DummyControllerWithDependency::class, $controller);
        $this->assertSame($dummyService, $controller->service);
    }

    public function testDispatchCallableRoute(): void
    {
        $called = false;
        $router = new Router(null, function (RouteCollector $r) use (&$called): void {
            $r->addRoute('GET', '/test-route', function () use (&$called): void {
                $called = true;
            });
        });

        $router->dispatch('GET', '/test-route');
        $this->assertTrue($called);
    }

    public function testDispatchControllerAction(): void
    {
        $router = new Router(null, function (RouteCollector $r): void {
            $r->addRoute('GET', '/dummy-action', [DummySimpleController::class, 'ping']);
        });

        DummySimpleController::$pinged = false;
        $router->dispatch('GET', '/dummy-action');
        $this->assertTrue(DummySimpleController::$pinged);
    }
}

class DummySimpleController
{
    public static bool $pinged = false;

    public function ping(): void
    {
        self::$pinged = true;
    }
}

class DummyControllerWithDependency
{
    public function __construct(public \stdClass $service)
    {
    }
}
