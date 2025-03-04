<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class Layout implements \JsonSerializable
{
    /**
     * @param Row[] $rows Array of Row objects
     * @param array $sidebar Sidebar configuration
     */
    public function __construct(
        public readonly array $rows,
        public readonly array $sidebar,
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
            'rows' => $this->rows,
            'sidebar' => $this->sidebar,
        ];
    }
} 