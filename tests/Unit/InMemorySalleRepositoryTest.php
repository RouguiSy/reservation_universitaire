<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Support\InMemorySalleRepository;
use PHPUnit\Framework\TestCase;

class InMemorySalleRepositoryTest extends TestCase
{
    private InMemorySalleRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemorySalleRepository();
    }

    public function testCreerEtTrouver(): void
    {
        $salle = $this->repository->creer([
            'nom' => 'Salle 101',
            'batiment' => 'Batiment A',
            'capacite' => 30,
            'type' => 'cours',
            'active' => true,
        ]);

        $this->assertSame(1, $salle->id);
        $trouve = $this->repository->trouver($salle->id);
        $this->assertNotNull($trouve);
        $this->assertSame('Salle 101', $trouve->nom);
    }

    public function testTrouverInexistantRetourneNull(): void
    {
        $this->assertNull($this->repository->trouver(999));
    }

    public function testTrouverParNomEtBatiment(): void
    {
        $this->repository->creer([
            'nom' => 'Salle 102',
            'batiment' => 'Batiment B',
            'capacite' => 20,
            'type' => 'reunion',
            'active' => true,
        ]);

        $trouve = $this->repository->trouverParNomEtBatiment('Salle 102', 'Batiment B');
        $this->assertNotNull($trouve);
        $this->assertSame(20, $trouve->capacite);

        $inexistant = $this->repository->trouverParNomEtBatiment('Salle 102', 'Batiment Inconnu');
        $this->assertNull($inexistant);
    }

    public function testActivesFiltreCorrectement(): void
    {
        $this->repository->creer([
            'nom' => 'Salle Active',
            'batiment' => 'Batiment A',
            'capacite' => 30,
            'type' => 'cours',
            'active' => true,
        ]);

        $this->repository->creer([
            'nom' => 'Salle Inactive',
            'batiment' => 'Batiment A',
            'capacite' => 30,
            'type' => 'cours',
            'active' => false,
        ]);

        $toutes = $this->repository->toutes();
        $actives = $this->repository->actives();

        $this->assertCount(2, $toutes);
        $this->assertCount(1, $actives);
        $this->assertSame('Salle Active', $actives->first()->nom);
    }

    public function testMettreAJour(): void
    {
        $salle = $this->repository->creer([
            'nom' => 'Ancien Nom',
            'batiment' => 'Batiment A',
            'capacite' => 25,
            'type' => 'cours',
            'active' => true,
        ]);

        $salleModifiee = $this->repository->mettreAJour($salle, [
            'nom' => 'Nouveau Nom',
            'capacite' => 50,
        ]);

        $this->assertSame('Nouveau Nom', $salleModifiee->nom);
        $this->assertSame(50, $salleModifiee->capacite);
        $this->assertSame('Batiment A', $salleModifiee->batiment);
    }

    public function testSupprimer(): void
    {
        $salle = $this->repository->creer([
            'nom' => 'A Supprimer',
            'batiment' => 'Batiment X',
            'capacite' => 10,
            'type' => 'reunion',
            'active' => true,
        ]);

        $supprime = $this->repository->supprimer($salle);
        $this->assertTrue($supprime);
        $this->assertNull($this->repository->trouver($salle->id));
    }
}
