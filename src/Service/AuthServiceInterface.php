<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;

interface AuthServiceInterface
{
    public function authentifier(string $email, string $password): ?User;

    public function authentifierOrFail(string $email, string $password): User;
}
