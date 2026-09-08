<?php

declare(strict_types=1);

namespace App\Controller;

class HomeController
{
    public function index(): void
    {
        require_once dirname(__DIR__, 2) . '/templates/home.php';
    }
}
