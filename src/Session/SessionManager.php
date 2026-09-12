<?php

declare(strict_types=1);

namespace App\Session;


class SessionManager implements SessionManagerInterface
{
    public function start(array $options = []): void
    {
        if ($this->isStarted()) {
            return;
        }

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

        $defaultOptions = [
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ];

        session_set_cookie_params(array_merge($defaultOptions, $options));
        session_start();
    }

    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    private function ensureStarted(): void
    {
        if (!$this->isStarted()) {
            $this->start();
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->ensureStarted();
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        $this->ensureStarted();
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    public function clear(): void
    {
        $this->ensureStarted();
        $_SESSION = [];
    }

    public function all(): array
    {
        $this->ensureStarted();
        return $_SESSION;
    }

    public function regenerate(bool $deleteOldSession = true): bool
    {
        $this->ensureStarted();
        return session_regenerate_id($deleteOldSession);
    }

    public function destroy(): void
    {
        if (!$this->isStarted()) {
            return;
        }

        $this->clear();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    // --- Messages Flash ---

    public function flash(string $type, string $message): void
    {
        $this->set('flash', [
            'type' => $type,
            'message' => $message,
        ]);
    }

    public function setFlash(string $type, string $message): void
    {
        $this->flash($type, $message);
    }

    public function getFlash(): ?array
    {
        $flash = $this->get('flash');
        $this->remove('flash');
        return is_array($flash) ? $flash : null;
    }

    public function hasFlash(): bool
    {
        return $this->has('flash');
    }

    // --- Authentification / Utilisateur ---

    public function setUser(array $user): void
    {
        $this->set('user', $user);
    }

    public function getUser(): ?array
    {
        $user = $this->get('user');
        return is_array($user) ? $user : null;
    }

    public function hasUser(): bool
    {
        return $this->has('user');
    }

    public function removeUser(): void
    {
        $this->remove('user');
    }

    public function isAdmin(): bool
    {
        return ($this->getUser()['role'] ?? null) === 'admin';
    }

    // --- Formulaires : erreurs et anciennes valeurs ---

    public function setFormErrors(string $form, array $errors): void
    {
        $all = $this->get('form_errors', []);
        $all[$form] = $errors;
        $this->set('form_errors', $all);
    }

    public function getFormErrors(string $form): array
    {
        $all = $this->get('form_errors', []);
        $errors = $all[$form] ?? [];
        unset($all[$form]);
        $this->set('form_errors', $all);
        return is_array($errors) ? $errors : [];
    }

    public function setFormOld(string $form, array $old): void
    {
        $all = $this->get('form_old', []);
        $all[$form] = $old;
        $this->set('form_old', $all);
    }

    public function getFormOld(string $form): array
    {
        $all = $this->get('form_old', []);
        $old = $all[$form] ?? [];
        unset($all[$form]);
        $this->set('form_old', $all);
        return is_array($old) ? $old : [];
    }
}
