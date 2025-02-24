<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Http;

use Dotcms\PhpSdk\Config\Config;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

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
}
