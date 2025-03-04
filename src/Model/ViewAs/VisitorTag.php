<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\ViewAs;

class VisitorTag implements \JsonSerializable
{
    /**
     * @param string $tag Tag name
     * @param int $count Tag count
     */
    public function __construct(
        public readonly string $tag,
        public readonly int $count,
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
            'tag' => $this->tag,
            'count' => $this->count,
        ];
    }
}
