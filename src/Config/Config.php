<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Config;

use Dotcms\PhpSdk\Exception\ConfigException;
use GuzzleHttp\RequestOptions;

class Config
{
    /**
     * @var array{
     *     headers?: array<string, string>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * }
     */
    private array $validatedOptions;

    private const ALLOWED_OPTIONS = [
        RequestOptions::HEADERS,
        RequestOptions::VERIFY,
        RequestOptions::TIMEOUT,
        RequestOptions::CONNECT_TIMEOUT,
        RequestOptions::HTTP_ERRORS,
        RequestOptions::ALLOW_REDIRECTS,
    ];

    /**
     * @param array{
     *     headers?: array<string, string>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * } $clientOptions
     */
    public function __construct(
        private readonly string $host,
        private readonly string $apiKey,
        array $clientOptions = [
            'headers' => [],
            'verify' => true,
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => true,
            'allow_redirects' => true,
        ]
    ) {
        $this->validateHost($host);
        $this->validateApiKey($apiKey);
        $this->validateClientOptions($clientOptions);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return array{
     *     base_uri: string,
     *     headers: array<string, string>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * }
     */
    public function getClientOptions(): array
    {
        /** @var array<string, string> $headers */
        $headers = $this->validatedOptions['headers'] ?? [];

        return array_merge($this->validatedOptions, [
            'base_uri' => $this->host,
            'headers' => array_merge(
                $headers,
                ['Authorization' => 'Bearer ' . $this->apiKey]
            ),
        ]);
    }

    private function validateHost(string $host): void
    {
        if (! filter_var($host, FILTER_VALIDATE_URL) || ! preg_match('/^https?:\/\//', $host)) {
            throw ConfigException::invalidHost($host);
        }
    }

    private function validateApiKey(string $apiKey): void
    {
        if (trim($apiKey) === '') {
            throw ConfigException::emptyApiKey();
        }
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
    private function validateClientOptions(array $options): void
    {
        // Check for unknown options
        $unknownOptions = array_diff(array_keys($options), self::ALLOWED_OPTIONS);
        if (! empty($unknownOptions)) {
            throw ConfigException::invalidClientOption(
                (string) array_key_first($unknownOptions),
                'Unknown option'
            );
        }

        // Validate headers
        if (isset($options['headers'])) {
            if (! is_array($options['headers'])) {
                throw ConfigException::invalidClientOption(
                    'headers',
                    'Must be an array'
                );
            }

            foreach ($options['headers'] as $name => $value) {
                if (! is_string($name) || ! is_string($value)) {
                    throw ConfigException::invalidClientOption(
                        'headers',
                        'Header names and values must be strings'
                    );
                }
            }
        }

        // Validate timeouts
        foreach (['timeout', 'connect_timeout'] as $option) {
            if (isset($options[$option]) && (! is_int($options[$option]) || $options[$option] < 0)) {
                throw ConfigException::invalidClientOption(
                    $option,
                    'Must be a positive integer'
                );
            }
        }

        // Validate booleans
        foreach (['verify', 'http_errors', 'allow_redirects'] as $option) {
            if (isset($options[$option]) && ! is_bool($options[$option])) {
                throw ConfigException::invalidClientOption(
                    $option,
                    'Must be a boolean'
                );
            }
        }

        $this->validatedOptions = $options;
    }
}
