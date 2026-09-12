<?php

declare(strict_types=1);

namespace App\Exception;

class SalleIndisponibleException extends BusinessException
{
    public function __construct(string $message = '', int $statusCode = 409, ?\Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, [], $previous);
    }

    public static function conflit(\DateTimeInterface $debut, \DateTimeInterface $fin): self
    {
        $message = function_exists('message')
            ? message('salle.conflict_dates', [
                ':debut' => $debut->format('d/m/Y H:i'),
                ':fin'   => $fin->format('d/m/Y H:i'),
            ], sprintf('La salle est deja reservee du %s au %s', $debut->format('d/m/Y H:i'), $fin->format('d/m/Y H:i')))
            : sprintf('La salle est deja reservee du %s au %s', $debut->format('d/m/Y H:i'), $fin->format('d/m/Y H:i'));

        return new self($message, 409);
    }

    public static function salleInactive(): self
    {
        $message = function_exists('message')
            ? message('salle.inactive', [], 'Cette salle n\'est pas active')
            : 'Cette salle n\'est pas active';

        return new self($message, 422);
    }

    public static function dureeExcessive(): self
    {
        $message = function_exists('message')
            ? message('salle.duration_exceeded', [], 'La duree de reservation ne peut pas depasser 24 heures')
            : 'La duree de reservation ne peut pas depasser 24 heures';

        return new self($message, 422);
    }

    public static function salleNonTrouvee(): self
    {
        $message = function_exists('message')
            ? message('salle.not_found', [], 'La salle n\'existe pas')
            : 'La salle n\'existe pas';

        return new self($message, 404);
    }
}
