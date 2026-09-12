<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;

abstract class AbstractValidator implements ValidatorInterface
{
    abstract protected function rules(array $data): array;

    protected function messages(): array
    {
        return [];
    }

    public function validate(array $data): ValidationResult
    {
        $rules = $this->rules($data);
        $messages = $this->messages();
        $errors = [];

        foreach ($rules as $champ => $validator) {
            try {
                $validator->assert($data[$champ] ?? null);
            } catch (NestedValidationException $exception) {
                $errors[$champ] = $messages[$champ]
                    ?? $exception->getMessages()[0]
                    ?? "Le champ {$champ} est invalide.";
            }
        }

        if ($errors !== []) {
            return new ValidationResult(valid: false, errors: $errors);
        }

        return new ValidationResult(valid: true, data: $data);
    }
}
