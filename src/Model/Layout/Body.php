<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

class Body
{
    /**
     * @param Row[] $rows Array of rows
     */
    public function __construct(
        public readonly array $rows = [],
    ) {
    }
}
