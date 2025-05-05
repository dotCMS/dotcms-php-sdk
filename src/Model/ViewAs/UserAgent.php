<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\ViewAs;

class UserAgent
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
}
