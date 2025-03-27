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
        // First try to get the container data using the SDK helper
        $containerData = DotCmsHelper::getContainerData($containers, $container);
        
        // Debug the input data
        Log::debug('Container Input', [
            'container' => $container,
            'containers' => $containers
        ]);
        
        if (!$containerData) {
            Log::warning('No container data found', [
                'identifier' => $container['identifier'] ?? 'unknown',
                'uuid' => $container['uuid'] ?? 'unknown'
            ]);
            return [
                'contentlets' => [],
                'acceptTypes' => '',
                'maxContentlets' => 0,
                'variantId' => null
            ];
        }
        
        $identifier = $container['identifier'] ?? '';
        $uuid = $container['uuid'] ?? '';
        
        // Debug the container data
        Log::debug('Container Data', [
            'containerData' => $containerData,
            'identifier' => $identifier,
            'uuid' => $uuid
        ]);
        
        $structures = $containerData['containerStructures'] ?? [];
        $container = $containerData['container'] ?? [];
        
        // Debug the contentlets structure
        Log::debug('Contentlets Structure', [
            'contentlets' => $containerData['contentlets'] ?? [],
            'uuid-key' => "uuid-$uuid",
            'dotParser-key' => "uuid-dotParser_$uuid"
        ]);
        
        $contentlets = $containerData['contentlets']["uuid-$uuid"] 
            ?? $containerData['contentlets']["uuid-dotParser_$uuid"] 
            ?? [];
        
        if (empty($contentlets)) {
            Log::warning("No contentlets found for container: $identifier, uuid: $uuid");
        }
        
        $result = [
            ...$container,
            'acceptTypes' => implode(',', array_column($structures, 'contentTypeVar')),
            'contentlets' => $contentlets,
            'variantId' => $container['parentPermissionable']['variantId'] ?? null
        ];
        
        // Debug the final result
        Log::debug('Final Result', $result);
        
        return $result;
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