<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Visitor;

class UserAgent implements \JsonSerializable
{
    /**
     * @param string $browser Browser name
     * @param string $version Browser version
     * @param string $os Operating system
     * @param bool $mobile Whether the device is mobile
     */
    public function __construct(
        public readonly string $browser,
        public readonly string $version,
        public readonly string $os,
        public readonly bool $mobile,
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
            'browser' => $this->browser,
            'version' => $this->version,
            'os' => $this->os,
            'mobile' => $this->mobile,
        ];
    }
} 