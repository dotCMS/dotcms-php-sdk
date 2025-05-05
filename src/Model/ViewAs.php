<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Dotcms\PhpSdk\Model\ViewAs\Visitor;

class ViewAs
{
    /**
     * @param Visitor $visitor Visitor context information
     * @param array<string, mixed> $language Language details
     * @param string $mode The view mode (LIVE, PREVIEW, EDIT_MODE)
     */
    public function __construct(
        public readonly Visitor $visitor,
        public readonly array $language,
        public readonly string $mode,
    ) {
    }
}
