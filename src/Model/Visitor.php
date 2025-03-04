<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

class Visitor implements \JsonSerializable
{
    /**
     * @param VisitorTag[] $tags Array of visitor tags
     * @param string $device Device type
     * @param bool $isNew Whether the visitor is new
     * @param UserAgent $userAgent User agent information
     * @param string $referer Referer URL
     * @param string $dmid Device ID
     * @param GeoLocation $geo Geographical location
     * @param array<string, mixed> $personas Visitor personas
     */
    public function __construct(
        public readonly array $tags,
        public readonly string $device,
        public readonly bool $isNew,
        public readonly UserAgent $userAgent,
        public readonly string $referer,
        public readonly string $dmid,
        public readonly GeoLocation $geo,
        public readonly array $personas, // TODO: Add type to personas
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
            'tags' => $this->tags,
            'device' => $this->device,
            'isNew' => $this->isNew,
            'userAgent' => $this->userAgent,
            'referer' => $this->referer,
            'dmid' => $this->dmid,
            'geo' => $this->geo,
            'personas' => $this->personas,
        ];
    }
} 