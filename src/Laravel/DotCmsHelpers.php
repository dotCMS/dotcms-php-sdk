<?php

namespace Dotcms\PhpSdk\Laravel;

use Dotcms\PhpSdk\Model\Content\Contentlet;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Dotcms\PhpSdk\Utils\DotCmsHelper;

/**
 * DotCMS Laravel Helper for empty container support
 */
class DotCmsHelpers
{
    /**
     * Render a complete container with empty state support
     *
     * @param ContainerRef $containerRef Container reference
     * @param array<Contentlet> $contentlets Array of contentlets
     * @param string|null $mode Current mode (EDIT_MODE for UVE)
     * @param callable|null $contentRenderer Custom content renderer
     * @return string Rendered container HTML
     */
    public function renderContainer(
        ContainerRef $containerRef,
        array $contentlets,
        ?string $mode = null,
        ?callable $contentRenderer = null
    ): string {
        return DotCmsHelper::renderContainer($containerRef, $contentlets, $mode, $contentRenderer);
    }

    /**
     * Get CSS styles for empty containers
     *
     * @return string CSS for empty container styling
     */
    public function getEmptyContainerCSS(): string
    {
        return DotCmsHelper::getEmptyContainerCSS();
    }

    /**
     * Generate HTML attributes from array
     *
     * @param array<string, mixed> $attrs Attributes array
     * @return string HTML attributes string
     */
    public function htmlAttr(array $attrs): string
    {
        return DotCmsHelper::htmlAttributes($attrs);
    }

    /**
     * Rewrite container identifier for dynamic host support
     *
     * @param ContainerRef $containerRef Container reference to modify
     * @param string $newHost New host to use
     * @return ContainerRef Modified container reference
     */
    public function rewriteContainerIdentifier(ContainerRef $containerRef, string $newHost): ContainerRef
    {
        // Since ContainerRef properties are readonly, we need to create a new instance
        $newIdentifier = $containerRef->identifier;
        if ($newIdentifier !== '') {
            $newIdentifier = str_replace('//demo.dotcms.com/', "//$newHost/", $newIdentifier);
        }

        return new ContainerRef(
            identifier: $newIdentifier,
            uuid: $containerRef->uuid,
            historyUUIDs: $containerRef->historyUUIDs,
            contentlets: $containerRef->contentlets,
            acceptTypes: $containerRef->acceptTypes,
            maxContentlets: $containerRef->maxContentlets,
            variantId: $containerRef->variantId,
        );
    }
}
