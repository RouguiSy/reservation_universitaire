<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\ValidationResult;
use PHPUnit\Framework\TestCase;

final class ValidationResultTest extends TestCase
{
    public function test_succes_est_valide_et_sans_erreur(): void
    {
        $result = ValidationResult::succes(['nom' => 'Salle 1']);

        self::assertTrue($result->estValide());
        self::assertTrue($result->isValid());
        self::assertEmpty($result->getErreurs());
        self::assertSame(0, count($result));
        self::assertNull($result->reponse('nom'));
        self::assertFalse(isset($result['nom']));
    }

    public function test_echec_est_invalide_avec_erreurs(): void
    {
        $result = ValidationResult::echec([
            'nom' => 'Le nom est invalide.',
            'capacite' => 'La capacite est invalide.',
        ]);

        self::assertFalse($result->estValide());
        self::assertSame(2, count($result));
        self::assertSame('Le nom est invalide.', $result->reponse('nom'));
        self::assertSame('La capacite est invalide.', $result->reponse('capacite'));
    }

    public function test_acces_tableau_et_parcours_selon_cle(): void
    {
        $erreurs = [
            'date_debut' => 'La date doit etre future.',
            'date_fin' => 'La fin doit etre apres le debut.',
        ];

        $result = ValidationResult::echec($erreurs);

        // Test ArrayAccess
        self::assertTrue(isset($result['date_debut']));
        self::assertSame('La date doit etre future.', $result['date_debut']);
        self::assertSame('La fin doit etre apres le debut.', $result['date_fin']);
        self::assertNull($result['inconnu']);

        // Test parcours (IteratorAggregate)
        $parcouru = [];
        foreach ($result as $cle => $reponse) {
            $parcouru[$cle] = $reponse;
        }

        self::assertSame($erreurs, $parcouru);
    }
}
