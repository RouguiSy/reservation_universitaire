<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerSalleDTO;
use App\DTO\CreerSalleDTOBuilder;
use PHPUnit\Framework\TestCase;

class CreerSalleDTOBuilderTest extends TestCase
{
    public function testBuildValide(): void
    {
        $dto = (new CreerSalleDTOBuilder())
            ->nom('Amphi A')
            ->batiment('Batiment Principal')
            ->capacite(150)
            ->type('amphitheatre')
            ->active(true)
            ->build();

        $this->assertInstanceOf(CreerSalleDTO::class, $dto);
        $this->assertSame('Amphi A', $dto->nom);
        $this->assertSame('Batiment Principal', $dto->batiment);
        $this->assertSame(150, $dto->capacite);
        $this->assertSame('amphitheatre', $dto->type);
        $this->assertTrue($dto->active);
    }

    public function testValeurParDefautActiveEstTrue(): void
    {
        $dto = (new CreerSalleDTOBuilder())
            ->nom('Salle 101')
            ->batiment('Batiment B')
            ->capacite(30)
            ->type('cours')
            ->build();

        $this->assertTrue($dto->active);
    }

    public function testBuildSansNomLanceException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Tous les champs obligatoires de la salle doivent etre renseignes');

        (new CreerSalleDTOBuilder())
            ->batiment('Batiment B')
            ->capacite(30)
            ->type('cours')
            ->build();
    }

    public function testBuildSansCapaciteLanceException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Tous les champs obligatoires de la salle doivent etre renseignes');

        (new CreerSalleDTOBuilder())
            ->nom('Salle 101')
            ->batiment('Batiment B')
            ->type('cours')
            ->build();
    }

    public function testToArray(): void
    {
        $dto = (new CreerSalleDTOBuilder())
            ->nom('Labo Info')
            ->batiment('Batiment C')
            ->capacite(25)
            ->type('informatique')
            ->active(false)
            ->build();

        $tableau = $dto->toArray();

        $this->assertEquals([
            'nom' => 'Labo Info',
            'batiment' => 'Batiment C',
            'capacite' => 25,
            'type' => 'informatique',
            'active' => false
        ], $tableau);
    }
}
