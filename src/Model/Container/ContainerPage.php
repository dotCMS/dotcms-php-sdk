<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Container;

use Dotcms\PhpSdk\Model\Content\Contentlet;

class ContainerPage
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
}
