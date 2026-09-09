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

    public function testReservationContigueEstAutorisee(): void
    {
        $salle = $this->salleRepository->creer([
            'nom' => 'Salle Contigue',
            'batiment' => 'Batiment B',
            'capacite' => 40,
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

        $dto2 = new CreerReservationDTO(
            $salle->id,
            'Dr. Test2',
            'test2@universite.sn',
            'Cours 2',
            new \DateTimeImmutable('+1 day 11:00:00'),
            new \DateTimeImmutable('+1 day 13:00:00')
        );

        $reservation2 = $this->service->executer($dto2);

        $this->assertNotNull($reservation2);
        $this->assertEquals('confirmee', $reservation2->statut);
    }

    public function testReservationAnnuleeNeCreePasDeConflit(): void
    {
        $salle = $this->salleRepository->creer([
            'nom' => 'Salle Test',
            'batiment' => 'Batiment C',
            'capacite' => 25,
            'type' => 'reunion',
            'active' => true
        ]);

        $this->reservationRepository->creer([
            'salle_id' => $salle->id,
            'responsable' => 'Dr. Annule',
            'email' => 'annule@universite.sn',
            'motif' => 'Reunion annulee',
            'date_debut' => '+1 day 14:00:00',
            'date_fin' => '+1 day 16:00:00',
            'statut' => 'annulee'
        ]);

        $dto = new CreerReservationDTO(
            $salle->id,
            'Dr. Nouveau',
            'nouveau@universite.sn',
            'Nouvelle reunion',
            new \DateTimeImmutable('+1 day 14:00:00'),
            new \DateTimeImmutable('+1 day 16:00:00')
        );

        $reservation = $this->service->executer($dto);

        $this->assertNotNull($reservation);
        $this->assertEquals('confirmee', $reservation->statut);
    }

    public function testReservationMemeCreneauAutreSalleEstAutorisee(): void
    {
        $salle1 = $this->salleRepository->creer([
            'nom' => 'Salle 1',
            'batiment' => 'Batiment A',
            'capacite' => 30,
            'type' => 'cours',
            'active' => true
        ]);

        $salle2 = $this->salleRepository->creer([
            'nom' => 'Salle 2',
            'batiment' => 'Batiment A',
            'capacite' => 30,
            'type' => 'cours',
            'active' => true
        ]);

        $debut = new \DateTimeImmutable('+1 day 10:00:00');
        $fin = new \DateTimeImmutable('+1 day 12:00:00');

        $dto1 = new CreerReservationDTO($salle1->id, 'Dr. A', 'a@universite.sn', 'Cours A', $debut, $fin);
        $dto2 = new CreerReservationDTO($salle2->id, 'Dr. B', 'b@universite.sn', 'Cours B', $debut, $fin);

        $res1 = $this->service->executer($dto1);
        $res2 = $this->service->executer($dto2);

        $this->assertEquals('confirmee', $res1->statut);
        $this->assertEquals('confirmee', $res2->statut);
    }
}
