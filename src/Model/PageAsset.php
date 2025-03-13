<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Layout\Layout;

class PageAsset extends AbstractModel
{
    /**
     * @param Layout $layout Page layout structure
     * @param Template $template Template details
     * @param Page $page Page metadata
     * @param array<string, ContainerPage> $containers Associative array of containers keyed by identifier
     * @param Site $site Site information
     * @param Contentlet|null $urlContentMap Content map for generated pages
     * @param ViewAs $viewAs Visitor context
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly Layout $layout,
        public readonly Template $template,
        public readonly Page $page,
        public readonly array $containers,
        public readonly Site $site,
        public readonly ?Contentlet $urlContentMap,
        public readonly ViewAs $viewAs,
        array $additionalProperties = [],
    ) {
        $this->setAdditionalProperties($additionalProperties);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'layout' => $this->layout,
                'template' => $this->template,
                'page' => $this->page,
                'containers' => $this->containers,
                'site' => $this->site,
                'urlContentMap' => $this->urlContentMap,
                'viewAs' => $this->viewAs,
            ],
            $this->getAdditionalProperties()
        );
    }
}
