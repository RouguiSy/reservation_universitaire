<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Session\SessionManager;
use PHPUnit\Framework\TestCase;

final class SessionManagerTest extends TestCase
{
    private SessionManager $session;

    protected function setUp(): void
    {
        $this->session = new SessionManager();
        if ($this->session->isStarted()) {
            $this->session->clear();
        } else {
            $_SESSION = [];
        }
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
    }

    public function test_get_set_has_and_remove(): void
    {
        self::assertFalse($this->session->has('test_key'));
        self::assertNull($this->session->get('test_key'));
        self::assertSame('default_val', $this->session->get('test_key', 'default_val'));

        $this->session->set('test_key', 'test_value');
        self::assertTrue($this->session->has('test_key'));
        self::assertSame('test_value', $this->session->get('test_key'));

        $this->session->remove('test_key');
        self::assertFalse($this->session->has('test_key'));
        self::assertNull($this->session->get('test_key'));
    }

    public function test_clear_and_all(): void
    {
        $this->session->set('a', 1);
        $this->session->set('b', 2);

        self::assertArrayHasKey('a', $this->session->all());
        self::assertArrayHasKey('b', $this->session->all());
        self::assertSame(1, $this->session->all()['a']);

        $this->session->clear();
        self::assertEmpty($this->session->all());
        self::assertFalse($this->session->has('a'));
    }

    public function test_flash_messages(): void
    {
        self::assertFalse($this->session->hasFlash());
        self::assertNull($this->session->getFlash());

        $this->session->flash('success', 'Operation effectuee avec succes.');
        self::assertTrue($this->session->hasFlash());

        $flash = $this->session->getFlash();
        self::assertNotNull($flash);
        self::assertSame('success', $flash['type']);
        self::assertSame('Operation effectuee avec succes.', $flash['message']);

        // getFlash() doit consommer le message flash (usage unique)
        self::assertFalse($this->session->hasFlash());
        self::assertNull($this->session->getFlash());
    }

    public function test_user_authentication_helpers(): void
    {
        self::assertFalse($this->session->hasUser());
        self::assertNull($this->session->getUser());
        self::assertFalse($this->session->isAdmin());

        $user = [
            'id' => 1,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ];

        $this->session->setUser($user);
        self::assertTrue($this->session->hasUser());
        self::assertSame($user, $this->session->getUser());
        self::assertTrue($this->session->isAdmin());

        // Utilisateur standard (non admin)
        $this->session->setUser(['id' => 2, 'role' => 'user']);
        self::assertFalse($this->session->isAdmin());

        $this->session->removeUser();
        self::assertFalse($this->session->hasUser());
        self::assertNull($this->session->getUser());
    }

    public function test_form_errors_and_old_inputs(): void
    {
        $errors = ['nom' => 'Le nom est obligatoire.', 'capacite' => 'Invalide'];
        $old = ['nom' => 'Salle A', 'capacite' => '-5'];

        $this->session->setFormErrors('salle', $errors);
        $this->session->setFormOld('salle', $old);

        // Récupération avec consommation automatique
        self::assertSame($errors, $this->session->getFormErrors('salle'));
        self::assertSame($old, $this->session->getFormOld('salle'));

        // Une deuxième lecture doit être vide
        self::assertEmpty($this->session->getFormErrors('salle'));
        self::assertEmpty($this->session->getFormOld('salle'));
    }
}
