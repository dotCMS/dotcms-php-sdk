<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class Site implements \JsonSerializable
{
    /**
     * @param string $identifier The site identifier
     * @param string $hostname The site hostname
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $hostname,
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
            'hostname' => $this->hostname,
        ];
    }
} 