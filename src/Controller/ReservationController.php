<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDTO;
use App\Service\ReservationServiceInterface;
use App\Session\SessionManager;
use App\Session\SessionManagerInterface;

class ReservationController
{
    public function __construct(
        private ReservationServiceInterface $reservationService,
        private SessionManagerInterface $session = new SessionManager()
    ) {
    }

    public function index(): void
    {
        $salleId = isset($_GET['salle']) && ctype_digit((string) $_GET['salle']) ? (int) $_GET['salle'] : null;
        $reservations = $this->reservationService->lister($salleId);
        $salles = $this->reservationService->listerSallesActives();

        respond('reservations/index.php', [
            'reservations' => $reservations,
            'salles' => $salles,
            'salleId' => $salleId,
        ]);
    }

    public function create(): void
    {
        $salles = $this->reservationService->listerSallesActives();
        $errors = $this->session->getFormErrors('reservation');
        $old = $this->session->getFormOld('reservation');

        respond('reservations/form.php', [
            'salles' => $salles,
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function store(): void
    {
        $input = get_request_data();
        $dto = CreerReservationDTO::depuisTableau($input);
        $reservation = $this->reservationService->creer($dto);

        if (wantsJson()) {
            json_response([
                'status' => 'success',
                'message' => message('reservation.created'),
                'data' => $reservation,
            ], 201);
        }

        $this->session->flash('success', message('reservation.created'));
        header('Location: /reservations');
        exit;
    }

    public function cancel(int $id): void
    {
        $reservation = $this->reservationService->annuler($id);

        if (wantsJson()) {
            json_response([
                'status' => 'success',
                'message' => message('reservation.cancelled'),
                'data' => $reservation,
            ]);
        }

        $this->session->flash('success', message('reservation.cancelled'));
        header('Location: /reservations');
        exit;
    }

    public function show(int $id): void
    {
        $reservation = $this->reservationService->trouverOrFail($id);

        if (wantsJson()) {
            json_response([
                'status' => 'success',
                'data' => $reservation,
            ]);
        }

        respond('reservations/show.php', [
            'reservation' => $reservation,
        ]);
    }
}
