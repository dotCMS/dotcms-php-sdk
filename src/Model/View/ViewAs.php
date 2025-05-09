<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\View;

use Dotcms\PhpSdk\Model\Core\Language;
use Dotcms\PhpSdk\Model\ViewAs\Visitor;

class ViewAs
{
    /**
     * @param Visitor $visitor Visitor context information
     * @param Language $language Language details
     * @param string $mode The view mode (LIVE, PREVIEW, EDIT_MODE)
     * @param string $variantId The variant ID
     */
    public function __construct(
        public readonly Visitor $visitor,
        public readonly Language $language,
        public readonly string $mode,
        public readonly string $variantId = '',
    ) {
    }
}
