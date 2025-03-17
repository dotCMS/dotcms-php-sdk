<?php

namespace App\Helpers;

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
        if (empty($containers) || empty($container)) {
            return null;
        }

        $identifier = $container['identifier'] ?? null;
        
        if (!$identifier || !isset($containers[$identifier])) {
            return null;
        }

        return $containers[$identifier];
    }

    /**
     * Generate HTML attributes from an associative array
     * 
     * @param array $attributes
     * @return string
     */
    public function htmlAttr($attributes)
    {
        if (empty($attributes)) {
            return '';
        }

        $html = '';
        
        foreach ($attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= ' ' . $key;
                }
            } else {
                $html .= ' ' . $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }
        
        return $html;
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

        // Default rendering with title
        $title = $content['title'] ?? $content['name'] ?? 'No Title';
        return '<div class="content-wrapper"><h3>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h3></div>';
    }
} 