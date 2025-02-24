<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Http;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\Http\HttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class HttpClientTest extends TestCase
{
    private Config $config;

    private ClientInterface $mockClient;

    private HttpClient $httpClient;

    protected function setUp(): void
    {
        $this->config = new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key'
        );

        $this->mockClient = $this->createMock(ClientInterface::class);
        $this->httpClient = new class ($this->config, $this->mockClient) extends HttpClient {
            public function __construct(Config $config, private readonly ClientInterface $mockClient)
            {
                parent::__construct($config);
            }

            protected function createClient(): ClientInterface
            {
                return $this->mockClient;
            }
        };
    }

    public function testClientCreation(): void
    {
        $httpClient = new HttpClient($this->config);
        $client = $httpClient->getClient();

        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCustomClientInjection(): void
    {
        $this->assertSame($this->mockClient, $this->httpClient->getClient());
    }

    public function testGetRequest(): void
    {
        $uri = '/api/v1/content';
        $options = ['query' => ['limit' => 10]];
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $this->mockClient->expects($this->once())
            ->method('request')
            ->with('GET', $uri, $options)
            ->willReturn($expectedResponse);

        $response = $this->httpClient->get($uri, $options);
        $this->assertSame($expectedResponse, $response);
    }

    public function testPostRequest(): void
    {
        $uri = '/api/v1/content';
        $options = ['json' => ['title' => 'Test']];
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $this->mockClient->expects($this->once())
            ->method('request')
            ->with('POST', $uri, $options)
            ->willReturn($expectedResponse);

        $response = $this->httpClient->post($uri, $options);
        $this->assertSame($expectedResponse, $response);
    }

    public function testPutRequest(): void
    {
        $uri = '/api/v1/content/123';
        $options = ['json' => ['title' => 'Updated']];
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $this->mockClient->expects($this->once())
            ->method('request')
            ->with('PUT', $uri, $options)
            ->willReturn($expectedResponse);

        $response = $this->httpClient->put($uri, $options);
        $this->assertSame($expectedResponse, $response);
    }

    public function testDeleteRequest(): void
    {
        $uri = '/api/v1/content/123';
        $options = [];
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $this->mockClient->expects($this->once())
            ->method('request')
            ->with('DELETE', $uri, $options)
            ->willReturn($expectedResponse);

        $response = $this->httpClient->delete($uri, $options);
        $this->assertSame($expectedResponse, $response);
    }

    public function testGenericRequest(): void
    {
        $method = 'PATCH';
        $uri = '/api/v1/content/123';
        $options = ['json' => ['status' => 'published']];
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $this->mockClient->expects($this->once())
            ->method('request')
            ->with($method, $uri, $options)
            ->willReturn($expectedResponse);

        $response = $this->httpClient->request($method, $uri, $options);
        $this->assertSame($expectedResponse, $response);
    }
}
