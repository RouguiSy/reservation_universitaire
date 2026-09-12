<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTO;
use App\Exception\ValidationException;
use App\Service\SalleServiceInterface;
use App\Session\SessionManager;
use App\Session\SessionManagerInterface;
use App\Validation\ValidatorFactory;

class SalleController
{
    public function __construct(
        private SalleServiceInterface $salleService,
        private SessionManagerInterface $session = new SessionManager()
    ) {
    }

    public function index(): void
    {
        $terme = trim((string) ($_GET['q'] ?? ''));
        $batiment = trim((string) ($_GET['batiment'] ?? ''));
        $type = trim((string) ($_GET['type'] ?? ''));
        $salles = $this->salleService->rechercher($terme, $batiment, $type, (int) ($_GET['page'] ?? 1), 6);
        $batiments = $this->salleService->listerBatiments();

        respond('salles/index.php', [
            'salles' => $salles,
            'batiments' => $batiments,
            'terme' => $terme,
            'batiment' => $batiment,
            'type' => $type,
        ]);
    }

    public function create(): void
    {
        $errors = $this->session->getFormErrors('salle');
        $old = $this->session->getFormOld('salle');

        respond('salles/form.php', [
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function store(): void
    {
        $input = get_request_data();
        $dto = CreerSalleDTO::depuisTableau($input);
        $salle = $this->salleService->creer($dto);

        if (wantsJson()) {
            json_response([
                'status' => 'success',
                'message' => message('salle.created'),
                'data' => $salle,
            ], 201);
        }

        $this->session->flash('success', message('salle.created'));
        header('Location: /salles');
        exit;
    }

    public function toggle(int $id): void
    {
        $salle = $this->salleService->basculerStatut($id);

        if (wantsJson()) {
            json_response([
                'status' => 'success',
                'message' => message('salle.status_updated'),
                'data' => $salle,
            ]);
        }

        $this->session->flash('success', message('salle.status_updated'));
        header('Location: /salles');
        exit;
    }

    public function delete(int $id): void
    {
        $this->salleService->supprimer($id);

        if (wantsJson()) {
            json_response([
                'status' => 'success',
                'message' => message('salle.deleted'),
            ]);
        }

        $this->session->flash('success', message('salle.deleted'));
        header('Location: /salles');
        exit;
    }

    public function show(int $id): void
    {
        $salle = $this->salleService->trouverOrFail($id);

        if (wantsJson()) {
            json_response([
                'status' => 'success',
                'data' => $salle,
            ]);
        }

        respond('salles/show.php', [
            'salle' => $salle,
        ]);
    }

    public function edit(int $id): void
    {
        $salle = $this->salleService->trouverOrFail($id);

        $errors = $this->session->getFormErrors('salle');
        $old = $this->session->getFormOld('salle');

        respond('salles/form.php', [
            'salle' => $salle,
            'errors' => $errors,
            'old' => empty($old) ? $salle->toArray() : $old,
        ]);
    }

    public function update(int $id): void
    {
        $salle = $this->salleService->trouverOrFail($id);

        $input = get_request_data();
        $validator = ValidatorFactory::create('salle');
        $result = $validator->validate($input);

        if (!$result->isValid()) {
            throw new ValidationException($result->errors());
        }

        $donnees = [
            'nom' => (string) $input['nom'],
            'batiment' => (string) $input['batiment'],
            'capacite' => (int) $input['capacite'],
            'type' => (string) $input['type'],
            'active' => isset($input['active']) ? (bool) $input['active'] : $salle->active,
        ];

        $salleModifiee = $this->salleService->mettreAJour($id, $donnees);

        if (wantsJson()) {
            json_response([
                'status' => 'success',
                'message' => message('salle.updated'),
                'data' => $salleModifiee,
            ]);
        }

        $this->session->flash('success', message('salle.updated'));
        header('Location: /salles');
        exit;
    }
}
