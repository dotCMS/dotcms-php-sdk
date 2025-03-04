<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation\Ignore;

class Template implements \ArrayAccess, \JsonSerializable
{
    /**
     * @var array<string, mixed> Additional properties not explicitly defined
     * @Ignore()
     */
    private array $additionalProperties = [];

    /**
     * @param string $identifier The template identifier
     * @param string $title The template title
     * @param bool $drawed Whether the template is drawn in the layout designer
     * @param string $inode The template inode
     * @param string $friendlyName The template friendly name
     * @param bool $header Whether the template has a header
     * @param bool $footer Whether the template has a footer
     * @param bool $working Whether the template is in working state
     * @param bool $live Whether the template is live
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $title,
        public readonly bool $drawed,
        public readonly string $inode = '',
        public readonly string $friendlyName = '',
        public readonly bool $header = true,
        public readonly bool $footer = true,
        public readonly bool $working = false,
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
     * @throws \LogicException Template properties are read-only
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException('Template properties are read-only');
    }

    /**
     * @param mixed $offset
     * @return void
     * @throws \LogicException Template properties are read-only
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('Template properties are read-only');
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
                'title' => $this->title,
                'drawed' => $this->drawed,
                'inode' => $this->inode,
                'friendlyName' => $this->friendlyName,
                'header' => $this->header,
                'footer' => $this->footer,
                'working' => $this->working,
                'live' => $this->live,
            ],
            $this->additionalProperties
        );
    }
} 