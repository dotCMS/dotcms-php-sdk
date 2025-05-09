<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\ViewAs;

class Visitor
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
        public readonly array $personas,
    ) {
    }
}
