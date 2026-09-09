<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        if (Capsule::schema()->hasTable('reservations')) {
            return;
        }
        Capsule::schema()->create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salle_id')->constrained('salles')->cascadeOnDelete();
            $table->string('responsable', 120);
            $table->string('email', 190);
            $table->string('motif', 255);
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->enum('statut', ['confirmee', 'annulee'])->default('confirmee');
            $table->timestamps();
            $table->index(['salle_id', 'statut', 'date_debut', 'date_fin'], 'idx_disponibilite');
        });
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('reservations');
    }
};
