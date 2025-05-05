<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Page;

use Dotcms\PhpSdk\Model\Layout\Layout;

class Template
{
    /**
     * @param string $identifier The template identifier
     * @param string $title The template title
     * @param bool $drawed Whether the template is drawn in the layout designer
     * @param string $inode The template inode
     * @param string $friendlyName The template friendly name
     * @param bool|string $header Whether the template has a header
     * @param bool|string $footer Whether the template has a footer
     * @param bool $working Whether the template is in working state
     * @param bool $live Whether the template is live
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $title,
        public readonly bool $drawed,
        public readonly string $inode = '',
        public readonly string $friendlyName = '',
        public readonly bool|string $header = true,
        public readonly bool|string $footer = true,
        public readonly bool $working = false,
        public readonly bool $live = false,
    ) {
    }
}
