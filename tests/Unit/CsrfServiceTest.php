<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Security\CsrfService;
use PHPUnit\Framework\TestCase;

class CsrfServiceTest extends TestCase
{
    private CsrfService $csrfService;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        $this->csrfService = new CsrfService();
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }

    public function testGetTokenGeneratesNonEmptyString(): void
    {
        $token = $this->csrfService->getToken();

        $this->assertIsString($token);
        $this->assertSame(64, strlen($token));
        $this->assertSame($token, $this->csrfService->getToken());
    }

    public function testValidateValidToken(): void
    {
        $token = $this->csrfService->getToken();

        $this->assertTrue($this->csrfService->validate($token));
    }

    public function testValidateInvalidToken(): void
    {
        $this->csrfService->getToken();

        $this->assertFalse($this->csrfService->validate('invalid-token-123456'));
        $this->assertFalse($this->csrfService->validate(''));
        $this->assertFalse($this->csrfService->validate(null));
    }

    public function testRegenerateToken(): void
    {
        $initialToken = $this->csrfService->getToken();
        $newToken = $this->csrfService->regenerateToken();

        $this->assertNotEquals($initialToken, $newToken);
        $this->assertSame(64, strlen($newToken));
        $this->assertTrue($this->csrfService->validate($newToken));
        $this->assertFalse($this->csrfService->validate($initialToken));
    }
}
