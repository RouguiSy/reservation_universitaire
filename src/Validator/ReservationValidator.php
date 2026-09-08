<?php
declare(strict_types=1);
namespace App\Validator;
use App\Model\Reservation;
use App\Model\Salle;
use Respect\Validation\Validator as v;
class ReservationValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $erreurs = [];
        if (!v::intType()->positive()->validate($data['salle_id'] ?? null)) $erreurs['salle_id'] = "L'identifiant est invalide.";
        if (!v::stringType()->length(2, 120)->validate($data['responsable'] ?? '')) $erreurs['responsable'] = 'Le responsable est invalide.';
        if (!v::email()->validate($data['email'] ?? '')) $erreurs['email'] = "L'adresse est invalide.";
        if (!v::stringType()->length(5, 255)->validate($data['motif'] ?? '')) $erreurs['motif'] = 'Le motif est invalide.';
        if (!v::date()->validate($data['date_debut'] ?? '')) $erreurs['date_debut'] = 'La date de debut est invalide.';
        if (!v::date()->validate($data['date_fin'] ?? '')) $erreurs['date_fin'] = 'La date de fin est invalide.';
        if ($erreurs !== []) return ValidationResult::echec($erreurs);
        $salle = Salle::query()->find((int) $data['salle_id']);
        if (!$salle) return ValidationResult::echec(['salle_id' => "Cette salle n'existe pas."]);
        if (!$salle->active) return ValidationResult::echec(['salle_id' => "Cette salle n'est pas active."]);
        $debut = new \DateTimeImmutable($data['date_debut']);
        $fin = new \DateTimeImmutable($data['date_fin']);
        if ($debut <= new \DateTimeImmutable()) return ValidationResult::echec(['date_debut' => 'La date doit etre future.']);
        if ($fin <= $debut) return ValidationResult::echec(['date_fin' => 'La fin doit etre apres le debut.']);
        if ($fin->getTimestamp() - $debut->getTimestamp() > 86400) return ValidationResult::echec(['date_fin' => 'La reservation depasse 24 heures.']);
        $conflit = Reservation::query()->where('salle_id', (int) $data['salle_id'])->where('statut', 'confirmee')->where(function ($query) use ($data) { $query->where('date_debut', '<', $data['date_fin'])->where('date_fin', '>', $data['date_debut']); })->exists();
        if ($conflit) return ValidationResult::echec(['date_debut' => 'Cette salle est deja reservee.', 'date_fin' => 'Cette salle est deja reservee.']);
        return ValidationResult::succes();
    }
}
