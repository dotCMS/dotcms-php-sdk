<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

/**
 * Class NavigationItem
 *
 * Represents a navigation item from the dotCMS Navigation API.
 *
 * @package Dotcms\PhpSdk\Model
 */
class NavigationItem extends AbstractModel
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
     * @param array|null $children Array of child navigation items data
     */
    public function __construct(
        private readonly ?string $code,
        private readonly ?string $folder,
        private readonly string $host,
        private readonly int $languageId,
        private readonly string $href,
        private readonly string $title,
        private readonly string $type,
        private readonly int $hash,
        private readonly string $target,
        private readonly int $order,
        private readonly ?array $children = null
    ) {
        // Map children to NavigationItem objects if they exist
        if ($this->children !== null) {
            $this->childrenItems = array_map(
                fn($child) => new self(
                    $child['code'] ?? null,
                    $child['folder'] ?? null,
                    $child['host'],
                    $child['languageId'],
                    $child['href'],
                    $child['title'],
                    $child['type'],
                    $child['hash'],
                    $child['target'],
                    $child['order'],
                    $child['children'] ?? null
                ),
                $this->children
            );
        }
    }

    /**
     * Get the code
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Get the folder identifier
     *
     * @return string|null
     */
    public function getFolder(): ?string
    {
        return $this->folder;
    }

    /**
     * Get the host identifier
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Get the language ID
     *
     * @return int
     */
    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    /**
     * Get the URL
     *
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the hash
     *
     * @return int
     */
    public function getHash(): int
    {
        return $this->hash;
    }

    /**
     * Get the target
     *
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * Get the order
     *
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Get the raw children data
     *
     * @return array|null
     */
    public function getRawChildren(): ?array
    {
        return $this->children;
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
            'children' => $this->childrenItems
        ];
    }
} 