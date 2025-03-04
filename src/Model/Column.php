<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class Column implements \JsonSerializable
{
    /**
     * @param ContainerRef[] $containers Array of container references
     * @param int $width Width of the column
     * @param int $widthPercent Width percentage of the column
     * @param int $leftOffset Left offset percentage
     * @param string $styleClass CSS class for styling
     */
    public function __construct(
        /** @var ContainerRef[] */
        public readonly array $containers,
        public readonly int $width,
        public readonly int $widthPercent,
        public readonly int $leftOffset,
        public readonly string $styleClass,
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
            'containers' => $this->containers,
            'width' => $this->width,
            'widthPercent' => $this->widthPercent,
            'leftOffset' => $this->leftOffset,
            'styleClass' => $this->styleClass,
        ];
    }
} 