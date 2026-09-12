<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Exception\NotFoundException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use Illuminate\Support\Collection;

class ReservationService implements ReservationServiceInterface
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private SalleRepositoryInterface $salleRepository,
        private CreerReservationServiceInterface $creerService,
        private AnnulerReservationServiceInterface $annulerService
    ) {
    }

    public function lister(?int $salleId = null): Collection
    {
        return $salleId !== null
            ? $this->reservationRepository->trouverParSalle($salleId)
            : $this->reservationRepository->toutes();
    }

    public function listerSallesActives(): Collection
    {
        return $this->salleRepository->actives();
    }

    public function trouver(int $id): ?Reservation
    {
        return $this->reservationRepository->trouver($id);
    }

    public function trouverOrFail(int $id): Reservation
    {
        $reservation = $this->trouver($id);
        if (!$reservation) {
            $msg = function_exists('message') ? message('reservation.not_found', [], 'Reservation non trouvee') : 'Reservation non trouvee';
            throw new NotFoundException($msg);
        }

        return $reservation;
    }

    public function creer(CreerReservationDTO $dto): Reservation
    {
        return $this->creerService->executer($dto);
    }

    public function annuler(int $id): Reservation
    {
        return $this->annulerService->executer($id);
    }
}
