<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Http;

use Dotcms\PhpSdk\Config\Config;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;

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
    public function request(string $method, string $uri, array $options = []): Response
    {
        return new Response($this->client->request($method, $uri, $options));
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
    public function requestAsync(string $method, string $uri, array $options = []): PromiseInterface
    {
        return $this->client->requestAsync($method, $uri, $options)
            ->then(function ($response) {
                return new Response($response);
            });
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
    public function get(string $uri, array $options = []): Response
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
    public function post(string $uri, array $options = []): Response
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
    public function put(string $uri, array $options = []): Response
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
    public function delete(string $uri, array $options = []): Response
    {
        return $this->request('DELETE', $uri, $options);
    }
}
