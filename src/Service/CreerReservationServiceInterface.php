<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Model\Reservation;

interface CreerReservationServiceInterface
{
    public function executer(CreerReservationDTO $dto): Reservation;
}
