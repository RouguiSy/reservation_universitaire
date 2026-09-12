<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\SalleValidator;
use PHPUnit\Framework\TestCase;

final class SalleValidatorTest extends TestCase
{
    private SalleValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new SalleValidator();
    }

    private function donneesValides(array $ecrasement = []): array
    {
        return array_merge([
            'nom'      => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => '40',
            'type'     => 'cours',
            'active'   => '1',
        ], $ecrasement);
    }

    public function test_des_donnees_valides_sont_acceptees(): void
    {
        $resultat = $this->validator->validate($this->donneesValides());

        self::assertTrue($resultat->estValide());
    }

    public function test_une_capacite_negative_est_rejetee(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['capacite' => '-5']));

        self::assertFalse($resultat->estValide());
        self::assertArrayHasKey('capacite', $resultat->getErreurs());
    }

    public function test_un_type_de_salle_inconnu_est_rejete(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['type' => 'piscine']));

        self::assertFalse($resultat->estValide());
        self::assertArrayHasKey('type', $resultat->getErreurs());
    }

    public function test_un_nom_trop_court_est_rejete(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['nom' => 'A']));

        self::assertFalse($resultat->estValide());
        self::assertArrayHasKey('nom', $resultat->getErreurs());
        self::assertSame('Le nom est invalide.', $resultat->reponse('nom'));
        self::assertSame('Le nom est invalide.', $resultat['nom']);
    }
}

