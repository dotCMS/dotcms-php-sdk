<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Http;

use Dotcms\PhpSdk\Config\Config;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    private readonly ClientInterface $client;

    public function __construct(private readonly Config $config)
    {
        $this->client = $this->createClient();
    }

    protected function createClient(): ClientInterface
    {
        return new Client($this->config->getClientOptions());
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @param array{
     *     headers?: array<string, string>,
     *     query?: array<string, mixed>,
     *     json?: array<string, mixed>|string,
     *     form_params?: array<string, mixed>,
     *     multipart?: array<array{name: string, contents: mixed, headers?: array<string, string>}>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * } $options
     */
    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }

    /**
     * @param array{
     *     headers?: array<string, string>,
     *     query?: array<string, mixed>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * } $options
     */
    public function get(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('GET', $uri, $options);
    }

    /**
     * @param array{
     *     headers?: array<string, string>,
     *     json?: array<string, mixed>|string,
     *     form_params?: array<string, mixed>,
     *     multipart?: array<array{name: string, contents: mixed, headers?: array<string, string>}>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * } $options
     */
    public function post(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * @param array{
     *     headers?: array<string, string>,
     *     json?: array<string, mixed>|string,
     *     form_params?: array<string, mixed>,
     *     multipart?: array<array{name: string, contents: mixed, headers?: array<string, string>}>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * } $options
     */
    public function put(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('PUT', $uri, $options);
    }

    /**
     * @param array{
     *     headers?: array<string, string>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * } $options
     */
    public function delete(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('DELETE', $uri, $options);
    }
}
