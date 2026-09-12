<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Reservation;
use App\Model\Salle;

class DashboardService implements DashboardServiceInterface
{

    public function obtenirStatistiques(): array
    {
        $totalSalles = Salle::query()->count();
        $sallesActives = Salle::query()->where('active', true)->count();
        $totalReservations = Reservation::query()->where('statut', 'confirmee')->count();

        $topSalles = Salle::query()
            ->leftJoin('reservations', function ($join): void {
                $join->on('salles.id', '=', 'reservations.salle_id')->where('reservations.statut', '=', 'confirmee');
            })
            ->selectRaw('salles.id, salles.nom, salles.batiment, COUNT(reservations.id) as reservations_count')
            ->groupBy('salles.id', 'salles.nom', 'salles.batiment')
            ->orderByDesc('reservations_count')
            ->orderBy('salles.nom')
            ->limit(10)
            ->get();

        return [
            'totalSalles'       => $totalSalles,
            'sallesActives'     => $sallesActives,
            'totalReservations' => $totalReservations,
            'topSalles'         => $topSalles,
        ];
    }
}
