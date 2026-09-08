<?php
declare(strict_types=1);
namespace App\DTO;
class CreerSalleDTOBuilder
{
    private ?string $nom = null;
    private ?string $batiment = null;
    private ?int $capacite = null;
    private ?string $type = null;
    private bool $active = true;
    public function nom(string $nom): self { $this->nom = $nom; return $this; }
    public function batiment(string $batiment): self { $this->batiment = $batiment; return $this; }
    public function capacite(int $capacite): self { $this->capacite = $capacite; return $this; }
    public function type(string $type): self { $this->type = $type; return $this; }
    public function active(bool $active): self { $this->active = $active; return $this; }
    public function build(): CreerSalleDTO
    {
        if ($this->nom === null || $this->batiment === null || $this->capacite === null || $this->type === null) {
            throw new \InvalidArgumentException('Tous les champs obligatoires de la salle doivent etre renseignes');
        }
        return new CreerSalleDTO($this->nom, $this->batiment, $this->capacite, $this->type, $this->active);
    }
}