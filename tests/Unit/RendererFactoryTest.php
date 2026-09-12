<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\View\HtmlRenderer;
use App\View\JsonRenderer;
use App\View\RendererFactory;
use App\View\RendererInterface;
use App\View\ViewRenderer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RendererFactoryTest extends TestCase
{
    private RendererFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new RendererFactory();
    }

    public function testCreateHtmlRenderer(): void
    {
        $renderer = $this->factory->create('html');

        $this->assertInstanceOf(RendererInterface::class, $renderer);
        $this->assertInstanceOf(HtmlRenderer::class, $renderer);
    }

    public function testCreateJsonRenderer(): void
    {
        $renderer = $this->factory->create('json');

        $this->assertInstanceOf(RendererInterface::class, $renderer);
        $this->assertInstanceOf(JsonRenderer::class, $renderer);
    }

    public function testStaticMakeMethod(): void
    {
        $html = RendererFactory::make('html');
        $json = RendererFactory::make('json');

        $this->assertInstanceOf(HtmlRenderer::class, $html);
        $this->assertInstanceOf(JsonRenderer::class, $json);
    }

    public function testCreateWithUnknownFormatThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Format de rendu non supporte : 'xml'");

        $this->factory->create('xml');
    }

    public function testRegisterCustomRendererSupportsOpenClosedPrinciple(): void
    {
        $mockCustomRenderer = new class implements RendererInterface {
            public function render(string $view, array $data = [], int $statusCode = 200): string
            {
                return '<xml>' . ($data['status'] ?? '') . '</xml>';
            }

            public function respond(string $view, array $data = [], int $statusCode = 200, bool $exit = true): void
            {
            }
        };

        $this->factory->register('xml', fn() => $mockCustomRenderer);
        $created = $this->factory->create('xml');

        $this->assertSame($mockCustomRenderer, $created);
        $this->assertSame('<xml>success</xml>', $created->render('', ['status' => 'success']));
    }

    public function testViewRendererIntegratesWithRendererFactory(): void
    {
        $viewRenderer = new ViewRenderer($this->factory);

        $this->assertInstanceOf(RendererFactory::class, $viewRenderer->getFactory());
        $this->assertInstanceOf(HtmlRenderer::class, $viewRenderer->getRenderer('html'));
        $this->assertInstanceOf(JsonRenderer::class, $viewRenderer->getRenderer('json'));
    }
}
