<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation\Ignore;

class Contentlet implements \ArrayAccess, \JsonSerializable
{
    /**
     * @var array<string, mixed> Additional properties not explicitly defined
     * @Ignore()
     */
    private array $additionalProperties = [];

    /**
     * @param string $identifier The contentlet identifier
     * @param string $inode The contentlet inode
     * @param string $title The contentlet title
     * @param string $contentType The content type
     * @param bool $working Whether the contentlet is in working state
     * @param bool $locked Whether the contentlet is locked
     * @param bool $live Whether the contentlet is live
     * @param string $ownerName The name of the owner
     * @param string $publishUserName The name of the publish user
     * @param string $publishUser The ID of the publish user
     * @param int $languageId The language ID
     * @param int $creationDate The creation date timestamp
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $inode,
        public readonly string $title,
        public readonly string $contentType,
        public readonly bool $working = false,
        public readonly bool $locked = false,
        public readonly bool $live = false,
        public readonly string $ownerName = '',
        public readonly string $publishUserName = '',
        public readonly string $publishUser = '',
        public readonly int $languageId = 0,
        public readonly int $creationDate = 0,
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
     * @throws \LogicException Contentlet properties are read-only
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException('Contentlet properties are read-only');
    }

    /**
     * @param mixed $offset
     * @return void
     * @throws \LogicException Contentlet properties are read-only
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('Contentlet properties are read-only');
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
                'working' => $this->working,
                'locked' => $this->locked,
                'live' => $this->live,
                'ownerName' => $this->ownerName,
                'publishUserName' => $this->publishUserName,
                'publishUser' => $this->publishUser,
                'languageId' => $this->languageId,
                'creationDate' => $this->creationDate,
            ],
            $this->additionalProperties
        );
    }
} 