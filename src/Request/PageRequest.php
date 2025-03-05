<?php

namespace DotCMS\SDK\Request;

use InvalidArgumentException;

/**
 * Class PageRequest
 * 
 * Represents a request to the dotCMS Page API.
 * 
 * @package DotCMS\SDK\Request
 */
class PageRequest
{
    /**
     * Valid format values
     */
    private const FORMAT_JSON = 'json';
    private const FORMAT_RENDER = 'render';
    private const VALID_FORMATS = [self::FORMAT_JSON, self::FORMAT_RENDER];

    /**
     * Valid mode values
     */
    private const MODE_LIVE = 'LIVE';
    private const MODE_WORKING = 'WORKING';
    private const MODE_EDIT = 'EDIT_MODE';
    private const VALID_MODES = [self::MODE_LIVE, self::MODE_WORKING, self::MODE_EDIT];

    /**
     * Valid depth values
     */
    private const MIN_DEPTH = 0;
    private const MAX_DEPTH = 3;

    /**
     * @var string The format of the response (json or render)
     */
    private string $format;

    /**
     * @var string The path to the page
     */
    private string $pagePath;

    /**
     * @var string|null The mode (LIVE, WORKING, EDIT_MODE)
     */
    private ?string $mode = null;

    /**
     * @var string|null The host ID (Site ID)
     */
    private ?string $hostId = null;

    /**
     * @var int|null The language ID
     */
    private ?int $languageId = null;

    /**
     * @var string|null The persona ID
     */
    private ?string $personaId = null;

    /**
     * @var bool|null Whether to fire rules
     */
    private ?bool $fireRules = null;

    /**
     * @var int|null The depth of related content to retrieve (0-3)
     */
    private ?int $depth = null;

    /**
     * PageRequest constructor.
     *
     * @param string $format The format of the response (json or render)
     * @param string $pagePath The path to the page
     * 
     * @throws InvalidArgumentException If the format is invalid
     */
    public function __construct(string $format, string $pagePath)
    {
        $this->setFormat($format);
        $this->setPagePath($pagePath);
    }

    /**
     * Set the format of the response.
     *
     * @param string $format The format (json or render)
     * 
     * @return self
     * @throws InvalidArgumentException If the format is invalid
     */
    private function setFormat(string $format): self
    {
        if (!in_array($format, self::VALID_FORMATS)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid format "%s". Valid formats are: %s',
                    $format,
                    implode(', ', self::VALID_FORMATS)
                )
            );
        }

        $this->format = $format;
        return $this;
    }

    /**
     * Set the page path.
     *
     * @param string $pagePath The path to the page
     * 
     * @return self
     * @throws InvalidArgumentException If the page path is empty
     */
    private function setPagePath(string $pagePath): self
    {
        if (empty($pagePath)) {
            throw new InvalidArgumentException('Page path cannot be empty');
        }

        // Ensure the path starts with a slash
        if (!str_starts_with($pagePath, '/')) {
            $pagePath = '/' . $pagePath;
        }

        // If the path ends with a slash, append 'index'
        if (str_ends_with($pagePath, '/')) {
            $pagePath .= 'index';
        }

        $this->pagePath = $pagePath;
        return $this;
    }

    /**
     * Get the format.
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Get the page path.
     *
     * @return string
     */
    public function getPagePath(): string
    {
        return $this->pagePath;
    }

    /**
     * Get the mode.
     *
     * @return string|null
     */
    public function getMode(): ?string
    {
        return $this->mode;
    }

    /**
     * Get the host ID.
     *
     * @return string|null
     */
    public function getHostId(): ?string
    {
        return $this->hostId;
    }

    /**
     * Get the language ID.
     *
     * @return int|null
     */
    public function getLanguageId(): ?int
    {
        return $this->languageId;
    }

    /**
     * Get the persona ID.
     *
     * @return string|null
     */
    public function getPersonaId(): ?string
    {
        return $this->personaId;
    }

    /**
     * Get whether to fire rules.
     *
     * @return bool|null
     */
    public function getFireRules(): ?bool
    {
        return $this->fireRules;
    }

    /**
     * Get the depth.
     *
     * @return int|null
     */
    public function getDepth(): ?int
    {
        return $this->depth;
    }

    /**
     * Set the mode.
     *
     * @param string $mode The mode (LIVE, WORKING, EDIT_MODE)
     * 
     * @return self A new instance with the updated mode
     * @throws InvalidArgumentException If the mode is invalid
     */
    public function withMode(string $mode): self
    {
        if (!in_array($mode, self::VALID_MODES)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid mode "%s". Valid modes are: %s',
                    $mode,
                    implode(', ', self::VALID_MODES)
                )
            );
        }

        $clone = clone $this;
        $clone->mode = $mode;
        return $clone;
    }

    /**
     * Set the host ID.
     *
     * @param string $hostId The host ID (Site ID)
     * 
     * @return self A new instance with the updated host ID
     */
    public function withHostId(string $hostId): self
    {
        $clone = clone $this;
        $clone->hostId = $hostId;
        return $clone;
    }

    /**
     * Set the language ID.
     *
     * @param int $languageId The language ID
     * 
     * @return self A new instance with the updated language ID
     */
    public function withLanguageId(int $languageId): self
    {
        $clone = clone $this;
        $clone->languageId = $languageId;
        return $clone;
    }

    /**
     * Set the persona ID.
     *
     * @param string $personaId The persona ID
     * 
     * @return self A new instance with the updated persona ID
     */
    public function withPersonaId(string $personaId): self
    {
        $clone = clone $this;
        $clone->personaId = $personaId;
        return $clone;
    }

    /**
     * Set whether to fire rules.
     *
     * @param bool $fireRules Whether to fire rules
     * 
     * @return self A new instance with the updated fire rules setting
     */
    public function withFireRules(bool $fireRules): self
    {
        $clone = clone $this;
        $clone->fireRules = $fireRules;
        return $clone;
    }

    /**
     * Set the depth.
     *
     * @param int $depth The depth of related content to retrieve (0-3)
     * 
     * @return self A new instance with the updated depth
     * @throws InvalidArgumentException If the depth is invalid
     */
    public function withDepth(int $depth): self
    {
        if ($depth < self::MIN_DEPTH || $depth > self::MAX_DEPTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid depth "%d". Depth must be between %d and %d',
                    $depth,
                    self::MIN_DEPTH,
                    self::MAX_DEPTH
                )
            );
        }

        $clone = clone $this;
        $clone->depth = $depth;
        return $clone;
    }

    /**
     * Build the request URL path.
     *
     * @return string The request URL path
     */
    public function buildPath(): string
    {
        return sprintf('/api/v1/page/%s%s', $this->format, $this->pagePath);
    }

    /**
     * Build the query parameters.
     *
     * @return array The query parameters
     */
    public function buildQueryParams(): array
    {
        $params = [];

        if ($this->mode !== null) {
            $params['mode'] = $this->mode;
        }

        if ($this->hostId !== null) {
            $params['host_id'] = $this->hostId;
        }

        if ($this->languageId !== null) {
            $params['language_id'] = $this->languageId;
        }

        if ($this->personaId !== null) {
            $params['com.dotmarketing.persona.id'] = $this->personaId;
        }

        if ($this->fireRules !== null) {
            $params['fireRules'] = $this->fireRules ? 'true' : 'false';
        }

        if ($this->depth !== null) {
            $params['depth'] = $this->depth;
        }

        return $params;
    }

    /**
     * Validate the request.
     *
     * @return bool True if the request is valid
     * @throws InvalidArgumentException If the request is invalid
     */
    public function validate(): bool
    {
        // Format and page path are validated in the constructor
        
        // Validate mode if set
        if ($this->mode !== null && !in_array($this->mode, self::VALID_MODES)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid mode "%s". Valid modes are: %s',
                    $this->mode,
                    implode(', ', self::VALID_MODES)
                )
            );
        }

        // Validate depth if set
        if ($this->depth !== null && ($this->depth < self::MIN_DEPTH || $this->depth > self::MAX_DEPTH)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid depth "%d". Depth must be between %d and %d',
                    $this->depth,
                    self::MIN_DEPTH,
                    self::MAX_DEPTH
                )
            );
        }

        return true;
    }
} 