<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Dotcms\PhpSdk\Model\ViewAs\Visitor;

class ViewAs implements \JsonSerializable
{
    /**
     * @param Visitor $visitor Visitor context information
     * @param array $language Language details
     * @param string $mode The view mode (LIVE, PREVIEW, EDIT_MODE)
     */
    public function __construct(
        public readonly Visitor $visitor,
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
