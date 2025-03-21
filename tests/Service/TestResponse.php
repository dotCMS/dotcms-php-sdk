<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Service;

use Dotcms\PhpSdk\Exception\ResponseException;
use Dotcms\PhpSdk\Http\Response as DotcmsResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Custom Response class for testing
 */
class TestResponse extends DotcmsResponse
{
    private ResponseInterface $originalResponse;

    public function __construct(ResponseInterface $response)
    {
        $this->originalResponse = $response;
        parent::__construct($response);
    }

    public function toArray(): array
    {
        $body = (string)$this->originalResponse->getBody();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ResponseException('Invalid JSON response: ' . json_last_error_msg());
        }

        return $data;
    }
}
