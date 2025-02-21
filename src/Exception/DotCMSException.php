<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Exception;

class DotCMSException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null,
        private readonly ?array $context = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): ?array
    {
        return $this->context;
    }
} 