<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\UnauthorizedException;
use App\Model\User;
use App\Repository\EloquentUserRepository;
use App\Repository\UserRepositoryInterface;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private ?UserRepositoryInterface $userRepository = null
    ) {
        $this->userRepository = $userRepository ?? new EloquentUserRepository();
    }

    public function authentifier(string $email, string $password): ?User
    {
        $user = $this->userRepository->trouverParEmail($email);

        if (!$user || !password_verify($password, $user->password)) {
            return null;
        }

        return $user;
    }

    public function authentifierOrFail(string $email, string $password): User
    {
        $user = $this->authentifier($email, $password);

        if (!$user) {
            $msg = function_exists('message') ? message('auth.invalid_credentials', [], 'Email ou mot de passe incorrect.') : 'Email ou mot de passe incorrect.';
            throw new UnauthorizedException($msg);
        }

        return $user;
    }
}
