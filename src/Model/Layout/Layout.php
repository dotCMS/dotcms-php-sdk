<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

class Layout implements \JsonSerializable
{
    /**
     * @param string|null $width Layout width
     * @param string $title Layout title
     * @param bool $header Whether to show header
     * @param bool $footer Whether to show footer
     * @param array{rows: Row[]} $body Layout body containing rows
     * @param array{containers: ContainerRef[], location: string, width: string, widthPercent: int, preview: bool} $sidebar Sidebar configuration
     * @param int $version Layout version
     */
    public function __construct(
        public readonly ?string $width = null,
        public readonly string $title = '',
        public readonly bool $header = true,
        public readonly bool $footer = true,
        public readonly array $body = ['rows' => []],
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
     * Get rows from the body
     *
     * @return Row[] Array of rows
     */
    public function getRows(): array
    {
        return $this->body['rows'] ?? [];
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
        return [
            'width' => $this->width,
            'title' => $this->title,
            'header' => $this->header,
            'footer' => $this->footer,
            'body' => $this->body,
            'sidebar' => $this->sidebar,
            'version' => $this->version,
        ];
    }
}
