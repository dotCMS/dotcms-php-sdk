<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Core;

use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * Abstract base class for models that need to handle additional properties
 *
 * @implements \ArrayAccess<string, mixed>
 */
abstract class AbstractModel implements \ArrayAccess, \JsonSerializable
{
    /**
     * @var array<string, mixed> Additional properties not explicitly defined
     * @Ignore()
     */
    private array $additionalProperties = [];

    /**
     * Set additional properties
     *
     * @param array<string, mixed> $additionalProperties
     */
    protected function setAdditionalProperties(array $additionalProperties): void
    {
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
     * Magic method to get property values
     * This enables property access syntax (e.g., $obj->propertyName) for additional properties
     *
     * @param string $name Property name
     * @return mixed Property value or null if not found
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    /**
     * Magic method to check if a property exists
     * This enables isset() checks on additional properties
     *
     * @param string $name Property name
     * @return bool True if the property exists
     */
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return is_string($offset) && $this->has($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (! is_string($offset)) {
            return null;
        }

        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \RuntimeException('Properties are read-only');
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \RuntimeException('Properties are read-only');
    }

    /**
     * Get all additional properties
     *
     * @return array<string, mixed>
     */
    protected function getAdditionalProperties(): array
    {
        return $this->additionalProperties;
    }
}
