<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Config;

use Dotcms\PhpSdk\Config\Config;
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
        $this->assertEquals('Bearer test-api-key', $clientOptions['headers']['Authorization']);
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
        $this->assertEquals('Bearer test-api-key', $clientOptions['headers']['Authorization']);
        $this->assertEquals('value', $clientOptions['headers']['X-Custom']);
        $this->assertFalse($clientOptions['verify']);
        $this->assertEquals(60, $clientOptions['timeout']);
        $this->assertEquals(20, $clientOptions['connect_timeout']);
        $this->assertFalse($clientOptions['http_errors']);
        $this->assertFalse($clientOptions['allow_redirects']);
    }
} 