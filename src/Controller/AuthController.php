<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;

class AuthController
{
    public function login(): void
    {
        $errors = $_SESSION['auth_errors'] ?? [];
        $old = $_SESSION['auth_old'] ?? [];
        unset($_SESSION['auth_errors'], $_SESSION['auth_old']);
        require_once dirname(__DIR__, 2) . '/templates/auth/login.php';
    }

    public function authenticate(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $user = User::query()->where('email', $email)->first();
        if (!$user || !password_verify($password, $user->password)) {
            $_SESSION['auth_errors'] = ['global' => 'Email ou mot de passe incorrect.'];
            $_SESSION['auth_old'] = ['email' => $email];
            header('Location: /login');
            exit;
        }
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int) $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role];
        header('Location: ' . ($user->isAdmin() ? '/dashboard' : '/reservations'));
        exit;
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
        header('Location: /login');
        exit;
    }
}
