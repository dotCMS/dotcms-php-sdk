<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class ContainerRef implements \JsonSerializable
{
    /**
     * @param string $identifier The container identifier
     * @param string $uuid The container UUID
     * @param string[] $historyUUIDs Array of history UUIDs
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $uuid,
        public readonly array $historyUUIDs = [],
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
            'identifier' => $this->identifier,
            'uuid' => $this->uuid,
            'historyUUIDs' => $this->historyUUIDs,
        ];
    }
} 