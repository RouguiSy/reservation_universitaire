<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DashboardService;
use App\Service\DashboardServiceInterface;

class DashboardController
{
    public function __construct(
        private ?DashboardServiceInterface $dashboardService = null
    ) {
        $this->dashboardService = $dashboardService ?? new DashboardService();
    }

    public function index(): void
    {
        $stats = $this->dashboardService->obtenirStatistiques();
        respond('dashboard.php', $stats);
    }
}
