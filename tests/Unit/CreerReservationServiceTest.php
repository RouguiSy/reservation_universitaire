<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Service\CreerReservationService;
use Tests\Support\InMemorySalleRepository;
use Tests\Support\InMemoryReservationRepository;
use PHPUnit\Framework\TestCase;

class CreerReservationServiceTest extends TestCase
{
    private CreerReservationService $service;
    private InMemorySalleRepository $salleRepository;
    private InMemoryReservationRepository $reservationRepository;

    protected function setUp(): void
    {
        $this->salleRepository = new InMemorySalleRepository();
        $this->reservationRepository = new InMemoryReservationRepository();
        $this->service = new CreerReservationService(
            $this->salleRepository,
            $this->reservationRepository
        );
    }

    public function testCreerReservationValide(): void
    {
        $salle = $this->salleRepository->creer([
            'nom' => 'Salle Test',
            'batiment' => 'Batiment A',
            'capacite' => 30,
            'type' => 'cours',
            'active' => true
        ]);

        $dto = new CreerReservationDTO(
            $salle->id,
            'Dr. Test',
            'test@universite.sn',
            'Cours de test',
            new \DateTimeImmutable('+1 day 09:00:00'),
            new \DateTimeImmutable('+1 day 11:00:00')
        );

        $reservation = $this->service->executer($dto);

        $this->assertNotNull($reservation);
        $this->assertEquals('confirmee', $reservation->statut);
        $this->assertEquals($salle->id, $reservation->salle_id);
    }

    public function testSalleNonTrouvee(): void
    {
        $this->expectException(SalleIndisponibleException::class);

        $dto = new CreerReservationDTO(
            999,
            'Dr. Test',
            'test@universite.sn',
            'Cours de test',
            new \DateTimeImmutable('+1 day 09:00:00'),
            new \DateTimeImmutable('+1 day 11:00:00')
        );

        $this->service->executer($dto);
    }

    public function testSalleInactive(): void
    {
        $salle = $this->salleRepository->creer([
            'nom' => 'Salle Inactive',
            'batiment' => 'Batiment A',
            'capacite' => 30,
            'type' => 'cours',
            'active' => false
        ]);

        $this->expectException(SalleIndisponibleException::class);

        $dto = new CreerReservationDTO(
            $salle->id,
            'Dr. Test',
            'test@universite.sn',
            'Cours de test',
            new \DateTimeImmutable('+1 day 09:00:00'),
            new \DateTimeImmutable('+1 day 11:00:00')
        );

        $this->service->executer($dto);
    }

    public function testDureeExcessive(): void
    {
        $salle = $this->salleRepository->creer([
            'nom' => 'Salle Test',
            'batiment' => 'Batiment A',
            'capacite' => 30,
            'type' => 'cours',
            'active' => true
        ]);

        $this->expectException(SalleIndisponibleException::class);

        $dto = new CreerReservationDTO(
            $salle->id,
            'Dr. Test',
            'test@universite.sn',
            'Cours de test',
            new \DateTimeImmutable('+1 day 09:00:00'),
            new \DateTimeImmutable('+2 day 11:00:00')
        );

        $this->service->executer($dto);
    }

    public function testConflit(): void
    {
        $salle = $this->salleRepository->creer([
            'nom' => 'Salle Test',
            'batiment' => 'Batiment A',
            'capacite' => 30,
            'type' => 'cours',
            'active' => true
        ]);

        $dto1 = new CreerReservationDTO(
            $salle->id,
            'Dr. Test1',
            'test1@universite.sn',
            'Cours 1',
            new \DateTimeImmutable('+1 day 09:00:00'),
            new \DateTimeImmutable('+1 day 11:00:00')
        );

        $this->service->executer($dto1);

        $this->expectException(SalleIndisponibleException::class);

        $dto2 = new CreerReservationDTO(
            $salle->id,
            'Dr. Test2',
            'test2@universite.sn',
            'Cours 2',
            new \DateTimeImmutable('+1 day 09:30:00'),
            new \DateTimeImmutable('+1 day 10:30:00')
        );

        $this->service->executer($dto2);
    }
}
