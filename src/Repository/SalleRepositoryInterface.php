<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SalleRepositoryInterface
{
    public function trouver(int $id): ?Salle;
    public function trouverParNomEtBatiment(string $nom, string $batiment): ?Salle;
    public function toutes(): Collection;
    public function rechercher(string $terme, string $batiment, string $type, int $page, int $parPage): LengthAwarePaginator;
    public function actives(): Collection;
    public function creer(array $donnees): Salle;
    public function mettreAJour(Salle $salle, array $donnees): Salle;
    public function supprimer(Salle $salle): bool;
}
