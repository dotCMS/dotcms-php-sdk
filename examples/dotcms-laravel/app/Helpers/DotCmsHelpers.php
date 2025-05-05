<?php

namespace App\Helpers;

use Dotcms\PhpSdk\Utils\DotCmsHelper;
use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;

class DotCmsHelpers
{
    /**
     * Get container data from the containers array
     * 
     * @param array<string, ContainerPage> $containers Array of containers indexed by identifier
     * @param ContainerRef $containerRef Container reference with identifier
     * @return array|null
     */
    public function getContainerData(array $containers, ContainerRef $containerRef)
    {
        $containerData = DotCmsHelper::getContainerData($containers, $containerRef);

        if (!$containerData) {
            return [
                'contentlets' => [],
                'acceptTypes' => '',
                'maxContentlets' => 0,
                'variantId' => null
            ];
        }
        
        return $containerData;
    }

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
     * @param array $content
     * @return string
     */
    public function generateHtmlBasedOnProperty($content)
    {
        if (empty($content)) {
            return '';
        }

        // Check if we have a template to render
        $contentType = $content['contentType'] ?? '';
        if ($contentType) {
            $viewPath = 'dotcms.content-types.' . strtolower($contentType);
            if (view()->exists($viewPath)) {
                return view($viewPath, ['content' => $content])->render();
            }
        }

        // Fall back to the SDK simple HTML renderer
        return DotCmsHelper::simpleContentHtml($content);
    }
} 