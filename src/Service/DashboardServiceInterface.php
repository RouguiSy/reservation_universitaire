<?php

declare(strict_types=1);

namespace App\Service;

interface DashboardServiceInterface
{
    public function obtenirStatistiques(): array;
}
