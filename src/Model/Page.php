<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

class Page extends AbstractModel
{
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
                'pageUrl' => $this->pageUrl,
                'live' => $this->live,
                'working' => $this->working,
                'hostName' => $this->hostName,
                'host' => $this->host,
            ],
            $this->getAdditionalProperties()
        );
    }
} 