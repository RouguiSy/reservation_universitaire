<?php

declare(strict_types=1);

namespace App\Validator;

use App\Model\Reservation;
use App\Model\Salle;
use Illuminate\Validation\Factory;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;

class ReservationValidator implements ValidatorInterface
{
    private Factory $validator;

    public function __construct()
    {
        $translator = new Translator(new ArrayLoader(), 'fr');
        $this->validator = new Factory($translator);
    }

    public function validate(array $data): ValidationResult
    {
        $validator = $this->validator->make($data, [
            'salle_id' => 'required|integer|min:1',
            'responsable' => 'required|string|max:120',
            'email' => 'required|email|max:190',
            'motif' => 'required|string|max:255',
            'date_debut' => 'required|date|after:now',
            'date_fin' => 'required|date|after:date_debut'
        ]);

        if ($validator->fails()) {
            $erreurs = [];
            foreach ($validator->errors()->all() as $erreur) {
                $erreurs[] = $erreur;
            }
            return ValidationResult::echec($erreurs);
        }

        if (!empty($data['salle_id'])) {
            $salle = Salle::query()->find($data['salle_id']);
            if (!$salle) {
                return ValidationResult::echec(['salle_id' => 'Cette salle n\'existe pas']);
            }

            if (!$salle->active) {
                return ValidationResult::echec(['salle_id' => 'Cette salle n\'est pas active']);
            }
        }

        if (!empty($data['date_debut']) && !empty($data['date_fin'])) {
            $debut = new \DateTime($data['date_debut']);
            $fin = new \DateTime($data['date_fin']);
            $duree = $debut->diff($fin);

            if ($duree->days > 1) {
                return ValidationResult::echec(['date_fin' => 'La reservation ne peut pas depasser 24 heures']);
            }
        }

        if (!empty($data['salle_id']) && !empty($data['date_debut']) && !empty($data['date_fin'])) {
            $conflit = Reservation::query()
                ->where('salle_id', $data['salle_id'])
                ->where('statut', 'confirmee')
                ->where(function ($query) use ($data) {
                    $query->where('date_debut', '<', $data['date_fin'])
                          ->where('date_fin', '>', $data['date_debut']);
                })
                ->exists();

            if ($conflit) {
                return ValidationResult::echec([
                    'date_debut' => 'Cette salle est deja reservee sur cette plage horaire',
                    'date_fin' => 'Cette salle est deja reservee sur cette plage horaire'
                ]);
            }
        }

        return ValidationResult::succes();
    }
}
