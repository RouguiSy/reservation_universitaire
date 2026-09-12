<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/src/helpers.php';
        Message::reset();
    }

    protected function tearDown(): void
    {
        Message::reset();
    }

    public function testGetExistingMessage(): void
    {
        $message = Message::get('auth.invalid_credentials');
        $this->assertSame('Email ou mot de passe incorrect.', $message);
    }

    public function testGetMessageViaHelper(): void
    {
        $this->assertSame('Connexion reussie', message('auth.login_success'));
        $this->assertSame('Connexion reussie', trans('auth.login_success'));
    }

    public function testGetMissingKeyReturnsKeyOrFallback(): void
    {
        $this->assertSame('non.existent.key', Message::get('non.existent.key'));
        $this->assertSame('Fallback message', Message::get('non.existent.key', [], 'Fallback message'));
    }

    public function testMessageWithPlaceholderReplacement(): void
    {
        $msg = Message::get('validation.invalid', [':field' => 'email']);
        $this->assertSame('Le champ email est invalide.', $msg);

        $msg2 = Message::get('salle.conflict_dates', [
            ':debut' => '10/09/2026 10:00',
            ':fin'   => '10/09/2026 12:00',
        ]);
        $this->assertSame('La salle est deja reservee du 10/09/2026 10:00 au 10/09/2026 12:00', $msg2);
    }

    public function testLoadCustomMessages(): void
    {
        Message::load([
            'custom' => [
                'hello' => 'Bonjour :nom !',
            ],
        ]);

        $this->assertSame('Bonjour Alice !', message('custom.hello', [':nom' => 'Alice']));
    }
}
