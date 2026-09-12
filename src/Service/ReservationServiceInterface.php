<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Model\Reservation;
use Illuminate\Support\Collection;

interface ReservationServiceInterface
{
    public function lister(?int $salleId = null): Collection;

    public function listerSallesActives(): Collection;

    public function trouver(int $id): ?Reservation;

    public function trouverOrFail(int $id): Reservation;

    public function creer(CreerReservationDTO $dto): Reservation;

    public function annuler(int $id): Reservation;
}
