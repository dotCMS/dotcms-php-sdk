<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

class Column
{
    /**
     * @param ContainerRef[] $containers Array of container references
     * @param int $width Width of the column
     * @param int $widthPercent Width percentage of the column
     * @param int $leftOffset Left offset percentage
     * @param string $styleClass CSS class for styling
     * @param bool $preview Whether the column is in preview mode
     * @param int $left Left position
     */
    public function __construct(
        /** @var ContainerRef[] */
        public readonly array $containers,
        public readonly int $width,
        public readonly int $widthPercent,
        public readonly int $leftOffset,
        public readonly string $styleClass,
        public readonly bool $preview = false,
        public readonly int $left = 0,
    ) {
    }
}
