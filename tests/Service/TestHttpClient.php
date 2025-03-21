<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Service;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Http\Response as DotcmsResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Test-specific HttpClient that allows injecting a mock client
 */
class TestHttpClient extends HttpClient
{
    private MockHandler $mockHandler;

    public function __construct(MockHandler $mockHandler)
    {
        $config = new Config(
            'https://demo.dotcms.com/api/v1',
            'test-api-key',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 5,
                'http_errors' => false,
            ]
        );
        $this->mockHandler = $mockHandler;
        parent::__construct($config);
    }

    protected function createClient(): Client
    {
        $handlerStack = HandlerStack::create($this->mockHandler);

        return new Client(['handler' => $handlerStack]);
    }

    public function request(string $method, string $uri, array $options = []): DotcmsResponse
    {
        $client = $this->createClient();
        $response = $client->request($method, $uri, $options);

        return new TestResponse($response);
    }

    public function requestAsync(string $method, string $uri, array $options = []): PromiseInterface
    {
        $client = $this->createClient();
        $promise = $client->requestAsync($method, $uri, $options);

        return $promise->then(function ($response) {
            return new TestResponse($response);
        });
    }
}
