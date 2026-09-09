<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Support\InMemoryReservationRepository;
use PHPUnit\Framework\TestCase;

class InMemoryReservationRepositoryTest extends TestCase
{
    private InMemoryReservationRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryReservationRepository();
    }

    public function testCreerEtTrouver(): void
    {
        $reservation = $this->repository->creer([
            'salle_id' => 1,
            'responsable' => 'Dr. Alpha',
            'email' => 'alpha@universite.sn',
            'motif' => 'Cours Algorithmique',
            'date_debut' => '+1 day 08:00:00',
            'date_fin' => '+1 day 10:00:00',
            'statut' => 'confirmee',
        ]);

        $this->assertSame(1, $reservation->id);
        $trouve = $this->repository->trouver($reservation->id);
        $this->assertNotNull($trouve);
        $this->assertSame('Dr. Alpha', $trouve->responsable);
    }

    public function testTrouverParSalle(): void
    {
        $this->repository->creer([
            'salle_id' => 1,
            'responsable' => 'Dr. A',
            'email' => 'a@universite.sn',
            'motif' => 'Cours 1',
            'date_debut' => '+1 day 08:00:00',
            'date_fin' => '+1 day 10:00:00',
        ]);

        $this->repository->creer([
            'salle_id' => 2,
            'responsable' => 'Dr. B',
            'email' => 'b@universite.sn',
            'motif' => 'Cours 2',
            'date_debut' => '+1 day 08:00:00',
            'date_fin' => '+1 day 10:00:00',
        ]);

        $reservationsSalle1 = $this->repository->trouverParSalle(1);
        $this->assertCount(1, $reservationsSalle1);
        $this->assertSame('Dr. A', $reservationsSalle1->first()->responsable);
    }

    public function testTrouverParSalleEtPeriodeDetecteConflits(): void
    {
        $this->repository->creer([
            'salle_id' => 1,
            'responsable' => 'Dr. A',
            'email' => 'a@universite.sn',
            'motif' => 'Cours existant',
            'date_debut' => '2026-10-15 09:00:00',
            'date_fin' => '2026-10-15 11:00:00',
            'statut' => 'confirmee',
        ]);

        // Chevauchement
        $conflits = $this->repository->trouverParSalleEtPeriode(
            1,
            new \DateTimeImmutable('2026-10-15 10:00:00'),
            new \DateTimeImmutable('2026-10-15 12:00:00')
        );
        $this->assertCount(1, $conflits);

        // Sans chevauchement (avant)
        $avant = $this->repository->trouverParSalleEtPeriode(
            1,
            new \DateTimeImmutable('2026-10-15 07:00:00'),
            new \DateTimeImmutable('2026-10-15 09:00:00')
        );
        $this->assertCount(0, $avant);

        // Sans chevauchement (apres)
        $apres = $this->repository->trouverParSalleEtPeriode(
            1,
            new \DateTimeImmutable('2026-10-15 11:00:00'),
            new \DateTimeImmutable('2026-10-15 13:00:00')
        );
        $this->assertCount(0, $apres);
    }

    public function testAnnuler(): void
    {
        $res = $this->repository->creer([
            'salle_id' => 1,
            'responsable' => 'Dr. Test',
            'email' => 'test@universite.sn',
            'motif' => 'Reunion',
            'date_debut' => '+1 day 10:00:00',
            'date_fin' => '+1 day 12:00:00',
            'statut' => 'confirmee',
        ]);

        $annulee = $this->repository->annuler($res);
        $this->assertSame('annulee', $annulee->statut);
        $this->assertSame('annulee', $this->repository->trouver($res->id)->statut);
    }

    public function testSupprimer(): void
    {
        $res = $this->repository->creer([
            'salle_id' => 1,
            'responsable' => 'Dr. Test',
            'email' => 'test@universite.sn',
            'motif' => 'A supprimer',
            'date_debut' => '+1 day 10:00:00',
            'date_fin' => '+1 day 12:00:00',
        ]);

        $this->assertTrue($this->repository->supprimer($res));
        $this->assertNull($this->repository->trouver($res->id));
    }
}
