<?php

declare(strict_types=1);

namespace App\DTO;

use App\Validator\ReservationValidator;

class CreerReservationDTO
{
    public readonly int $salleId;
    public readonly string $responsable;
    public readonly string $email;
    public readonly string $motif;
    public readonly \DateTimeImmutable $dateDebut;
    public readonly \DateTimeImmutable $dateFin;

    public function __construct(
        int $salleId,
        string $responsable,
        string $email,
        string $motif,
        \DateTimeImmutable $dateDebut,
        \DateTimeImmutable $dateFin
    ) {
        $this->salleId = $salleId;
        $this->responsable = $responsable;
        $this->email = $email;
        $this->motif = $motif;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
    }

    public static function depuisTableau(array $data): self
    {
        $validator = new ReservationValidator();
        $result = $validator->validate($data);

        if (!$result->estValide()) {
            throw new \InvalidArgumentException(
                'Donnees invalides: ' . implode(', ', $result->getErreurs())
            );
        }

        return new self(
            (int) $data['salle_id'],
            $data['responsable'],
            $data['email'],
            $data['motif'],
            new \DateTimeImmutable($data['date_debut']),
            new \DateTimeImmutable($data['date_fin'])
        );
    }

    public function toArray(): array
    {
        return [
            'salle_id' => $this->salleId,
            'responsable' => $this->responsable,
            'email' => $this->email,
            'motif' => $this->motif,
            'date_debut' => $this->dateDebut->format('Y-m-d H:i:s'),
            'date_fin' => $this->dateFin->format('Y-m-d H:i:s')
        ];
    }
}
