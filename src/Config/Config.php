<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Config;

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
} 