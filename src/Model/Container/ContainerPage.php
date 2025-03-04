<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Container;

use Dotcms\PhpSdk\Model\Contentlet;

class ContainerPage implements \JsonSerializable
{
    /**
     * @param Container $container Container details
     * @param ContainerStructure[] $containerStructures Container structure details
     * @param array<string, string> $rendered Rendered content keyed by UUID
     * @param array<string, Contentlet[]> $contentlets Contentlets keyed by UUID
     */
    public function __construct(
        public readonly Container $container,
        public readonly array $containerStructures = [],
        public readonly array $rendered = [],
        public readonly array $contentlets = [],
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
