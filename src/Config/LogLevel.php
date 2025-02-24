<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Config;

use Monolog\Level;

enum LogLevel: string
{
    case DEBUG = 'debug';
    case INFO = 'info';
    case WARNING = 'warning';
    case ERROR = 'error';

    public function toMonologLevel(): Level
    {
        return match($this) {
            self::DEBUG => Level::Debug,
            self::INFO => Level::Info,
            self::WARNING => Level::Warning,
            self::ERROR => Level::Error,
        };
    }
} 