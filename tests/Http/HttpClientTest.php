<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Http;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\Exception\HttpException;
use Dotcms\PhpSdk\Exception\ResponseException;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Http\Response;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

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
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"data": "test"}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(200);

        $this->mockClient->expects($this->once())
            ->method('request')
            ->with('GET', $uri, $options)
            ->willReturn($mockResponse);

        $response = $this->httpClient->get($uri, $options);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals(['data' => 'test'], $response->toArray());
    }

    public function testPostRequest(): void
    {
        $uri = '/api/v1/content';
        $options = ['json' => ['title' => 'Test']];
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"id": 123, "title": "Test"}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(201);

        $this->mockClient->expects($this->once())
            ->method('request')
            ->with('POST', $uri, $options)
            ->willReturn($mockResponse);

        $response = $this->httpClient->post($uri, $options);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertEquals(['id' => 123, 'title' => 'Test'], $response->toArray());
    }

    public function testPutRequest(): void
    {
        $uri = '/api/v1/content/123';
        $options = ['json' => ['title' => 'Updated']];
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"id": 123, "title": "Updated"}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(200);

        $this->mockClient->expects($this->once())
            ->method('request')
            ->with('PUT', $uri, $options)
            ->willReturn($mockResponse);

        $response = $this->httpClient->put($uri, $options);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals(['id' => 123, 'title' => 'Updated'], $response->toArray());
    }

    public function testDeleteRequest(): void
    {
        $uri = '/api/v1/content/123';
        $options = [];
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"success": true}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(204);

        $this->mockClient->expects($this->once())
            ->method('request')
            ->with('DELETE', $uri, $options)
            ->willReturn($mockResponse);

        $response = $this->httpClient->delete($uri, $options);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(204, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->toArray());
    }

    public function testGenericRequest(): void
    {
        $method = 'PATCH';
        $uri = '/api/v1/content/123';
        $options = ['json' => ['status' => 'published']];
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"status": "published"}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(200);

        $this->mockClient->expects($this->once())
            ->method('request')
            ->with($method, $uri, $options)
            ->willReturn($mockResponse);

        $response = $this->httpClient->request($method, $uri, $options);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(['status' => 'published'], $response->toArray());
    }

    public function testInvalidJsonResponse(): void
    {
        $uri = '/api/v1/content';
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"invalid": json}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(200);

        $this->mockClient->method('request')
            ->willReturn($mockResponse);

        $response = $this->httpClient->get($uri);

        $this->expectException(ResponseException::class);
        $response->toArray();
    }

    public function testEmptyResponse(): void
    {
        $uri = '/api/v1/content';
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(204);

        $this->mockClient->method('request')
            ->willReturn($mockResponse);

        $response = $this->httpClient->get($uri);
        $this->assertEquals([], $response->toArray());
    }

    public function testNonArrayResponse(): void
    {
        $uri = '/api/v1/content';
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('"string response"');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $this->mockClient->method('request')
            ->willReturn($mockResponse);

        $response = $this->httpClient->get($uri);

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Response data is not an array. Got string: "string response"');
        $response->toArray();
    }

    public function testNonArrayResponseWithNumber(): void
    {
        $uri = '/api/v1/content';
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('42');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $this->mockClient->method('request')
            ->willReturn($mockResponse);

        $response = $this->httpClient->get($uri);

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Response data is not an array. Got integer: 42');
        $response->toArray();
    }

    public function testNonArrayResponseWithBoolean(): void
    {
        $uri = '/api/v1/content';
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('true');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(200);

        $this->mockClient->method('request')
            ->willReturn($mockResponse);

        $response = $this->httpClient->get($uri);

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Response data is not an array. Got boolean: true');
        $response->toArray();
    }

    public function testBadRequestError(): void
    {
        $uri = '/api/v1/content';
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"error": "Invalid input"}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(400);

        $mockResponse->method('getReasonPhrase')
            ->willReturn('Error');

        $mockResponse->method('getHeaders')
            ->willReturn([]);

        $this->mockClient->method('request')
            ->willReturn($mockResponse);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Bad Request: HTTP 400 Error');
        $this->expectExceptionCode(400);

        $this->httpClient->get($uri);
    }

    public function testUnauthorizedError(): void
    {
        $uri = '/api/v1/content';
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"error": "Invalid token"}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(401);

        $mockResponse->method('getReasonPhrase')
            ->willReturn('Error');

        $mockResponse->method('getHeaders')
            ->willReturn([]);

        $this->mockClient->method('request')
            ->willReturn($mockResponse);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Unauthorized: HTTP 401 Error');
        $this->expectExceptionCode(401);

        $this->httpClient->get($uri);
    }

    public function testNotFoundError(): void
    {
        $uri = '/api/v1/content/123';
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"error": "Content not found"}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(404);

        $mockResponse->method('getReasonPhrase')
            ->willReturn('Error');

        $mockResponse->method('getHeaders')
            ->willReturn([]);

        $this->mockClient->method('request')
            ->willReturn($mockResponse);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Not Found: HTTP 404 Error');
        $this->expectExceptionCode(404);

        $this->httpClient->get($uri);
    }

    public function testServerError(): void
    {
        $uri = '/api/v1/content';
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"error": "Internal server error"}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $mockResponse->method('getStatusCode')
            ->willReturn(500);

        $mockResponse->method('getReasonPhrase')
            ->willReturn('Error');

        $mockResponse->method('getHeaders')
            ->willReturn([]);

        $this->mockClient->method('request')
            ->willReturn($mockResponse);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Internal Server Error: HTTP 500 Error');
        $this->expectExceptionCode(500);

        $this->httpClient->get($uri);
    }
}
