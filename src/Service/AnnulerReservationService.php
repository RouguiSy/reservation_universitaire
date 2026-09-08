<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;

class AnnulerReservationService
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    ) {
    }

    public function executer(int $id): Reservation
    {
        $reservation = $this->reservationRepository->trouver($id);

        if (!$reservation) {
            throw new \InvalidArgumentException('Reservation non trouvee');
        }

        if ($reservation->estAnnulee()) {
            throw new \InvalidArgumentException('Cette reservation est deja annulee');
        }

        return $this->reservationRepository->annuler($reservation);
    }
}
