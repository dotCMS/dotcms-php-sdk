<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

use Dotcms\PhpSdk\Model\AbstractModel;

class Column extends AbstractModel
{
    /**
     * @param ContainerRef[] $containers Array of container references
     * @param int $width Width of the column
     * @param int $widthPercent Width percentage of the column
     * @param int $leftOffset Left offset percentage
     * @param string $styleClass CSS class for styling
     * @param bool $preview Whether the column is in preview mode
     * @param int $left Left position
     * @param array<string, mixed> $additionalProperties Additional properties
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
        array $additionalProperties = [],
    ) {
        $this->setAdditionalProperties($additionalProperties);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'containers' => $this->containers,
                'width' => $this->width,
                'widthPercent' => $this->widthPercent,
                'leftOffset' => $this->leftOffset,
                'styleClass' => $this->styleClass,
                'preview' => $this->preview,
                'left' => $this->left,
            ],
            $this->getAdditionalProperties()
        );
    }
}
