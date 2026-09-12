<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerSalleDTO;
use App\Exception\NotFoundException;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use Illuminate\Support\Collection;

class SalleService implements SalleServiceInterface
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository
    ) {
    }

    public function rechercher(string $terme = '', string $batiment = '', string $type = '', int $page = 1, int $perPage = 6): mixed
    {
        return $this->salleRepository->rechercher($terme, $batiment, $type, $page, $perPage);
    }

    public function listerBatiments(): Collection
    {
        return $this->salleRepository->toutes()->pluck('batiment')->unique()->sort()->values();
    }

    public function listerActives(): Collection
    {
        return $this->salleRepository->actives();
    }

    public function trouver(int $id): ?Salle
    {
        return $this->salleRepository->trouver($id);
    }

    public function trouverOrFail(int $id): Salle
    {
        $salle = $this->salleRepository->trouver($id);
        if (!$salle) {
            $msg = function_exists('message') ? message('salle.not_found', [], 'Salle non trouvee') : 'Salle non trouvee';
            throw new NotFoundException($msg);
        }

        return $salle;
    }

    public function creer(CreerSalleDTO $dto): Salle
    {
        return $this->salleRepository->creer($dto->toArray());
    }

    public function basculerStatut(int $id): Salle
    {
        $salle = $this->trouverOrFail($id);

        $salle->active = !$salle->active;
        $salle->save();

        return $salle;
    }

    public function supprimer(int $id): bool
    {
        $salle = $this->trouverOrFail($id);

        return $this->salleRepository->supprimer($salle);
    }

    public function mettreAJour(int $id, array $donnees): Salle
    {
        $salle = $this->trouverOrFail($id);

        return $this->salleRepository->mettreAJour($salle, $donnees);
    }
}
