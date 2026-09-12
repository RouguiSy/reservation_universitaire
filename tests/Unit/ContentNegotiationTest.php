<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ContentNegotiationTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/src/helpers.php';
        $_GET = [];
        $_POST = [];
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml';
        $_SERVER['REQUEST_URI'] = '/salles';
        unset($_SERVER['CONTENT_TYPE'], $_SERVER['HTTP_CONTENT_TYPE']);
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
        unset($_SERVER['HTTP_ACCEPT'], $_SERVER['REQUEST_URI'], $_SERVER['CONTENT_TYPE'], $_SERVER['HTTP_CONTENT_TYPE']);
    }

    public function testWantsJsonReturnsFalseByDefault(): void
    {
        $this->assertFalse(wantsJson());
    }

    public function testWantsJsonWithFormatQueryParam(): void
    {
        $_GET['format'] = 'json';
        $this->assertTrue(wantsJson());

        $_GET['format'] = 'JSON';
        $this->assertTrue(wantsJson());

        $_GET['format'] = 'html';
        $this->assertFalse(wantsJson());
    }

    public function testWantsJsonWithAcceptHeader(): void
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json, text/plain, */*';
        $this->assertTrue(wantsJson());
    }

    public function testWantsJsonWithContentTypeHeader(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->assertTrue(wantsJson());
    }

    public function testWantsJsonWithApiUriPrefix(): void
    {
        $_SERVER['REQUEST_URI'] = '/api/salles';
        $this->assertTrue(wantsJson());
    }

    public function testGetRequestDataReturnsPost(): void
    {
        $_POST = ['nom' => 'Salle B', 'capacite' => '30'];
        $data = get_request_data();
        $this->assertSame('Salle B', $data['nom']);
        $this->assertSame('30', $data['capacite']);
    }
}
