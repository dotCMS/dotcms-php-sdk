<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Container;

class ContainerStructure implements \JsonSerializable
{
    /**
     * @param string $id Container structure ID
     * @param string $structureId Structure ID
     * @param string $containerInode Container inode
     * @param string $containerId Container ID
     * @param string $code Container code
     * @param string $contentTypeVar Content type variable
     */
    public function __construct(
        public readonly string $id,
        public readonly string $structureId,
        public readonly string $containerInode,
        public readonly string $containerId,
        public readonly string $code,
        public readonly string $contentTypeVar,
    ) {
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'structureId' => $this->structureId,
            'containerInode' => $this->containerInode,
            'containerId' => $this->containerId,
            'code' => $this->code,
            'contentTypeVar' => $this->contentTypeVar,
        ];
    }
}
