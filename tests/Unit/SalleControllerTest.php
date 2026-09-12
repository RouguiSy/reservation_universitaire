<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controller\SalleController;
use App\Service\SalleServiceInterface;
use App\Session\SessionManagerInterface;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class SalleControllerTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/src/helpers.php';
        $_GET = [];
        $_POST = [];
    }

    public function testControllerAcceptsMockedServiceInterface(): void
    {
        $mockService = $this->createMock(SalleServiceInterface::class);
        $mockSession = $this->createMock(SessionManagerInterface::class);

        $controller = new SalleController($mockService, $mockSession);
        $this->assertInstanceOf(SalleController::class, $controller);
    }

    public function testCreateMethodLoadsFormErrorsAndOldDataFromSession(): void
    {
        $mockService = $this->createMock(SalleServiceInterface::class);
        $mockSession = $this->createMock(SessionManagerInterface::class);

        $mockSession->expects($this->once())
            ->method('getFormErrors')
            ->with('salle')
            ->willReturn(['nom' => ['Le nom est requis']]);

        $mockSession->expects($this->once())
            ->method('getFormOld')
            ->with('salle')
            ->willReturn(['nom' => '']);

        $controller = new SalleController($mockService, $mockSession);

        // Capture de la sortie du respond()
        ob_start();
        $controller->create();
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }
}
