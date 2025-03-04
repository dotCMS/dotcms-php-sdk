<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Container;

use Dotcms\PhpSdk\Model\AbstractModel;
use Symfony\Component\Serializer\Annotation as Serializer;

class Container extends AbstractModel
{
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
                'path' => $this->path,
                'live' => $this->live,
                'working' => $this->working,
                'locked' => $this->locked,
                'hostId' => $this->hostId,
                'hostName' => $this->hostName,
                'maxContentlets' => $this->maxContentlets,
                'notes' => $this->notes,
            ],
            $this->getAdditionalProperties()
        );
    }
} 