<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

class Row
{
    /**
     * @param Column[] $columns Array of Column objects
     * @param string|null $styleClass CSS class for styling
     */
    public function __construct(
        public readonly array $columns,
        public readonly ?string $styleClass = null,
    ) {
    }
}
