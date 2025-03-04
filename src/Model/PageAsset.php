<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Layout\Layout;
use Dotcms\PhpSdk\Model\Visitor\ViewAs;

class PageAsset implements \JsonSerializable
{
    /**
     * @param Layout $layout Page layout structure
     * @param Template $template Template details
     * @param Page $page Page metadata
     * @param array<string, ContainerPage> $containers Associative array of containers keyed by identifier
     * @param Site $site Site information
     * @param Contentlet|null $urlContentMap Content map for generated pages
     * @param ViewAs $viewAs Visitor context
     */
    public function __construct(
        public readonly Layout $layout,
        public readonly Template $template,
        public readonly Page $page,
        public readonly array $containers,
        public readonly Site $site,
        public readonly ?Contentlet $urlContentMap,
        public readonly ViewAs $viewAs,
    ) {
    }

    /**
     * Check if this is a generated page (e.g., blog post, product page).
     */
    public function isGenerated(): bool
    {
        return $this->urlContentMap !== null;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'layout' => $this->layout,
            'template' => $this->template,
            'page' => $this->page,
            'containers' => $this->containers,
            'site' => $this->site,
            'urlContentMap' => $this->urlContentMap,
            'viewAs' => $this->viewAs,
        ];
    }
}
