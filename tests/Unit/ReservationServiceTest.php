<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDTO;
use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationServiceInterface;
use App\Service\CreerReservationServiceInterface;
use App\Service\ReservationService;
use DateTimeImmutable;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class ReservationServiceTest extends TestCase
{
    private ReservationRepositoryInterface $reservationRepo;
    private SalleRepositoryInterface $salleRepo;
    private CreerReservationServiceInterface $creerService;
    private AnnulerReservationServiceInterface $annulerService;
    private ReservationService $service;

    protected function setUp(): void
    {
        $this->reservationRepo = $this->createMock(ReservationRepositoryInterface::class);
        $this->salleRepo = $this->createMock(SalleRepositoryInterface::class);
        $this->creerService = $this->createMock(CreerReservationServiceInterface::class);
        $this->annulerService = $this->createMock(AnnulerReservationServiceInterface::class);

        $this->service = new ReservationService(
            $this->reservationRepo,
            $this->salleRepo,
            $this->creerService,
            $this->annulerService
        );
    }

    public function testListerToutesReservationsQuandSalleIdEstNull(): void
    {
        $reservations = new Collection();
        $this->reservationRepo->expects($this->once())
            ->method('toutes')
            ->willReturn($reservations);

        $result = $this->service->lister(null);
        $this->assertSame($reservations, $result);
    }

    public function testListerReservationsParSalle(): void
    {
        $reservations = new Collection();
        $this->reservationRepo->expects($this->once())
            ->method('trouverParSalle')
            ->with(5)
            ->willReturn($reservations);

        $result = $this->service->lister(5);
        $this->assertSame($reservations, $result);
    }

    public function testListerSallesActives(): void
    {
        $salles = new Collection([new Salle()]);
        $this->salleRepo->expects($this->once())
            ->method('actives')
            ->willReturn($salles);

        $result = $this->service->listerSallesActives();
        $this->assertSame($salles, $result);
    }

    public function testCreerDelegatesToCreerService(): void
    {
        $dto = new CreerReservationDTO(
            salleId: 1,
            responsable: 'Alice Dupont',
            email: 'alice@example.com',
            motif: 'Reunion projet',
            dateDebut: new DateTimeImmutable('2026-09-12 10:00'),
            dateFin: new DateTimeImmutable('2026-09-12 11:00')
        );

        $reservation = new Reservation();
        $this->creerService->expects($this->once())
            ->method('executer')
            ->with($dto)
            ->willReturn($reservation);

        $result = $this->service->creer($dto);
        $this->assertSame($reservation, $result);
    }

    public function testAnnulerDelegatesToAnnulerService(): void
    {
        $reservation = new Reservation();
        $this->annulerService->expects($this->once())
            ->method('executer')
            ->with(42)
            ->willReturn($reservation);

        $result = $this->service->annuler(42);
        $this->assertSame($reservation, $result);
    }

    public function testTrouverOrFailReturnsReservationWhenFound(): void
    {
        $reservation = new Reservation();
        $reservation->id = 7;

        $this->reservationRepo->expects($this->once())
            ->method('trouver')
            ->with(7)
            ->willReturn($reservation);

        $result = $this->service->trouverOrFail(7);
        $this->assertSame($reservation, $result);
    }

    public function testTrouverOrFailThrowsExceptionWhenNotFound(): void
    {
        $this->reservationRepo->expects($this->once())
            ->method('trouver')
            ->with(999)
            ->willReturn(null);

        $this->expectException(\App\Exception\NotFoundException::class);
        $this->service->trouverOrFail(999);
    }
}
