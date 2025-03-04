<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class Container implements \ArrayAccess, \JsonSerializable
{
    /**
     * Additional properties not explicitly defined in the class
     * 
     * @var array<string, mixed>
     */
    protected array $additionalProperties;

    /**
     * @param string $identifier Container identifier
     * @param string $inode Container inode
     * @param string $title Container title
     * @param string $path Container path
     * @param bool $live Whether the container is live
     * @param bool $working Whether the container is working
     * @param bool $locked Whether the container is locked
     * @param string $hostId Host ID
     * @param string $hostName Host name
     * @param int $maxContentlets Maximum number of contentlets
     * @param string $notes Container notes
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $inode,
        public readonly string $title,
        public readonly string $path,
        public readonly bool $live = false,
        public readonly bool $working = false,
        public readonly bool $locked = false,
        public readonly string $hostId = '',
        public readonly string $hostName = '',
        public readonly int $maxContentlets = 0,
        public readonly string $notes = '',
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
     * Check if an offset exists
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has((string) $offset);
    }

    /**
     * Get an offset
     * 
     * @return mixed The value at the specified offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get((string) $offset);
    }

    /**
     * Set a value at the specified offset
     * 
     * @throws \RuntimeException Always throws an exception as this object is immutable
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \RuntimeException('Cannot modify immutable object');
    }

    /**
     * Unset an offset
     * 
     * @throws \RuntimeException Always throws an exception as this object is immutable
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \RuntimeException('Cannot modify immutable object');
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
                'path' => $this->path,
                'live' => $this->live,
                'working' => $this->working,
                'locked' => $this->locked,
                'hostId' => $this->hostId,
                'hostName' => $this->hostName,
                'maxContentlets' => $this->maxContentlets,
                'notes' => $this->notes,
            ],
            $this->additionalProperties
        );
    }
} 