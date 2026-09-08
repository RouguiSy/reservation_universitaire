<?php
declare(strict_types=1);
namespace App\Validator;
use App\Model\Salle;
use Respect\Validation\Validator as v;
class SalleValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $erreurs = [];
        $capacite = filter_var($data['capacite'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $active = $data['active'] ?? true;
        $activeValide = in_array($active, [true, false, 0, 1, '0', '1', 'on', 'off'], true);
        if (!v::stringType()->length(2, 100)->validate($data['nom'] ?? '')) $erreurs['nom'] = 'Le nom est invalide.';
        if (!v::stringType()->length(2, 100)->validate($data['batiment'] ?? '')) $erreurs['batiment'] = 'Le batiment est invalide.';
        if ($capacite === false || !v::intType()->between(1, 1000)->validate($capacite)) $erreurs['capacite'] = 'La capacite est invalide.';
        if (!v::in(['amphitheatre', 'cours', 'laboratoire', 'informatique', 'reunion'])->validate($data['type'] ?? null)) $erreurs['type'] = 'Le type est invalide.';
        if (!$activeValide) $erreurs['active'] = "L'etat est invalide.";
        if ($erreurs !== []) return ValidationResult::echec($erreurs);
        if (Salle::query()->where('nom', $data['nom'])->where('batiment', $data['batiment'])->exists()) return ValidationResult::echec(['nom' => 'Cette salle existe deja.', 'batiment' => 'Cette salle existe deja.']);
        return ValidationResult::succes();
    }
}
