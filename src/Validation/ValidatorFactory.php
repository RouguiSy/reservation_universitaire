<?php

declare(strict_types=1);

namespace App\Validation;

use InvalidArgumentException;

class ValidatorFactory
{
    /**
     * @var array<string, class-string<ValidatorInterface>>
     */
    private static array $registry = [
        'salle'       => SalleValidator::class,
        'reservation' => ReservationValidator::class,
    ];

    /**
     * Crée une instance de ValidatorInterface selon le type demandé.
     *
     * @throws InvalidArgumentException si le type n'est pas enregistré
     */
    public static function create(string $type): ValidatorInterface
    {
        $key = strtolower(trim($type));

        if (!isset(self::$registry[$key])) {
            $known = implode(', ', array_keys(self::$registry));
            throw new InvalidArgumentException("Validateur inconnu pour le type '{$type}'. Types disponibles : {$known}");
        }

        $class = self::$registry[$key];
        return new $class();
    }

    /**
     * Enregistre un nouveau validateur dans la fabrique (Open/Closed Principle).
     *
     * @param class-string<ValidatorInterface> $class
     */
    public static function register(string $type, string $class): void
    {
        self::$registry[strtolower(trim($type))] = $class;
    }
}
