<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

class Layout
{
    /**
     * @param string|null $width Layout width
     * @param string $title Layout title
     * @param bool $header Whether to show header
     * @param bool $footer Whether to show footer
     * @param Body $body The body containing rows
     * @param array{containers: ContainerRef[], location: string, width: string, widthPercent: int, preview: bool} $sidebar Sidebar configuration
     * @param int $version Layout version
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
    ) {
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
}
