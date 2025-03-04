<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class Container implements \JsonSerializable
{
    /**
     * @param array $containerStructures Container structure details
     * @param array $rendered Rendered content
     */
    public function __construct(
        public readonly array $containerStructures = [],
        public readonly array $rendered = [],
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
            'containerStructures' => $this->containerStructures,
            'rendered' => $this->rendered,
        ];
    }
} 