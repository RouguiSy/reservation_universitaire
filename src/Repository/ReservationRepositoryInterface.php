<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use Illuminate\Database\Eloquent\Collection;

interface ReservationRepositoryInterface
{
    public function trouver(int $id): ?Reservation;
    public function trouverParSalle(int $salleId): Collection;
    public function trouverParSalleEtPeriode(int $salleId, \DateTimeInterface $debut, \DateTimeInterface $fin): Collection;
    public function trouverEnCoursParSalle(int $salleId): Collection;
    public function creer(array $donnees): Reservation;
    public function annuler(Reservation $reservation): Reservation;
    public function supprimer(Reservation $reservation): bool;
}
