<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use Illuminate\Database\Capsule\Manager as Capsule;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private ?Capsule $capsule = null
    ) {
    }

    public function trouverParEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function trouver(int $id): ?User
    {
        return User::query()->find($id);
    }
}
