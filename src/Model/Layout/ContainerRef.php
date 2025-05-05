<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

class ContainerRef
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
        public readonly array $contentlets = [],
        public readonly string $acceptTypes = '',
        public readonly int $maxContentlets = 0,
        public readonly ?int $variantId = null,
    ) {
    }
}
