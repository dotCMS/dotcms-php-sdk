<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

/**
 * Class NavigationItem
 *
 * Represents a navigation item from the dotCMS Navigation API.
 *
 * Note: This class doesn't extend AbstractModel because we have a well-defined
 * structure with public properties, making array access unnecessary. We only
 * implement JsonSerializable for JSON conversion.
 *
 * @package Dotcms\PhpSdk\Model
 */
class NavigationItem implements \JsonSerializable
{
    /**
     * @var NavigationItem[]|null Array of child navigation items
     */
    private ?array $childrenItems = null;

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
     * @param array<int, array<string, mixed>>|null $children Array of child navigation items data
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
        /** @var array<int, array<string, mixed>>|null Array of raw child navigation items data */
        private readonly ?array $children = null
    ) {
        // Map children to NavigationItem objects if they exist
        if ($this->children !== null) {
            $this->childrenItems = array_map(
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
                $this->children
            );
        }
    }

    /**
     * Check if this navigation item is a folder
     *
     * @return bool
     */
    public function isFolder(): bool
    {
        return $this->type === 'folder';
    }

    /**
     * Check if this navigation item is a page
     *
     * @return bool
     */
    public function isPage(): bool
    {
        return $this->type === 'htmlpage';
    }

    /**
     * Check if this navigation item has children
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->childrenItems !== null && count($this->childrenItems) > 0;
    }

    /**
     * Get the children as NavigationItem objects
     *
     * @return NavigationItem[]|null
     */
    public function getChildren(): ?array
    {
        return $this->childrenItems;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'folder' => $this->folder,
            'host' => $this->host,
            'languageId' => $this->languageId,
            'href' => $this->href,
            'title' => $this->title,
            'type' => $this->type,
            'hash' => $this->hash,
            'target' => $this->target,
            'order' => $this->order,
            'children' => $this->childrenItems,
        ];
    }
}
