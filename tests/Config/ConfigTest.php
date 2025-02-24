<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Config;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\Config\LogLevel;
use Dotcms\PhpSdk\Exception\ConfigException;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private const HOST = 'https://demo.dotcms.com';
    private const API_KEY = 'test-api-key';

    public function testValidConfig(): void
    {
        $config = new Config(self::HOST, self::API_KEY);

        $this->assertSame(self::HOST, $config->getHost());
        $this->assertSame(self::API_KEY, $config->getApiKey());
        $this->assertInstanceOf(Logger::class, $config->getLogger());
        $this->assertEquals(LogLevel::INFO, $config->getLogLevel());

        $handlers = $config->getLogger()->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(NullHandler::class, $handlers[0]);
    }

    public function testCustomLogHandler(): void
    {
        $handler = new TestHandler();
        $config = new Config(
            self::HOST,
            self::API_KEY,
            logConfig: [
                'handlers' => [$handler],
                'console' => false,
            ]
        );

        $handlers = $config->getLogger()->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertSame($handler, $handlers[0]);
    }

    public function testCustomLogLevel(): void
    {
        $config = new Config(
            self::HOST,
            self::API_KEY,
            logConfig: [
                'level' => LogLevel::DEBUG,
            ]
        );

        $this->assertEquals(LogLevel::DEBUG, $config->getLogLevel());

        $handlers = $config->getLogger()->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(StreamHandler::class, $handlers[0]);
    }

    public function testConsoleLoggingDisabled(): void
    {
        $config = new Config(
            self::HOST,
            self::API_KEY,
            logConfig: [
                'level' => LogLevel::INFO,
                'console' => false,
            ]
        );

        $handlers = $config->getLogger()->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(NullHandler::class, $handlers[0]);
    }

    public function testMultipleHandlers(): void
    {
        $testHandler = new TestHandler();
        $config = new Config(
            self::HOST,
            self::API_KEY,
            logConfig: [
                'level' => LogLevel::INFO,
                'handlers' => [$testHandler],
            ]
        );

        $handlers = $config->getLogger()->getHandlers();
        $this->assertCount(2, $handlers);
        $this->assertInstanceOf(TestHandler::class, $handlers[0]);
        $this->assertInstanceOf(StreamHandler::class, $handlers[1]);

        // Verify the test handler is the same instance
        $this->assertSame($testHandler, $handlers[0]);

        // Verify the console handler is configured correctly
        $consoleHandler = $handlers[1];
        $this->assertInstanceOf(StreamHandler::class, $consoleHandler);

        $urlReflection = new \ReflectionProperty($consoleHandler, 'url');
        $urlReflection->setAccessible(true);
        $this->assertEquals('php://stdout', $urlReflection->getValue($consoleHandler));

        $levelReflection = new \ReflectionProperty($consoleHandler, 'level');
        $levelReflection->setAccessible(true);
        $this->assertEquals(LogLevel::INFO->toMonologLevel(), $levelReflection->getValue($consoleHandler));
    }

    public function testInvalidLogConfigLevel(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Invalid log config option "level": Must be a LogLevel enum');
        new Config(
            self::HOST,
            self::API_KEY,
            logConfig: ['level' => 'debug']
        );
    }

    public function testInvalidLogConfigHandlers(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Invalid log config option "handlers": Must be an array of HandlerInterface');
        new Config(
            self::HOST,
            self::API_KEY,
            logConfig: ['handlers' => ['not a handler']]
        );
    }

    public function testInvalidLogConfigConsole(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Invalid log config option "console": Must be a boolean');
        new Config(
            self::HOST,
            self::API_KEY,
            logConfig: ['console' => 'yes']
        );
    }

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
