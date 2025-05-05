<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Page;

use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Content\Contentlet;
use Dotcms\PhpSdk\Model\Layout\Layout;
use Dotcms\PhpSdk\Model\Site\Site;
use Dotcms\PhpSdk\Model\Site\VanityUrl;
use Dotcms\PhpSdk\Model\View\ViewAs;

class PageAsset
{
    /**
     * @param Layout $layout Page layout structure
     * @param Template $template Template details
     * @param Page $page Page metadata
     * @param array<string, ContainerPage> $containers Associative array of containers keyed by identifier
     * @param Site $site Site information
     * @param Contentlet|null $urlContentMap Content map for generated pages
     * @param ViewAs $viewAs Visitor context
     * @param VanityUrl|null $vanityUrl Optional vanity URL configuration
     */
    public function __construct(
        public readonly Layout $layout,
        public readonly Template $template,
        public readonly Page $page,
        public readonly array $containers,
        public readonly Site $site,
        public readonly ?Contentlet $urlContentMap,
        public readonly ViewAs $viewAs,
        public readonly ?VanityUrl $vanityUrl = null,
    ) {
    }
}
