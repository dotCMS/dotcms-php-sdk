<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Exception;

class ConfigException extends DotCMSException
{
    public static function invalidHost(string $host): self
    {
        return new self(
            sprintf('Invalid host URL: "%s". Host must be a valid URL starting with http:// or https://', $host),
            context: ['host' => $host]
        );
    }

    public static function emptyApiKey(): self
    {
        return new self('API key cannot be empty');
    }

    public static function invalidClientOption(string $option, string $message): self
    {
        return new self(
            sprintf('Invalid client option "%s": %s', $option, $message),
            context: ['option' => $option]
        );
    }
} 