<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\View\Negotiation\FormatNegotiator;
use PHPUnit\Framework\TestCase;

class FormatNegotiatorTest extends TestCase
{
    private FormatNegotiator $negotiator;

    protected function setUp(): void
    {
        $_GET = [];
        $_POST = [];
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $_SERVER['REQUEST_URI'] = '/salles';
        unset($_SERVER['CONTENT_TYPE'], $_SERVER['HTTP_CONTENT_TYPE'], $_ENV['APP_RESPONSE_FORMAT']);

        $this->negotiator = new FormatNegotiator();
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
        unset($_SERVER['HTTP_ACCEPT'], $_SERVER['REQUEST_URI'], $_SERVER['CONTENT_TYPE'], $_SERVER['HTTP_CONTENT_TYPE'], $_ENV['APP_RESPONSE_FORMAT']);
    }

    public function testDefaultsToHtml(): void
    {
        $this->assertSame('html', $this->negotiator->negotiate());
    }

    public function testExplicitOverrideHasHighestPriority(): void
    {
        $_GET['format'] = 'html';
        $this->assertSame('json', $this->negotiator->negotiate('json'));

        $_GET['format'] = 'json';
        $this->assertSame('html', $this->negotiator->negotiate('html'));
    }

    public function testQueryParamTakesPrecedenceOverHeadersAndEnv(): void
    {
        $_ENV['APP_RESPONSE_FORMAT'] = 'html';
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $_GET['format'] = 'json';

        $this->assertSame('json', $this->negotiator->negotiate());

        $_ENV['APP_RESPONSE_FORMAT'] = 'json';
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $_GET['format'] = 'html';

        $this->assertSame('html', $this->negotiator->negotiate());
    }

    public function testAcceptHeaderResolvesJson(): void
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json, text/javascript, */*';
        $this->assertSame('json', $this->negotiator->negotiate());
    }

    public function testContentTypeHeaderResolvesJson(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->assertSame('json', $this->negotiator->negotiate());
    }

    public function testApiRoutePrefixResolvesJson(): void
    {
        $_SERVER['REQUEST_URI'] = '/api/v1/salles';
        $this->assertSame('json', $this->negotiator->negotiate());
    }

    public function testEnvVariableResolvesWhenNoRequestIndicators(): void
    {
        $_ENV['APP_RESPONSE_FORMAT'] = 'json';
        $this->assertSame('json', $this->negotiator->negotiate());
    }

    public function testInvalidCandidatesAreSkippedInPipeline(): void
    {
        $_GET['format'] = 'invalid_format_xyz';
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        // 'invalid_format_xyz' est ignoré, le pipeline continue et trouve 'json' via HTTP_ACCEPT
        $this->assertSame('json', $this->negotiator->negotiate());
    }

    public function testCustomProviderCanBePrepended(): void
    {
        $this->negotiator->addProvider(fn(): ?string => 'json', true);
        $this->assertSame('json', $this->negotiator->negotiate());
    }
}
