<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\CsrfService;
use App\Service\AuthService;
use App\Service\AuthServiceInterface;
use App\Session\SessionManager;
use App\Session\SessionManagerInterface;

class AuthController
{
    public function __construct(
        private ?AuthServiceInterface $authService = null,
        private SessionManagerInterface $session = new SessionManager(),
        private CsrfService $csrfService = new CsrfService()
    ) {
        $this->authService = $authService ?? new AuthService();
    }

    public function login(): void
    {
        $errors = $this->session->getFormErrors('auth');
        $old = $this->session->getFormOld('auth');

        respond('auth/login.php', [
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function authenticate(): void
    {
        $input = get_request_data();
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        $user = $this->authService->authentifierOrFail($email, $password);

        $this->session->regenerate(true);
        $this->csrfService->regenerateToken();
        $userData = ['id' => (int) $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role];
        $this->session->setUser($userData);

        if (wantsJson()) {
            json_response([
                'status' => 'success',
                'message' => message('auth.login_success'),
                'user' => $userData,
            ]);
        }

        header('Location: ' . ($user->isAdmin() ? '/dashboard' : '/reservations'));
        exit;
    }

    public function logout(): void
    {
        $this->session->removeUser();
        $this->session->regenerate(true);
        $this->csrfService->regenerateToken();

        if (wantsJson()) {
            json_response([
                'status' => 'success',
                'message' => message('auth.logout_success'),
            ]);
        }

        header('Location: /login');
        exit;
    }
}
