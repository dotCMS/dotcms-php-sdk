<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\ViewAs;

class VisitorTag
{
    /**
     * @param string $tag Tag name
     * @param int $count Tag count
     */
    public function __construct(
        public readonly string $tag,
        public readonly int $count
    ) {
    }
}
