<?php

declare(strict_types=1);

namespace App\Validation;

use DateTimeImmutable;
use Respect\Validation\Validator as v;

final class ReservationValidator extends AbstractValidator
{
    protected function messages(): array
    {
        return [
            'salle_id'    => "L'identifiant est invalide.",
            'responsable' => 'Le responsable est invalide.',
            'email'       => "L'adresse est invalide.",
            'motif'       => 'Le motif est invalide.',
            'date_debut'  => 'La date doit etre future.',
            'date_fin'    => 'La fin doit etre apres le debut.',
        ];
    }

    protected function rules(array $data): array
    {
        return [
            'salle_id'    => v::intVal()->positive(),
            'responsable' => v::stringType()->notEmpty()->length(2, 120),
            'email'       => v::email(),
            'motif'       => v::stringType()->notEmpty()->length(5, 255),
            'date_debut'  => v::callback(fn ($val) => ($d = $this->parseDate($val)) && $d > new DateTimeImmutable()),
            'date_fin'    => v::callback(fn ($val) => ($f = $this->parseDate($val)) && ($d = $this->parseDate($data['date_debut'] ?? null)) && $f > $d),
        ];
    }

    public function parseDate(mixed $value): ?DateTimeImmutable
    {
        if (!is_string($value)) {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
