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
     * Render a complete container using SDK functionality with empty state support
     *
     * @param ContainerRef $containerRef Container reference
     * @param array<Contentlet> $contentlets Array of contentlets
     * @param string|null $mode Current mode for UVE detection
     * @return string Container HTML
     */
    public function renderContainer(ContainerRef $containerRef, array $contentlets, ?string $mode = null)
    {
        return DotCmsHelper::renderContainer(
            $containerRef,
            $contentlets,
            $mode,
            function(Contentlet $content) {
                return $this->generateHtmlBasedOnProperty($content);
            }
        );
    }

    /**
     * Get CSS for empty container styling
     *
     * @return string CSS styles
     */
    public function getEmptyContainerCSS()
    {
        return DotCmsHelper::getEmptyContainerCSS();
    }
} 