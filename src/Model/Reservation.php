<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'salle_id', 'responsable', 'email', 'motif',
        'date_debut', 'date_fin', 'statut'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime'
    ];

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function estConfirmee(): bool
    {
        return $this->statut === 'confirmee';
    }

    public function estAnnulee(): bool
    {
        return $this->statut === 'annulee';
    }

    public function annuler(): void
    {
        $this->statut = 'annulee';
        $this->save();
    }

    public function confirmer(): void
    {
        $this->statut = 'confirmee';
        $this->save();
    }

    public function chevaucheAvec(Reservation $autre): bool
    {
        return $this->date_debut < $autre->date_fin &&
                $this->date_fin > $autre->date_debut;
    }

    public function getDureeEnHeures(): float
    {
        return $this->date_debut->diffInHours($this->date_fin, true);
    }
}