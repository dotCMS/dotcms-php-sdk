<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class ViewAs implements \JsonSerializable
{
    /**
     * @param array $visitor Visitor context information
     * @param array $language Language details
     * @param string $mode The view mode (LIVE, PREVIEW, EDIT_MODE)
     */
    public function __construct(
        public readonly array $visitor,
        public readonly array $language,
        public readonly string $mode,
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
            'visitor' => $this->visitor,
            'language' => $this->language,
            'mode' => $this->mode,
        ];
    }
} 