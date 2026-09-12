<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSalleRepository implements SalleRepositoryInterface
{
    public function __construct(
        private ?Capsule $capsule = null
    ) {
    }
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

    public function rechercher(string $terme, string $batiment, string $type, int $page, int $parPage): LengthAwarePaginator
    {
        $query = Salle::query()->orderBy('nom');
        if ($terme !== '') $query->where(function ($builder) use ($terme): void { $builder->where('nom', 'like', "%{$terme}%")->orWhere('batiment', 'like', "%{$terme}%"); });
        if ($batiment !== '') $query->where('batiment', $batiment);
        if ($type !== '') $query->where('type', $type);
        return $query->paginate($parPage, ['*'], 'page', max(1, $page));
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
