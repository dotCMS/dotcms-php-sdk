<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

class Template extends AbstractModel
{
    /**
     * @param string $identifier The template identifier
     * @param string $title The template title
     * @param bool $drawed Whether the template is drawn in the layout designer
     * @param string $inode The template inode
     * @param string $friendlyName The template friendly name
     * @param bool|string $header Whether the template has a header
     * @param bool|string $footer Whether the template has a footer
     * @param bool $working Whether the template is in working state
     * @param bool $live Whether the template is live
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $title,
        public readonly bool $drawed,
        public readonly string $inode = '',
        public readonly string $friendlyName = '',
        public readonly bool|string $header = true,
        public readonly bool|string $footer = true,
        public readonly bool $working = false,
        public readonly bool $live = false,
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
                'identifier' => $this->identifier,
                'title' => $this->title,
                'drawed' => $this->drawed,
                'inode' => $this->inode,
                'friendlyName' => $this->friendlyName,
                'header' => $this->header,
                'footer' => $this->footer,
                'working' => $this->working,
                'live' => $this->live,
            ],
            $this->getAdditionalProperties()
        );
    }
}
