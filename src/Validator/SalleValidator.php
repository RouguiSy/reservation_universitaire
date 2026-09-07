<?php

declare(strict_types=1);

namespace App\Validator;

use App\Model\Salle;
use Illuminate\Validation\Factory;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;

class SalleValidator implements ValidatorInterface
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
            'nom' => 'required|string|max:100',
            'batiment' => 'required|string|max:100',
            'capacite' => 'required|integer|min:1|max:1000',
            'type' => 'required|string|max:50|in:amphitheatre,cours,laboratoire,informatique,reunion',
            'active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            $erreurs = [];
            foreach ($validator->errors()->all() as $erreur) {
                $erreurs[] = $erreur;
            }
            return ValidationResult::echec($erreurs);
        }

        if (!empty($data['nom']) && !empty($data['batiment'])) {
            $exists = Salle::query()
                ->where('nom', $data['nom'])
                ->where('batiment', $data['batiment'])
                ->exists();

            if ($exists) {
                return ValidationResult::echec([
                    'nom' => 'Une salle avec ce nom existe deja dans ce batiment',
                    'batiment' => 'Une salle avec ce nom existe deja dans ce batiment'
                ]);
            }
        }

        return ValidationResult::succes();
    }
}
