<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\ReservationValidator;
use PHPUnit\Framework\TestCase;

final class ReservationValidatorTest extends TestCase
{
    private ReservationValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ReservationValidator();
    }

    private function donneesValides(array $ecrasement = []): array
    {
        return array_merge([
            'salle_id'    => '1',
            'responsable' => 'Awa Ndiaye',
            'email'       => 'awa.ndiaye@universite.sn',
            'motif'       => "Cours d'architecture logicielle",
            'date_debut'  => '2027-01-10T10:00',
            'date_fin'    => '2027-01-10T12:00',
        ], $ecrasement);
    }

    public function test_des_donnees_valides_sont_acceptees(): void
    {
        $resultat = $this->validator->validate($this->donneesValides());

        self::assertTrue($resultat->estValide());
    }

    public function test_une_adresse_email_invalide_est_rejetee(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['email' => 'adresse-invalide']));

        self::assertFalse($resultat->estValide());
        self::assertArrayHasKey('email', $resultat->getErreurs());
    }

    public function test_un_responsable_vide_est_rejete(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['responsable' => '']));

        self::assertFalse($resultat->estValide());
        self::assertArrayHasKey('responsable', $resultat->getErreurs());
    }

    public function test_une_date_incorrecte_est_rejetee(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['date_debut' => 'pas-une-date']));

        self::assertFalse($resultat->estValide());
        self::assertArrayHasKey('date_debut', $resultat->getErreurs());
    }

    public function test_un_motif_trop_court_est_rejete(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['motif' => 'TP']));

        self::assertFalse($resultat->estValide());
        self::assertArrayHasKey('motif', $resultat->getErreurs());
    }

    public function test_une_date_debut_passee_est_rejetee(): void
    {
        $resultat = $this->validator->validate($this->donneesValides([
            'date_debut' => '2020-01-01T10:00',
            'date_fin'   => '2020-01-01T12:00',
        ]));

        self::assertFalse($resultat->estValide());
        self::assertSame('La date doit etre future.', $resultat->reponse('date_debut'));
        self::assertSame('La date doit etre future.', $resultat['date_debut']);
    }

    public function test_une_date_fin_anterieure_au_debut_est_rejetee(): void
    {
        $resultat = $this->validator->validate($this->donneesValides([
            'date_debut' => '2027-01-10T14:00',
            'date_fin'   => '2027-01-10T10:00',
        ]));

        self::assertFalse($resultat->estValide());
        self::assertSame('La fin doit etre apres le debut.', $resultat->reponse('date_fin'));
    }
}

