<?php

declare(strict_types=1);

namespace App\Security;

use App\Session\SessionManager;
use App\Session\SessionManagerInterface;

class CsrfService
{
    private const SESSION_KEY = 'csrf_token';

    private SessionManagerInterface $session;

    public function __construct(?SessionManagerInterface $session = null)
    {
        $this->session = $session ?? (function_exists('session') ? session() : new SessionManager());
    }

    public function getToken(): string
    {
        $token = $this->session->get(self::SESSION_KEY);
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->session->set(self::SESSION_KEY, $token);
        }

        return $token;
    }

    public function validate(?string $token): bool
    {
        $sessionToken = $this->session->get(self::SESSION_KEY);

        if (!is_string($sessionToken) || empty($token)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    public function regenerateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->session->set(self::SESSION_KEY, $token);
        return $token;
    }
}
