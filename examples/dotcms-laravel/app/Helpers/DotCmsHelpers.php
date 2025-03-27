<?php

namespace App\Helpers;

use Dotcms\PhpSdk\Utils\DotCmsHelper;
use Illuminate\Support\Facades\Log;

class DotCmsHelpers
{
    /**
     * Get container data from the containers array
     * 
     * @param array $containers
     * @param array $container
     * @return array|null
     */
    public function getContainersData($containers, $container)
    {
        $containerData = DotCmsHelper::getContainerData($containers, $container);
        
        if (!$containerData) {
            return [
                'contentlets' => [],
                'acceptTypes' => '',
                'maxContentlets' => 0,
                'variantId' => null
            ];
        }
        
        if (empty($containerData['contentlets'])) {
            Log::warning("No contentlets found for container: " . ($container['identifier'] ?? 'unknown') . ", uuid: " . ($container['uuid'] ?? 'unknown'));
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