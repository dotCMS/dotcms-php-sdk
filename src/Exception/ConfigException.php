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

    public static function invalidClientOption(string $option, string $reason): self
    {
        return new self(
            sprintf('Invalid client option "%s": %s', $option, $reason),
            context: [
                'option' => $option,
                'reason' => $reason,
            ]
        );
    }

    /**
     * @param array<string> $allowedLevels
     */
    public static function invalidLogLevel(string $level, array $allowedLevels): self
    {
        return new self(
            sprintf(
                'Invalid log level "%s". Allowed levels are: %s',
                $level,
                implode(', ', $allowedLevels)
            ),
            context: [
                'level' => $level,
                'allowed_levels' => $allowedLevels,
            ]
        );
    }

    public static function invalidLogConfig(string $option, string $reason): self
    {
        return new self(sprintf('Invalid log config option "%s": %s', $option, $reason));
    }
}
