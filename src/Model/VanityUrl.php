<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

class VanityUrl extends AbstractModel
{
    /**
     * @param string $url The vanity URL
     * @param string $forwardTo The URL to forward to
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly string $url,
        public readonly string $forwardTo,
        array $additionalProperties = [],
    ) {
        $this->setAdditionalProperties($additionalProperties);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'url' => $this->url,
                'forwardTo' => $this->forwardTo,
            ],
            $this->getAdditionalProperties()
        );
    }
}
