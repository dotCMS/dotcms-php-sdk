<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class ContainerPage implements \JsonSerializable
{
    /**
     * @param ContainerStructure[] $containerStructures Container structure details
     * @param array<string, string> $rendered Rendered content keyed by UUID
     * @param array<string, Contentlet[]> $contentlets Contentlets keyed by UUID
     * @param Container $container Container details
     */
    public function __construct(
        public readonly array $containerStructures = [],
        public readonly array $rendered = [],
        public readonly array $contentlets = [],
        public readonly Container $container,
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
            'contentlets' => $this->contentlets,
            'container' => $this->container,
        ];
    }
} 