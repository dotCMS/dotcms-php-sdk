<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

class Site extends AbstractModel
{
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
        $this->setAdditionalProperties($additionalProperties);
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
            $this->getAdditionalProperties()
        );
    }
} 