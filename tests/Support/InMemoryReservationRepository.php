<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class InMemoryReservationRepository implements ReservationRepositoryInterface
{
    private array $reservations = [];
    private int $nextId = 1;

    public function trouver(int $id): ?Reservation
    {
        return $this->reservations[$id] ?? null;
    }

    public function trouverParSalle(int $salleId): Collection
    {
        $result = array_filter(
            $this->reservations,
            fn($r) => $r->salle_id === $salleId
        );
        return new Collection(array_values($result));
    }

    public function trouverParSalleEtPeriode(int $salleId, \DateTimeInterface $debut, \DateTimeInterface $fin): Collection
    {
        $result = array_filter(
            $this->reservations,
            function ($r) use ($salleId, $debut, $fin) {
                return $r->salle_id === $salleId
                    && $r->statut === 'confirmee'
                    && $r->date_debut < $fin
                    && $r->date_fin > $debut;
            }
        );
        return new Collection(array_values($result));
    }

    public function trouverEnCoursParSalle(int $salleId): Collection
    {
        $now = new \DateTime();
        $result = array_filter(
            $this->reservations,
            function ($r) use ($salleId, $now) {
                return $r->salle_id === $salleId
                    && $r->statut === 'confirmee'
                    && $r->date_debut <= $now
                    && $r->date_fin >= $now;
            }
        );
        return new Collection(array_values($result));
    }

    public function creer(array $donnees): Reservation
    {
        $reservation = new Reservation();
        $reservation->id = $this->nextId++;
        $reservation->salle_id = $donnees['salle_id'];
        $reservation->responsable = $donnees['responsable'];
        $reservation->email = $donnees['email'];
        $reservation->motif = $donnees['motif'];
        $reservation->date_debut = new \DateTime($donnees['date_debut']);
        $reservation->date_fin = new \DateTime($donnees['date_fin']);
        $reservation->statut = $donnees['statut'] ?? 'confirmee';
        $this->reservations[$reservation->id] = $reservation;
        return $reservation;
    }

    public function annuler(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulee';
        $this->reservations[$reservation->id] = $reservation;
        return $reservation;
    }

    public function supprimer(Reservation $reservation): bool
    {
        unset($this->reservations[$reservation->id]);
        return true;
    }
}
