<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

class VanityUrl implements \JsonSerializable
{
    /**
     * @param string $url The vanity URL
     * @param string $forwardTo The URL to forward to
     */
    public function __construct(
        public readonly string $url,
        public readonly string $forwardTo,
    ) {
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'forwardTo' => $this->forwardTo,
        ];
    }
}
