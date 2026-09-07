<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salle extends Model
{
    protected $fillable = [
        'nom', 'batiment', 'capacite', 'type', 'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'capacite' => 'integer'
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
