<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Model\User;
use App\Repository\UserRepositoryInterface;
use App\Service\AuthService;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    private UserRepositoryInterface $userRepo;
    private AuthService $service;

    protected function setUp(): void
    {
        $this->userRepo = $this->createMock(UserRepositoryInterface::class);
        $this->service = new AuthService($this->userRepo);
    }

    public function testAuthentifierReturnsUserOnValidCredentials(): void
    {
        $user = new User();
        $user->email = 'admin@example.com';
        $user->password = password_hash('secret123', PASSWORD_BCRYPT);

        $this->userRepo->expects($this->once())
            ->method('trouverParEmail')
            ->with('admin@example.com')
            ->willReturn($user);

        $result = $this->service->authentifier('admin@example.com', 'secret123');
        $this->assertSame($user, $result);
    }

    public function testAuthentifierReturnsNullWhenUserNotFound(): void
    {
        $this->userRepo->expects($this->once())
            ->method('trouverParEmail')
            ->with('unknown@example.com')
            ->willReturn(null);

        $result = $this->service->authentifier('unknown@example.com', 'secret123');
        $this->assertNull($result);
    }

    public function testAuthentifierReturnsNullOnInvalidPassword(): void
    {
        $user = new User();
        $user->email = 'admin@example.com';
        $user->password = password_hash('secret123', PASSWORD_BCRYPT);

        $this->userRepo->expects($this->once())
            ->method('trouverParEmail')
            ->with('admin@example.com')
            ->willReturn($user);

        $result = $this->service->authentifier('admin@example.com', 'wrongpassword');
        $this->assertNull($result);
    }

    public function testAuthentifierOrFailReturnsUserOnValidCredentials(): void
    {
        $user = new User();
        $user->email = 'admin@example.com';
        $user->password = password_hash('secret123', PASSWORD_BCRYPT);

        $this->userRepo->expects($this->once())
            ->method('trouverParEmail')
            ->with('admin@example.com')
            ->willReturn($user);

        $result = $this->service->authentifierOrFail('admin@example.com', 'secret123');
        $this->assertSame($user, $result);
    }

    public function testAuthentifierOrFailThrowsExceptionWhenInvalid(): void
    {
        $this->userRepo->expects($this->once())
            ->method('trouverParEmail')
            ->with('invalid@example.com')
            ->willReturn(null);

        $this->expectException(\App\Exception\UnauthorizedException::class);
        $this->service->authentifierOrFail('invalid@example.com', 'secret123');
    }
}
