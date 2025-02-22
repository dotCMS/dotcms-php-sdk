<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Config;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\Exception\ConfigException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testConfigCreation(): void
    {
        $config = new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key'
        );

        $this->assertEquals('https://demo.dotcms.com', $config->getHost());
        $this->assertEquals('test-api-key', $config->getApiKey());

        $clientOptions = $config->getClientOptions();
        $this->assertEquals('https://demo.dotcms.com', $clientOptions['base_uri']);
        /** @var array<string, string> $headers */
        $headers = $clientOptions['headers'];
        $this->assertEquals('Bearer test-api-key', $headers['Authorization']);
        $this->assertTrue($clientOptions['verify']);
        $this->assertEquals(30, $clientOptions['timeout']);
        $this->assertEquals(10, $clientOptions['connect_timeout']);
        $this->assertTrue($clientOptions['http_errors']);
        $this->assertTrue($clientOptions['allow_redirects']);
    }

    public function testConfigWithCustomValues(): void
    {
        $customOptions = [
            'headers' => ['X-Custom' => 'value'],
            'verify' => false,
            'timeout' => 60,
            'connect_timeout' => 20,
            'http_errors' => false,
            'allow_redirects' => false,
        ];

        $config = new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key',
            clientOptions: $customOptions
        );

        $clientOptions = $config->getClientOptions();
        $this->assertEquals('https://demo.dotcms.com', $clientOptions['base_uri']);
        /** @var array<string, string> $headers */
        $headers = $clientOptions['headers'];
        $this->assertEquals('Bearer test-api-key', $headers['Authorization']);
        $this->assertEquals('value', $headers['X-Custom']);
        $this->assertFalse($clientOptions['verify']);
        $this->assertEquals(60, $clientOptions['timeout']);
        $this->assertEquals(20, $clientOptions['connect_timeout']);
        $this->assertFalse($clientOptions['http_errors']);
        $this->assertFalse($clientOptions['allow_redirects']);
    }

    public function testEmptyHost(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Invalid host URL: "". Host must be a valid URL starting with http:// or https://');
        new Config(host: '', apiKey: 'test-api-key');
    }

    public function testInvalidUrlHost(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Invalid host URL: "not-a-url". Host must be a valid URL starting with http:// or https://');
        new Config(host: 'not-a-url', apiKey: 'test-api-key');
    }

    public function testMissingProtocolHost(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Invalid host URL: "demo.dotcms.com". Host must be a valid URL starting with http:// or https://');
        new Config(host: 'demo.dotcms.com', apiKey: 'test-api-key');
    }

    public function testInvalidProtocolHost(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Invalid host URL: "ftp://demo.dotcms.com". Host must be a valid URL starting with http:// or https://');
        new Config(host: 'ftp://demo.dotcms.com', apiKey: 'test-api-key');
    }

    public function testEmptyApiKey(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('API key cannot be empty');
        new Config(host: 'https://demo.dotcms.com', apiKey: '  ');
    }

    public function testInvalidHeadersType(): void
    {
        $this->expectException(ConfigException::class);
        new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key',
            clientOptions: ['headers' => 'not-an-array']
        );
    }

    public function testInvalidHeaderValue(): void
    {
        $this->expectException(ConfigException::class);
        new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key',
            clientOptions: ['headers' => ['X-Custom' => ['not-a-string']]]
        );
    }

    public function testInvalidTimeout(): void
    {
        $this->expectException(ConfigException::class);
        new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key',
            clientOptions: ['timeout' => -1]
        );
    }

    public function testInvalidConnectTimeout(): void
    {
        $this->expectException(ConfigException::class);
        new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key',
            clientOptions: ['connect_timeout' => 'not-an-int']
        );
    }

    public function testInvalidVerify(): void
    {
        $this->expectException(ConfigException::class);
        new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key',
            clientOptions: ['verify' => 'not-a-bool']
        );
    }

    public function testInvalidHttpErrors(): void
    {
        $this->expectException(ConfigException::class);
        new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key',
            clientOptions: ['http_errors' => 1]
        );
    }

    public function testInvalidAllowRedirects(): void
    {
        $this->expectException(ConfigException::class);
        new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key',
            clientOptions: ['allow_redirects' => 'yes']
        );
    }
}
