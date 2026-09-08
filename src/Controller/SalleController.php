<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SalleRepositoryInterface;
use App\DTO\CreerSalleDTO;
use App\Exception\ValidationException;

class SalleController
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository
    ) {
    }

    public function index(): void
    {
        $salles = $this->salleRepository->toutes();
        require_once dirname(__DIR__, 2) . '/templates/salles/index.php';
    }

    public function create(): void
    {
        $errors = $_SESSION['form_errors']['salle'] ?? [];
        $old = $_SESSION['form_old']['salle'] ?? [];
        unset($_SESSION['form_errors']['salle'], $_SESSION['form_old']['salle']);
        require_once dirname(__DIR__, 2) . '/templates/salles/create.php';
    }

    public function store(): void
    {
        try {
            $dto = CreerSalleDTO::depuisTableau($_POST);
            $this->salleRepository->creer($dto->toArray());
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Salle creee avec succes'];
        } catch (ValidationException $e) {
            $_SESSION['form_errors']['salle'] = $e->getErreurs();
            $_SESSION['form_old']['salle'] = $_POST;
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Veuillez corriger les champs signales.'];
            header('Location: /salles/create');
            exit;
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
        }

        header('Location: /salles');
        exit;
    }

    public function toggle(int $id): void
    {
        $salle = $this->salleRepository->trouver($id);

        if ($salle) {
            $salle->active = !$salle->active;
            $salle->save();
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statut modifie avec succes'];
        }

        header('Location: /salles');
        exit;
    }

    public function delete(int $id): void
    {
        $salle = $this->salleRepository->trouver($id);

        if ($salle) {
            $this->salleRepository->supprimer($salle);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Salle supprimee avec succes'];
        }

        header('Location: /salles');
        exit;
    }
}
