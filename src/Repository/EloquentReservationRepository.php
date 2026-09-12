<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Collection;

class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function __construct(
        private ?Capsule $capsule = null
    ) {
    }
    public function trouver(int $id): ?Reservation
    {
        return Reservation::query()->find($id);
    }

    public function toutes(): Collection
    {
        return Reservation::query()->with('salle')->orderBy('date_debut')->get();
    }

    public function trouverParSalle(int $salleId): Collection
    {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->orderBy('date_debut')
            ->get();
    }

    public function trouverParSalleEtPeriode(int $salleId, \DateTimeInterface $debut, \DateTimeInterface $fin): Collection
    {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->where('statut', 'confirmee')
            ->where(function ($query) use ($debut, $fin) {
                $query->where('date_debut', '<', $fin)
                    ->where('date_fin', '>', $debut);
            })
            ->get();
    }

    public function trouverEnCoursParSalle(int $salleId): Collection
    {
        $now = new \DateTime();
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->where('statut', 'confirmee')
            ->where('date_debut', '<=', $now)
            ->where('date_fin', '>=', $now)
            ->get();
    }

    public function creer(array $donnees): Reservation
    {
        return Reservation::query()->create($donnees);
    }

    public function annuler(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulee';
        $reservation->save();
        return $reservation;
    }

    public function supprimer(Reservation $reservation): bool
    {
        return (bool) $reservation->delete();
    }
}
