<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class Template implements \JsonSerializable
{
    /**
     * @param string $identifier The template identifier
     * @param string $title The template title
     * @param bool $drawed Whether the template is drawn in the layout designer
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $title,
        public readonly bool $drawed,
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
            'identifier' => $this->identifier,
            'title' => $this->title,
            'drawed' => $this->drawed,
        ];
    }
} 