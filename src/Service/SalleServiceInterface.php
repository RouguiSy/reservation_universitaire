<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;
use Illuminate\Support\Collection;

interface SalleServiceInterface
{
    public function rechercher(string $terme = '', string $batiment = '', string $type = '', int $page = 1, int $perPage = 6): mixed;

    public function listerBatiments(): Collection;

    public function listerActives(): Collection;

    public function trouver(int $id): ?Salle;

    public function trouverOrFail(int $id): Salle;

    public function creer(CreerSalleDTO $dto): Salle;

    public function basculerStatut(int $id): Salle;

    public function supprimer(int $id): bool;

    public function mettreAJour(int $id, array $donnees): Salle;
}
