<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Request;

use InvalidArgumentException;
use Respect\Validation\Validator as v;

/**
 * Class NavigationRequest
 *
 * Represents a request to the dotCMS Navigation API.
 *
 * @package Dotcms\PhpSdk\Request
 */
class NavigationRequest
{
    /**
     * Default values
     */
    private const DEFAULT_DEPTH = 1;
    private const DEFAULT_LANGUAGE_ID = 1;

    /**
     * @param string $path The root path to begin traversing the folder tree
     * @param int $depth The depth of the folder tree to return (default: 1)
     * @param int $languageId The language ID of content to return (default: 1)
     * @throws InvalidArgumentException If validation fails
     */
    public function __construct(
        private readonly string $path = '/',
        private readonly int $depth = self::DEFAULT_DEPTH,
        private readonly int $languageId = self::DEFAULT_LANGUAGE_ID
    ) {
        $this->validate();
    }

    /**
     * Validate the request parameters
     *
     * @throws InvalidArgumentException If validation fails
     */
    private function validate(): void
    {
        if (!v::stringType()->notEmpty()->validate($this->path)) {
            throw new InvalidArgumentException("Path must be a non-empty string");
        }
        
        if (!v::intVal()->min(1)->validate($this->depth)) {
            throw new InvalidArgumentException("Depth must be a positive integer");
        }
        
        if (!v::intVal()->min(1)->validate($this->languageId)) {
            throw new InvalidArgumentException("Language ID must be a positive integer");
        }
    }

    /**
     * Get the path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get the depth
     *
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
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
     * Build the API path
     *
     * @return string
     */
    public function buildPath(): string
    {
        return sprintf('/api/v1/nav%s', 
            $this->path === '/' ? '/' : '/' . ltrim($this->path, '/')
        );
    }

    /**
     * Build the query parameters
     *
     * @return array
     */
    public function buildQueryParams(): array
    {
        return [
            'depth' => $this->depth,
            'languageId' => $this->languageId
        ];
    }
} 