<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class Contentlet implements \JsonSerializable
{
    /**
     * @param string $identifier The contentlet identifier
     * @param string $inode The contentlet inode
     * @param string $title The contentlet title
     * @param string $contentType The content type
     * @param array $data The full contentlet data
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $inode,
        public readonly string $title,
        public readonly string $contentType,
        public readonly array $data,
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
            'inode' => $this->inode,
            'title' => $this->title,
            'contentType' => $this->contentType,
            'data' => $this->data,
        ];
    }
} 