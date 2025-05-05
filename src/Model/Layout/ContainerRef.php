<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

use Dotcms\PhpSdk\Model\Content\Contentlet;

class ContainerRef
{
    /**
     * @param string $identifier The container identifier
     * @param string $uuid The container UUID
     * @param string[] $historyUUIDs Array of history UUIDs
     * @param Contentlet[] $contentlets Array of contentlets in this container
     * @param string $acceptTypes Comma-separated list of accepted content types
     * @param int $maxContentlets Maximum number of contentlets allowed
     * @param int|null $variantId Optional variant ID for personalization
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $uuid,
        public readonly array $historyUUIDs = [],
        public readonly array $contentlets = [],
        public readonly string $acceptTypes = '',
        public readonly int $maxContentlets = 0,
        public readonly ?string $variantId = null,
    ) {
    }
}
