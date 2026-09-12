<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\ExceptionHandler;
use Throwable;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ?ExceptionHandler $exceptionHandler = null
    ) {
        $this->exceptionHandler = $exceptionHandler ?? new ExceptionHandler();
    }

    public function process(callable $next): void
    {
        try {
            $next();
        } catch (Throwable $e) {
            $this->exceptionHandler->handle($e);
        }
    }
}
