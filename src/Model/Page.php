<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation\Ignore;

class Page implements \ArrayAccess, \JsonSerializable
{
    /**
     * @var array<string, mixed> Additional properties not explicitly defined
     * @Ignore()
     */
    private array $additionalProperties = [];

    /**
     * @param string $identifier The page identifier
     * @param string $inode The page inode
     * @param string $title The page title
     * @param string $contentType The content type
     * @param string $pageUrl The page URL
     * @param bool $live Whether the page is live
     * @param bool $working Whether the page is in working state
     * @param string $hostName The hostname
     * @param string $host The host identifier
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $inode,
        public readonly string $title,
        public readonly string $contentType,
        public readonly string $pageUrl,
        public readonly bool $live,
        public readonly bool $working,
        public readonly string $hostName,
        public readonly string $host,
        array $additionalProperties = [],
    ) {
        $this->additionalProperties = $additionalProperties;
    }

    /**
     * Get a property value
     *
     * @param string $name Property name
     * @return mixed Property value or null if not found
     */
    protected function get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return $this->additionalProperties[$name] ?? null;
    }

    /**
     * Check if a property exists
     *
     * @param string $name Property name
     * @return bool True if the property exists
     */
    protected function has(string $name): bool
    {
        return property_exists($this, $name) || isset($this->additionalProperties[$name]);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has((string) $offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get((string) $offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @throws \LogicException Page properties are read-only
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException('Page properties are read-only');
    }

    /**
     * @param mixed $offset
     * @return void
     * @throws \LogicException Page properties are read-only
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('Page properties are read-only');
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
                'inode' => $this->inode,
                'title' => $this->title,
                'contentType' => $this->contentType,
                'pageUrl' => $this->pageUrl,
                'live' => $this->live,
                'working' => $this->working,
                'hostName' => $this->hostName,
                'host' => $this->host,
            ],
            $this->additionalProperties
        );
    }
} 