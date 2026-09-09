<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class InMemorySalleRepository implements SalleRepositoryInterface
{
    private array $salles = [];
    private int $nextId = 1;

    public function trouver(int $id): ?Salle
    {
        return $this->salles[$id] ?? null;
    }

    public function trouverParNomEtBatiment(string $nom, string $batiment): ?Salle
    {
        foreach ($this->salles as $salle) {
            if ($salle->nom === $nom && $salle->batiment === $batiment) {
                return $salle;
            }
        }
        return null;
    }

    public function toutes(): Collection
    {
        return new Collection(array_values($this->salles));
    }

    public function rechercher(string $terme, string $batiment, string $type, int $page, int $parPage): LengthAwarePaginator
    {
        $items = array_filter($this->salles, static function (Salle $salle) use ($terme, $batiment, $type): bool {
            return ($terme === '' || stripos($salle->nom, $terme) !== false || stripos($salle->batiment, $terme) !== false)
                && ($batiment === '' || $salle->batiment === $batiment)
                && ($type === '' || $salle->type === $type);
        });
        $items = array_values($items);
        return new LengthAwarePaginator(array_slice($items, ($page - 1) * $parPage, $parPage), count($items), $parPage, $page);
    }

    public function actives(): Collection
    {
        $actives = array_filter($this->salles, fn($s) => $s->active);
        return new Collection(array_values($actives));
    }

    public function creer(array $donnees): Salle
    {
        $salle = new Salle();
        $salle->id = $this->nextId++;
        $salle->nom = $donnees['nom'];
        $salle->batiment = $donnees['batiment'];
        $salle->capacite = $donnees['capacite'];
        $salle->type = $donnees['type'];
        $salle->active = $donnees['active'] ?? true;
        $this->salles[$salle->id] = $salle;
        return $salle;
    }

    public function mettreAJour(Salle $salle, array $donnees): Salle
    {
        $salle->nom = $donnees['nom'] ?? $salle->nom;
        $salle->batiment = $donnees['batiment'] ?? $salle->batiment;
        $salle->capacite = $donnees['capacite'] ?? $salle->capacite;
        $salle->type = $donnees['type'] ?? $salle->type;
        $salle->active = $donnees['active'] ?? $salle->active;
        $this->salles[$salle->id] = $salle;
        return $salle;
    }

    public function supprimer(Salle $salle): bool
    {
        unset($this->salles[$salle->id]);
        return true;
    }
}
