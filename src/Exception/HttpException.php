<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Exception;

use Psr\Http\Message\ResponseInterface;

class HttpException extends DotCMSException
{
    private const ERROR_MESSAGES = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        408 => 'Request Timeout',
        409 => 'Conflict',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
    ];

    public static function fromResponse(ResponseInterface $response, ?string $responseBody = null): self
    {
        $statusCode = $response->getStatusCode();
        $message = self::ERROR_MESSAGES[$statusCode] ?? 'HTTP Error';

        return new self(
            sprintf(
                '%s: HTTP %d %s',
                $message,
                $statusCode,
                $response->getReasonPhrase()
            ),
            $statusCode,
            context: [
                'status_code' => $statusCode,
                'reason' => $response->getReasonPhrase(),
                'headers' => $response->getHeaders(),
                'body' => $responseBody,
            ]
        );
    }
}
