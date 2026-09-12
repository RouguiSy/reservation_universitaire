<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;

interface UserRepositoryInterface
{
    public function trouverParEmail(string $email): ?User;

    public function trouver(int $id): ?User;
}
