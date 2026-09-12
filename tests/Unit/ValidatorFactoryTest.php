<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\Validation\ValidatorFactory;
use App\Validation\ValidatorInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ValidatorFactoryTest extends TestCase
{
    public function testCreateSalleValidator(): void
    {
        $validator = ValidatorFactory::create('salle');

        $this->assertInstanceOf(ValidatorInterface::class, $validator);
        $this->assertInstanceOf(SalleValidator::class, $validator);
    }

    public function testCreateReservationValidator(): void
    {
        $validator = ValidatorFactory::create('reservation');

        $this->assertInstanceOf(ValidatorInterface::class, $validator);
        $this->assertInstanceOf(ReservationValidator::class, $validator);
    }

    public function testCreateUnknownValidatorThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Validateur inconnu pour le type 'inconnu'");

        ValidatorFactory::create('inconnu');
    }
}
