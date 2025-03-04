<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class PageAsset implements \JsonSerializable
{
    /**
     * @param Layout $layout Page layout structure
     * @param Template $template Template details
     * @param Page $page Page metadata
     * @param array $containers Associative array of containers
     * @param array $contentlets Associative array of contentlets
     * @param Site $site Site information
     * @param array|null $urlContentMap Content map for generated pages
     * @param ViewAs $viewAs Visitor context
     */
    public function __construct(
        public readonly Layout $layout,
        public readonly Template $template,
        public readonly Page $page,
        public readonly array $containers,
        public readonly array $contentlets,
        public readonly Site $site,
        public readonly ?array $urlContentMap,
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
            'contentlets' => $this->contentlets,
            'site' => $this->site,
            'urlContentMap' => $this->urlContentMap,
            'viewAs' => $this->viewAs,
        ];
    }
} 