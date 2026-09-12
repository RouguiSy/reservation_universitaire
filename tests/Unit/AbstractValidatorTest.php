<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\AbstractValidator;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Validator as v;

final class TestCustomValidator extends AbstractValidator
{
    protected function messages(): array
    {
        return [
            'titre' => 'Le titre est invalide.',
            'prix'  => 'Le prix est invalide.',
        ];
    }

    protected function rules(array $data): array
    {
        return [
            'titre' => v::stringType()->length(3, 50),
            'prix'  => v::intVal()->positive(),
        ];
    }
}

final class AbstractValidatorTest extends TestCase
{
    private TestCustomValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new TestCustomValidator();
    }

    public function test_parcours_tableau_et_reponse_selon_cle(): void
    {
        $resultat = $this->validator->validate([
            'titre' => 'AB',
            'prix'  => '-10',
        ]);

        self::assertFalse($resultat->estValide());
        self::assertCount(2, $resultat);

        // Réponse selon la clé
        self::assertSame('Le titre est invalide.', $resultat->reponse('titre'));
        self::assertSame('Le titre est invalide.', $resultat['titre']);
        self::assertSame('Le prix est invalide.', $resultat->reponse('prix'));
        self::assertSame('Le prix est invalide.', $resultat['prix']);
    }

    public function test_donnees_valides_acceptees(): void
    {
        $resultat = $this->validator->validate([
            'titre' => 'Livre de test',
            'prix'  => '25',
        ]);

        self::assertTrue($resultat->estValide());
        self::assertCount(0, $resultat);
        self::assertSame('Livre de test', $resultat->getDonnees()['titre']);
    }

    public function test_instanciation_validation_result_avec_nom_parametre_user(): void
    {
        // Vérifie la compatibilité avec new ValidationResult(valid: false, errors: ...)
        $resEchec = new \App\Validation\ValidationResult(valid: false, errors: ['nom' => 'Erreur']);
        self::assertFalse($resEchec->estValide());
        self::assertSame('Erreur', $resEchec['nom']);

        $resSucces = new \App\Validation\ValidationResult(valid: true, data: ['nom' => 'Ok']);
        self::assertTrue($resSucces->estValide());
        self::assertSame('Ok', $resSucces->getDonnees()['nom']);
    }
}
