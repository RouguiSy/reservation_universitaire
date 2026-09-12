<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Reservation;

interface AnnulerReservationServiceInterface
{
    public function executer(int $id): Reservation;
}
