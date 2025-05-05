<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Content;

/**
 * Class NavigationItem
 *
 * Represents a navigation item from the dotCMS Navigation API.
 *
 * @package Dotcms\PhpSdk\Model
 */
class NavigationItem
{
    /**
     * @param string|null $code The code of the navigation item
     * @param string|null $folder The folder identifier
     * @param string $host The host identifier
     * @param int $languageId The language ID
     * @param string $href The URL of the navigation item
     * @param string $title The title of the navigation item
     * @param string $type The type of the navigation item (folder, htmlpage, etc.)
     * @param int $hash The hash of the navigation item
     * @param string $target The target attribute for links (_self, _blank, etc.)
     * @param int $order The order of the navigation item
     * @param array<int, array<string, mixed>>|null $rawChildren Array of child navigation items data
     */
    public function __construct(
        public readonly ?string $code,
        public readonly ?string $folder,
        public readonly string $host,
        public readonly int $languageId,
        public readonly string $href,
        public readonly string $title,
        public readonly string $type,
        public readonly int $hash,
        public readonly string $target,
        public readonly int $order,
        /** @var array<int, array<string, mixed>>|null Array of child navigation items data */
        private readonly ?array $rawChildren = null
    ) {
        $this->children = $this->mapChildren();
    }

    /** @var NavigationItem[]|null */
    public readonly ?array $children;

    /**
     * Check if this navigation item is a folder
     */
    public function isFolder(): bool
    {
        return $this->type === 'folder';
    }

    /**
     * Check if this navigation item is a page
     */
    public function isPage(): bool
    {
        return $this->type === 'htmlpage';
    }

    /**
     * Check if this navigation item has children
     */
    public function hasChildren(): bool
    {
        return $this->children !== null && count($this->children) > 0;
    }

    /**
     * Map raw children data to NavigationItem objects
     *
     * @return NavigationItem[]|null
     */
    private function mapChildren(): ?array
    {
        if ($this->rawChildren === null) {
            return null;
        }

        return array_map(
            function (array $child): NavigationItem {
                $code = null;
                if (isset($child['code']) && (is_string($child['code']) || is_null($child['code']))) {
                    $code = $child['code'];
                }

                $folder = null;
                if (isset($child['folder']) && (is_string($child['folder']) || is_null($child['folder']))) {
                    $folder = $child['folder'];
                }

                $host = '';
                if (isset($child['host']) && is_string($child['host'])) {
                    $host = $child['host'];
                }

                $languageId = 1; // Default value
                if (isset($child['languageId']) && is_numeric($child['languageId'])) {
                    $languageId = (int)$child['languageId'];
                }

                $href = '';
                if (isset($child['href']) && is_string($child['href'])) {
                    $href = $child['href'];
                }

                $title = '';
                if (isset($child['title']) && is_string($child['title'])) {
                    $title = $child['title'];
                }

                $type = '';
                if (isset($child['type']) && is_string($child['type'])) {
                    $type = $child['type'];
                }

                $hash = 0; // Default value
                if (isset($child['hash']) && is_numeric($child['hash'])) {
                    $hash = (int)$child['hash'];
                }

                $target = '_self'; // Default value
                if (isset($child['target']) && is_string($child['target'])) {
                    $target = $child['target'];
                }

                $order = 0; // Default value
                if (isset($child['order']) && is_numeric($child['order'])) {
                    $order = (int)$child['order'];
                }

                $children = null;
                if (isset($child['children']) && is_array($child['children'])) {
                    $children = $child['children'];
                }

                return new self(
                    $code,
                    $folder,
                    $host,
                    $languageId,
                    $href,
                    $title,
                    $type,
                    $hash,
                    $target,
                    $order,
                    $children
                );
            },
            $this->rawChildren
        );
    }
}
