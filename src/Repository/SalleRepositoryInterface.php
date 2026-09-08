<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Database\Eloquent\Collection;

interface SalleRepositoryInterface
{
    public function trouver(int $id): ?Salle;
    public function trouverParNomEtBatiment(string $nom, string $batiment): ?Salle;
    public function toutes(): Collection;
    public function actives(): Collection;
    public function creer(array $donnees): Salle;
    public function mettreAJour(Salle $salle, array $donnees): Salle;
    public function supprimer(Salle $salle): bool;
}
