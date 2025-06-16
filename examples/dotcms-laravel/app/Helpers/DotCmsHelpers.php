<?php

namespace App\Helpers;

use Dotcms\PhpSdk\Utils\DotCmsHelper;
use Dotcms\PhpSdk\Model\Content\Contentlet;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;

class DotCmsHelpers
{
    /**
     * Generate HTML attributes from an associative array
     * 
     * @param array $attributes
     * @return string
     */
    public function htmlAttr($attributes)
    {
        return DotCmsHelper::htmlAttributes($attributes);
    }

    /**
     * Generate HTML based on contentlet properties
     * 
     * @param Contentlet $content
     * @return string
     */
    public function generateHtmlBasedOnProperty(Contentlet $content)
    {
        if (empty($content)) {
            return '';
        }

        // Check if we have a template to render
        $contentType = $content->contentType;
        if ($contentType) {
            $viewPath = 'dotcms.content-types.' . strtolower($contentType);
            if (view()->exists($viewPath)) {
                return view($viewPath, ['content' => $content])->render();
            }
        }

        // Fall back to the SDK simple HTML renderer
        return DotCmsHelper::simpleContentHtml($content->jsonSerialize());
    }

    /**
     * Render a complete container with empty state support
     * 
     * @param ContainerRef $containerRef Container reference
     * @param array $contentlets Array of contentlets
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
        // If no custom renderer provided, use the existing Laravel content rendering logic
        if ($contentRenderer === null) {
            $contentRenderer = function(Contentlet $content) {
                $contentType = $content->contentType;
                if ($contentType) {
                    $viewPath = 'dotcms.content-types.' . strtolower($contentType);
                    if (view()->exists($viewPath)) {
                        return view($viewPath, ['content' => $content])->render();
                    }
                }
                // Fall back to the SDK simple HTML renderer
                return DotCmsHelper::simpleContentHtml($content->jsonSerialize());
            };
        }
        
        return DotCmsHelper::renderContainer($containerRef, $contentlets, $mode, $contentRenderer);
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
        if (isset($containerRef->identifier)) {
            $containerRef->identifier = str_replace('//demo.dotcms.com/', "//$newHost/", $containerRef->identifier);
        }
        return $containerRef;
    }
} 