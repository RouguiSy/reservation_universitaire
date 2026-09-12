<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\View\HtmlRenderer;
use App\View\JsonRenderer;
use App\View\ViewRenderer;
use PHPUnit\Framework\TestCase;

class ViewRendererTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/src/helpers.php';
        $_GET = [];
        $_POST = [];
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $_SERVER['REQUEST_URI'] = '/salles';
        unset($_SERVER['CONTENT_TYPE'], $_SERVER['HTTP_CONTENT_TYPE'], $_ENV['APP_RESPONSE_FORMAT']);
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
        unset($_SERVER['HTTP_ACCEPT'], $_SERVER['REQUEST_URI'], $_SERVER['CONTENT_TYPE'], $_SERVER['HTTP_CONTENT_TYPE'], $_ENV['APP_RESPONSE_FORMAT']);
    }

    public function testJsonRendererFormatsPayloadCorrectly(): void
    {
        $renderer = new JsonRenderer();

        $output = $renderer->render('', ['foo' => 'bar'], 200);
        $decoded = json_decode($output, true);

        $this->assertSame('success', $decoded['status']);
        $this->assertSame(['foo' => 'bar'], $decoded['data']);

        $errorOutput = $renderer->render('', ['message' => 'Erreur test'], 400);
        $errorDecoded = json_decode($errorOutput, true);
        $this->assertSame('error', $errorDecoded['status']);
    }

    public function testHtmlRendererThrowsExceptionOnMissingTemplate(): void
    {
        $this->expectException(\RuntimeException::class);
        $renderer = new HtmlRenderer();
        $renderer->render('non_existent_template_xyz.php');
    }

    public function testResolveFormatDefaultsToHtml(): void
    {
        $renderer = new ViewRenderer();
        $this->assertSame('html', $renderer->resolveFormat());
        $this->assertInstanceOf(HtmlRenderer::class, $renderer->getRenderer());
    }

    public function testResolveFormatFromEnvVariable(): void
    {
        $_ENV['APP_RESPONSE_FORMAT'] = 'json';
        $renderer = new ViewRenderer();
        $this->assertSame('json', $renderer->resolveFormat());
        $this->assertInstanceOf(JsonRenderer::class, $renderer->getRenderer());

        $_ENV['APP_RESPONSE_FORMAT'] = 'html';
        $this->assertSame('html', $renderer->resolveFormat());
    }

    public function testResolveFormatViaQueryParamOverridesEnv(): void
    {
        $_ENV['APP_RESPONSE_FORMAT'] = 'html';
        $_GET['format'] = 'json';

        $renderer = new ViewRenderer();
        $this->assertSame('json', $renderer->resolveFormat());

        $_ENV['APP_RESPONSE_FORMAT'] = 'json';
        $_GET['format'] = 'html';
        $this->assertSame('html', $renderer->resolveFormat());
    }

    public function testResolveFormatViaAcceptHeader(): void
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json, text/plain, */*';
        $renderer = new ViewRenderer();
        $this->assertSame('json', $renderer->resolveFormat());
    }

    public function testResolveFormatViaContentTypeHeader(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $renderer = new ViewRenderer();
        $this->assertSame('json', $renderer->resolveFormat());
    }

    public function testResolveFormatViaApiRoutePrefix(): void
    {
        $_SERVER['REQUEST_URI'] = '/api/salles';
        $renderer = new ViewRenderer();
        $this->assertSame('json', $renderer->resolveFormat());
    }

    public function testResolveFormatViaExplicitOverride(): void
    {
        $renderer = new ViewRenderer();
        $this->assertSame('json', $renderer->resolveFormat('json'));
        $this->assertSame('html', $renderer->resolveFormat('html'));
    }

    public function testViewRendererDelegatesRenderingToJson(): void
    {
        $_GET['format'] = 'json';
        $renderer = new ViewRenderer();
        $result = $renderer->render('', ['id' => 42], 200);

        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertSame('success', $decoded['status']);
        $this->assertSame(42, $decoded['data']['id']);
    }
}
