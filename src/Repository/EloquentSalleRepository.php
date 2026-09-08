<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Database\Eloquent\Collection;

class EloquentSalleRepository implements SalleRepositoryInterface
{
    public function trouver(int $id): ?Salle
    {
        return Salle::query()->find($id);
    }

    public function trouverParNomEtBatiment(string $nom, string $batiment): ?Salle
    {
        return Salle::query()
            ->where('nom', $nom)
            ->where('batiment', $batiment)
            ->first();
    }

    public function toutes(): Collection
    {
        return Salle::query()->orderBy('nom')->get();
    }

    public function actives(): Collection
    {
        return Salle::query()
            ->where('active', true)
            ->orderBy('nom')
            ->get();
    }

    public function creer(array $donnees): Salle
    {
        return Salle::query()->create($donnees);
    }

    public function mettreAJour(Salle $salle, array $donnees): Salle
    {
        $salle->update($donnees);
        return $salle;
    }

    public function supprimer(Salle $salle): bool
    {
        return (bool) $salle->delete();
    }
}
