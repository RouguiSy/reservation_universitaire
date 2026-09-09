<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Service\AnnulerReservationService;
use Tests\Support\InMemoryReservationRepository;
use PHPUnit\Framework\TestCase;

class AnnulerReservationServiceTest extends TestCase
{
    private AnnulerReservationService $service;
    private InMemoryReservationRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryReservationRepository();
        $this->service = new AnnulerReservationService($this->repository);
    }

    public function testAnnulerReservationValide(): void
    {
        $reservation = $this->repository->creer([
            'salle_id' => 1,
            'responsable' => 'Dr. Test',
            'email' => 'test@universite.sn',
            'motif' => 'Cours de test',
            'date_debut' => '+1 day 09:00:00',
            'date_fin' => '+1 day 11:00:00',
            'statut' => 'confirmee'
        ]);

        $result = $this->service->executer($reservation->id);

        $this->assertEquals('annulee', $result->statut);
        $this->assertEquals('annulee', $this->repository->trouver($reservation->id)->statut);
    }

    public function testReservationNonTrouvee(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Reservation non trouvee');
        $this->service->executer(999);
    }

    public function testReservationDejaAnnulee(): void
    {
        $reservation = $this->repository->creer([
            'salle_id' => 1,
            'responsable' => 'Dr. Test',
            'email' => 'test@universite.sn',
            'motif' => 'Cours de test',
            'date_debut' => '+1 day 09:00:00',
            'date_fin' => '+1 day 11:00:00',
            'statut' => 'annulee'
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cette reservation est deja annulee');
        $this->service->executer($reservation->id);
    }
}
