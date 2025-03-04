<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class Row implements \JsonSerializable
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

    /**
     * Specify data which should be serialized to JSON
     * 
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'columns' => $this->columns,
            'styleClass' => $this->styleClass,
        ];
    }
} 