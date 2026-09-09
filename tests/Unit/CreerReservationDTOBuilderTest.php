<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDTO;
use App\DTO\CreerReservationDTOBuilder;
use PHPUnit\Framework\TestCase;

class CreerReservationDTOBuilderTest extends TestCase
{
    public function testBuildValide(): void
    {
        $debut = new \DateTimeImmutable('+1 day 08:00:00');
        $fin = new \DateTimeImmutable('+1 day 10:00:00');

        $dto = (new CreerReservationDTOBuilder())
            ->salleId(1)
            ->responsable('Pr. Dupont')
            ->email('dupont@universite.sn')
            ->motif('Soutenance')
            ->dateDebut($debut)
            ->dateFin($fin)
            ->build();

        $this->assertInstanceOf(CreerReservationDTO::class, $dto);
        $this->assertSame(1, $dto->salleId);
        $this->assertSame('Pr. Dupont', $dto->responsable);
        $this->assertSame('dupont@universite.sn', $dto->email);
        $this->assertSame('Soutenance', $dto->motif);
        $this->assertSame($debut, $dto->dateDebut);
        $this->assertSame($fin, $dto->dateFin);
    }

    public function testBuildIncompletLanceException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Tous les champs obligatoires de la reservation doivent etre renseignes');

        (new CreerReservationDTOBuilder())
            ->salleId(1)
            ->responsable('Pr. Dupont')
            ->build();
    }

    public function testToArray(): void
    {
        $debut = new \DateTimeImmutable('2026-10-15 08:00:00');
        $fin = new \DateTimeImmutable('2026-10-15 10:00:00');

        $dto = (new CreerReservationDTOBuilder())
            ->salleId(5)
            ->responsable('Pr. Martin')
            ->email('martin@universite.sn')
            ->motif('Examen semestriel')
            ->dateDebut($debut)
            ->dateFin($fin)
            ->build();

        $tableau = $dto->toArray();

        $this->assertEquals([
            'salle_id' => 5,
            'responsable' => 'Pr. Martin',
            'email' => 'martin@universite.sn',
            'motif' => 'Examen semestriel',
            'date_debut' => '2026-10-15 08:00:00',
            'date_fin' => '2026-10-15 10:00:00'
        ], $tableau);
    }
}
