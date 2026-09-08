<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Model\Reservation;
use App\Repository\SalleRepositoryInterface;
use App\Repository\ReservationRepositoryInterface;
use App\Exception\SalleIndisponibleException;

class CreerReservationService
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository,
        private ReservationRepositoryInterface $reservationRepository
    ) {
    }

    public function executer(CreerReservationDTO $dto): Reservation
    {
        $salle = $this->salleRepository->trouver($dto->salleId);

        if (!$salle) {
            throw SalleIndisponibleException::salleNonTrouvee();
        }

        if (!$salle->active) {
            throw SalleIndisponibleException::salleInactive();
        }

        $duree = $dto->dateDebut->diff($dto->dateFin);
        if ($duree->days > 1) {
            throw SalleIndisponibleException::dureeExcessive();
        }

        $conflits = $this->reservationRepository->trouverParSalleEtPeriode(
            $dto->salleId,
            $dto->dateDebut,
            $dto->dateFin
        );

        if ($conflits->isNotEmpty()) {
            throw SalleIndisponibleException::conflit($dto->dateDebut, $dto->dateFin);
        }

        $donnees = $dto->toArray();
        $donnees['statut'] = 'confirmee';

        return $this->reservationRepository->creer($donnees);
    }
}
