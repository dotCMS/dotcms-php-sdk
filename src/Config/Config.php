<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Config;

use Dotcms\PhpSdk\Exception\ConfigException;

class Config
{
    public function __construct(
        private readonly string $host,
        private readonly string $apiKey,
        private readonly array $clientOptions = [
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

    public function getClientOptions(): array
    {
        return array_merge($this->clientOptions, [
            'base_uri' => $this->host,
            'headers' => array_merge(
                $this->clientOptions['headers'] ?? [],
                ['Authorization' => 'Bearer ' . $this->apiKey]
            ),
        ]);
    }

    private function validateHost(string $host): void
    {
        if (!filter_var($host, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//', $host)) {
            throw ConfigException::invalidHost($host);
        }
    }

    private function validateApiKey(string $apiKey): void
    {
        if (trim($apiKey) === '') {
            throw ConfigException::emptyApiKey();
        }
    }

    private function validateClientOptions(array $options): void
    {
        foreach ($options as $key => $value) {
            match ($key) {
                'headers' => $this->validateHeaders($value),
                'verify' => $this->validateBoolean($key, $value),
                'timeout', 'connect_timeout' => $this->validateTimeout($key, $value),
                'http_errors', 'allow_redirects' => $this->validateBoolean($key, $value),
                default => throw ConfigException::invalidClientOption($key, 'Unknown option')
            };
        }
    }

    private function validateHeaders(mixed $headers): void
    {
        if (!is_array($headers)) {
            throw ConfigException::invalidClientOption('headers', 'Must be an array');
        }

        foreach ($headers as $name => $value) {
            if (!is_string($name) || !is_string($value)) {
                throw ConfigException::invalidClientOption(
                    'headers',
                    'Header names and values must be strings'
                );
            }
        }
    }

    private function validateTimeout(string $key, mixed $value): void
    {
        if (!is_int($value) || $value < 0) {
            throw ConfigException::invalidClientOption(
                $key,
                'Must be a positive integer'
            );
        }
    }

    private function validateBoolean(string $key, mixed $value): void
    {
        if (!is_bool($value)) {
            throw ConfigException::invalidClientOption(
                $key,
                'Must be a boolean'
            );
        }
    }
} 