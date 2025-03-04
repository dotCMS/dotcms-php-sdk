<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

class Contentlet extends AbstractModel
{
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
            $this->getAdditionalProperties()
        );
    }
} 