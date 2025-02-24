<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Exception;

class ResponseException extends DotCMSException
{
    public static function invalidJson(string $reason): self
    {
        return new self(
            sprintf('Failed to parse JSON response: %s', $reason),
            context: ['reason' => $reason]
        );
    }

    public static function invalidType(string $actualType, string $preview): self
    {
        return new self(
            sprintf('Response data is not an array. Got %s: %s', $actualType, $preview),
            context: [
                'type' => $actualType,
                'preview' => $preview,
            ]
        );
    }
}
