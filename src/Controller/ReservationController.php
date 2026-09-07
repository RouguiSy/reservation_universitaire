<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SalleRepositoryInterface;
use App\Repository\ReservationRepositoryInterface;
use App\Service\CreerReservationService;
use App\Service\AnnulerReservationService;
use App\DTO\CreerReservationDTO;

class ReservationController
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository,
        private ReservationRepositoryInterface $reservationRepository,
        private CreerReservationService $creerService,
        private AnnulerReservationService $annulerService
    ) {
    }

    public function index(): void
    {
        $salleId = $_GET['salle'] ?? null;
        if ($salleId) {
            $reservations = $this->reservationRepository->trouverParSalle((int) $salleId);
        } else {
            $reservations = $this->reservationRepository->trouverParSalle(0);
        }
        $salles = $this->salleRepository->actives();
        require_once dirname(__DIR__, 2) . '/templates/reservations/index.php';
    }

    public function create(): void
    {
        $salles = $this->salleRepository->actives();
        require_once dirname(__DIR__, 2) . '/templates/reservations/create.php';
    }

    public function store(): void
    {
        try {
            $dto = CreerReservationDTO::depuisTableau($_POST);
            $this->creerService->executer($dto);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Reservation creee avec succes'];
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
        }

        header('Location: /reservations');
        exit;
    }

    public function cancel(int $id): void
    {
        try {
            $this->annulerService->executer($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Reservation annulee avec succes'];
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
        }

        header('Location: /reservations');
        exit;
    }
}
