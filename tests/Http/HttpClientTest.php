<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Http;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\Http\HttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    public function testClientCreation(): void
    {
        $config = new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key'
        );

        $httpClient = new HttpClient($config);
        $client = $httpClient->getClient();

        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCustomClientInjection(): void
    {
        $config = new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key'
        );

        $mockClient = $this->createMock(ClientInterface::class);
        $httpClient = new class ($config, $mockClient) extends HttpClient {
            public function __construct(Config $config, private readonly ClientInterface $mockClient)
            {
                parent::__construct($config);
            }

            protected function createClient(): ClientInterface
            {
                return $this->mockClient;
            }
        };

        $this->assertSame($mockClient, $httpClient->getClient());
    }
}
