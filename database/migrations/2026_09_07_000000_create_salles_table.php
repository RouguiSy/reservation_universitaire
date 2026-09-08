<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        Capsule::schema()->create('salles', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->string('batiment', 100);
            $table->integer('capacite');
            $table->string('type', 50);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['nom', 'batiment']);
            $table->index('active');
        });
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('salles');
    }
};
