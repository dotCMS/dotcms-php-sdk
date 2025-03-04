<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation\Ignore;

class Site implements \ArrayAccess, \JsonSerializable
{
    /**
     * @var array<string, mixed> Additional properties not explicitly defined
     * @Ignore()
     */
    private array $additionalProperties = [];

    /**
     * @param string $identifier The site identifier
     * @param string $hostname The site hostname
     * @param string $inode The site inode
     * @param bool $working Whether the site is in working state
     * @param string $folder The site folder
     * @param bool $locked Whether the site is locked
     * @param bool $archived Whether the site is archived
     * @param bool $live Whether the site is live
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $hostname,
        public readonly string $inode = '',
        public readonly bool $working = false,
        public readonly string $folder = '',
        public readonly bool $locked = false,
        public readonly bool $archived = false,
        public readonly bool $live = false,
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
     * @throws \LogicException Site properties are read-only
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException('Site properties are read-only');
    }

    /**
     * @param mixed $offset
     * @return void
     * @throws \LogicException Site properties are read-only
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('Site properties are read-only');
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
                'hostname' => $this->hostname,
                'inode' => $this->inode,
                'working' => $this->working,
                'folder' => $this->folder,
                'locked' => $this->locked,
                'archived' => $this->archived,
                'live' => $this->live,
            ],
            $this->additionalProperties
        );
    }
} 