<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SalleRepositoryInterface;
use App\Repository\ReservationRepositoryInterface;
use App\Service\CreerReservationService;
use App\Service\AnnulerReservationService;
use App\DTO\CreerReservationDTO;
use App\Exception\ValidationException;

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
        $reservations = $salleId
            ? $this->reservationRepository->trouverParSalle((int) $salleId)
            : $this->reservationRepository->toutes();
        $salles = $this->salleRepository->actives();
        require_once dirname(__DIR__, 2) . '/templates/reservations/index.php';
    }

    public function create(): void
    {
        $salles = $this->salleRepository->actives();
        $errors = $_SESSION['form_errors']['reservation'] ?? [];
        $old = $_SESSION['form_old']['reservation'] ?? [];
        unset($_SESSION['form_errors']['reservation'], $_SESSION['form_old']['reservation']);
        require_once dirname(__DIR__, 2) . '/templates/reservations/create.php';
    }

    public function store(): void
    {
        try {
            $dto = CreerReservationDTO::depuisTableau($_POST);
            $this->creerService->executer($dto);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Reservation creee avec succes'];
        } catch (ValidationException $e) {
            $_SESSION['form_errors']['reservation'] = $e->getErreurs();
            $_SESSION['form_old']['reservation'] = $_POST;
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Veuillez corriger les champs signales.'];
            header('Location: /reservations/create');
            exit;
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
