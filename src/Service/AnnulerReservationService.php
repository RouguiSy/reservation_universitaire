<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\BusinessException;
use App\Exception\NotFoundException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;

class AnnulerReservationService implements AnnulerReservationServiceInterface
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    ) {
    }

    public function executer(int $id): Reservation
    {
        $reservation = $this->reservationRepository->trouver($id);

        if (!$reservation) {
            $msg = function_exists('message') ? message('reservation.not_found', [], 'Reservation non trouvee') : 'Reservation non trouvee';
            throw new NotFoundException($msg);
        }

        if ($reservation->estAnnulee()) {
            $msg = function_exists('message') ? message('reservation.already_cancelled', [], 'Cette reservation est deja annulee') : 'Cette reservation est deja annulee';
            throw new BusinessException($msg, 409);
        }

        return $this->reservationRepository->annuler($reservation);
    }
}
