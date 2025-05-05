<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

use Dotcms\PhpSdk\Model\AbstractModel;

class Layout extends AbstractModel
{
    /**
     * @param string|null $width Layout width
     * @param string $title Layout title
     * @param bool $header Whether to show header
     * @param bool $footer Whether to show footer
     * @param Body $body The body containing rows
     * @param array{containers: ContainerRef[], location: string, width: string, widthPercent: int, preview: bool} $sidebar Sidebar configuration
     * @param int $version Layout version
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly ?string $width = null,
        public readonly string $title = '',
        public readonly bool $header = true,
        public readonly bool $footer = true,
        public readonly Body $body = new Body(),
        public readonly array $sidebar = [
            'containers' => [],
            'location' => '',
            'width' => 'small',
            'widthPercent' => 20,
            'preview' => false,
        ],
        public readonly int $version = 1,
        array $additionalProperties = [],
    ) {
        $this->setAdditionalProperties($additionalProperties);
    }

    /**
     * Get containers from the sidebar
     *
     * @return ContainerRef[] Array of container references
     */
    public function getSidebarContainers(): array
    {
        return $this->sidebar['containers'] ?? [];
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
                'width' => $this->width,
                'title' => $this->title,
                'header' => $this->header,
                'footer' => $this->footer,
                'body' => [
                    'rows' => $this->body->rows,
                ],
                'sidebar' => $this->sidebar,
                'version' => $this->version,
            ],
            $this->getAdditionalProperties()
        );
    }
}
