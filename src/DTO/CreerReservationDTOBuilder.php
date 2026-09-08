<?php

declare(strict_types=1);

namespace App\DTO;

class CreerReservationDTOBuilder
{
    private ?int $salleId = null;
    private ?string $responsable = null;
    private ?string $email = null;
    private ?string $motif = null;
    private ?\DateTimeImmutable $dateDebut = null;
    private ?\DateTimeImmutable $dateFin = null;

    public function salleId(int $value): self { $this->salleId = $value; return $this; }
    public function responsable(string $value): self { $this->responsable = $value; return $this; }
    public function email(string $value): self { $this->email = $value; return $this; }
    public function motif(string $value): self { $this->motif = $value; return $this; }
    public function dateDebut(\DateTimeImmutable $value): self { $this->dateDebut = $value; return $this; }
    public function dateFin(\DateTimeImmutable $value): self { $this->dateFin = $value; return $this; }

    public function build(): CreerReservationDTO
    {
        if ($this->salleId === null || $this->responsable === null || $this->email === null || $this->motif === null || $this->dateDebut === null || $this->dateFin === null) {
            throw new \InvalidArgumentException('Tous les champs obligatoires de la reservation doivent etre renseignes');
        }

        return new CreerReservationDTO($this->salleId, $this->responsable, $this->email, $this->motif, $this->dateDebut, $this->dateFin);
    }
}
