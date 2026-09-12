<?php

declare(strict_types=1);

namespace App\Router;

interface RouterInterface
{
    public function dispatch(string $httpMethod, string $uri): void;

    public function execute(mixed $handler, array $arguments = []): void;

    public function instantiateController(string $controllerClass): object;
}
