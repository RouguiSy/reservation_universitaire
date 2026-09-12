<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use App\Service\SalleService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class SalleServiceTest extends TestCase
{
    private SalleRepositoryInterface $repository;
    private SalleService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SalleRepositoryInterface::class);
        $this->service = new SalleService($this->repository);
    }

    public function testRechercherDelegatesToRepository(): void
    {
        $mockCollection = new Collection();
        $this->repository->expects($this->once())
            ->method('rechercher')
            ->with('Amphi', 'Bat A', 'amphitheatre', 1, 6)
            ->willReturn($mockCollection);

        $result = $this->service->rechercher('Amphi', 'Bat A', 'amphitheatre', 1, 6);
        $this->assertSame($mockCollection, $result);
    }

    public function testListerBatimentsReturnsUniqueSortedValues(): void
    {
        $salle1 = new Salle();
        $salle1->batiment = 'Bat B';
        $salle2 = new Salle();
        $salle2->batiment = 'Bat A';
        $salle3 = new Salle();
        $salle3->batiment = 'Bat B';

        $this->repository->expects($this->once())
            ->method('toutes')
            ->willReturn(new Collection([$salle1, $salle2, $salle3]));

        $batiments = $this->service->listerBatiments();
        $this->assertSame(['Bat A', 'Bat B'], $batiments->values()->all());
    }

    public function testTrouverDelegatesToRepository(): void
    {
        $salle = new Salle();
        $salle->id = 10;

        $this->repository->expects($this->once())
            ->method('trouver')
            ->with(10)
            ->willReturn($salle);

        $result = $this->service->trouver(10);
        $this->assertSame($salle, $result);
    }

    public function testCreerDelegatesToRepository(): void
    {
        $dto = new CreerSalleDTO(
            nom: 'Salle 101',
            batiment: 'Bat C',
            capacite: 40,
            type: 'cours',
            active: true
        );

        $salle = new Salle();
        $salle->nom = 'Salle 101';

        $this->repository->expects($this->once())
            ->method('creer')
            ->with($dto->toArray())
            ->willReturn($salle);

        $result = $this->service->creer($dto);
        $this->assertSame($salle, $result);
    }

    public function testSupprimerThrowsExceptionWhenNotFound(): void
    {
        $this->repository->expects($this->once())
            ->method('trouver')
            ->with(99)
            ->willReturn(null);

        $this->expectException(\App\Exception\NotFoundException::class);
        $this->service->supprimer(99);
    }

    public function testTrouverOrFailReturnsSalleWhenFound(): void
    {
        $salle = new Salle();
        $salle->id = 15;

        $this->repository->expects($this->once())
            ->method('trouver')
            ->with(15)
            ->willReturn($salle);

        $result = $this->service->trouverOrFail(15);
        $this->assertSame($salle, $result);
    }

    public function testTrouverOrFailThrowsExceptionWhenNotFound(): void
    {
        $this->repository->expects($this->once())
            ->method('trouver')
            ->with(999)
            ->willReturn(null);

        $this->expectException(\App\Exception\NotFoundException::class);
        $this->service->trouverOrFail(999);
    }
}
