<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exception\AlreadyExistsException;
use App\Exception\BusinessException;
use App\Exception\ExceptionHandler;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\SalleIndisponibleException;
use App\Exception\UnauthorizedException;
use App\Exception\ValidationException;
use App\Middleware\ErrorHandlerMiddleware;
use App\Session\SessionManager;
use PHPUnit\Framework\TestCase;

final class ErrorHandlerMiddlewareTest extends TestCase
{
    private SessionManager $session;
    private ExceptionHandler $handler;
    private ErrorHandlerMiddleware $middleware;

    protected function setUp(): void
    {
        $this->session = new SessionManager();
        if ($this->session->isStarted()) {
            $this->session->clear();
        } else {
            $_SESSION = [];
        }

        $this->handler = new ExceptionHandler($this->session);
        $this->middleware = new ErrorHandlerMiddleware($this->handler);
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
    }

    public function test_process_executes_next_when_no_error(): void
    {
        $executed = false;

        $this->middleware->process(function () use (&$executed): void {
            $executed = true;
        });

        self::assertTrue($executed);
    }

    public function test_determine_status_code_for_known_exceptions(): void
    {
        $validationEx = new ValidationException(['nom' => 'Invalide']);
        self::assertSame(422, $this->handler->determineStatusCode($validationEx));

        $notFoundEx = new NotFoundException('Ressource introuvable');
        self::assertSame(404, $this->handler->determineStatusCode($notFoundEx));

        $unauthorizedEx = new UnauthorizedException();
        self::assertSame(401, $this->handler->determineStatusCode($unauthorizedEx));

        $forbiddenEx = new ForbiddenException();
        self::assertSame(403, $this->handler->determineStatusCode($forbiddenEx));

        $alreadyExistsEx = new AlreadyExistsException();
        self::assertSame(409, $this->handler->determineStatusCode($alreadyExistsEx));

        $businessEx = new BusinessException('Regle violee', 422);
        self::assertSame(422, $this->handler->determineStatusCode($businessEx));

        $conflictEx = new SalleIndisponibleException('Conflit');
        self::assertSame(409, $this->handler->determineStatusCode($conflictEx));

        $invalidArgEx = new \InvalidArgumentException('Argument');
        self::assertSame(400, $this->handler->determineStatusCode($invalidArgEx));

        $generalEx = new \RuntimeException('Crash');
        self::assertSame(500, $this->handler->determineStatusCode($generalEx));
    }

    public function test_extract_errors_from_validation_exception(): void
    {
        $errors = ['email' => 'Adresse email invalide'];
        $validationEx = new ValidationException($errors);

        self::assertSame($errors, $this->handler->extractErrors($validationEx));
        self::assertSame($errors, $validationEx->errors());
        self::assertSame($errors, $validationEx->getErreurs());
    }
}
