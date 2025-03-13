<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Dotcms\PhpSdk\Model\ViewAs\Visitor;

class ViewAs extends AbstractModel
{
    /**
     * @param Visitor $visitor Visitor context information
     * @param array<string, mixed> $language Language details
     * @param string $mode The view mode (LIVE, PREVIEW, EDIT_MODE)
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly Visitor $visitor,
        public readonly array $language,
        public readonly string $mode,
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
                'visitor' => $this->visitor,
                'language' => $this->language,
                'mode' => $this->mode,
            ],
            $this->getAdditionalProperties()
        );
    }
}
