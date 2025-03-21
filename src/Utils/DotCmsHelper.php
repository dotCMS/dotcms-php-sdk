<?php

namespace Dotcms\PhpSdk\Utils;

/**
 * Helper class for dotCMS content operations
 */
class DotCmsHelper
{
    /**
     * Get container data from the containers array
     *
     * @param array<string, mixed> $containers Array of containers indexed by identifier
     * @param array<string, mixed> $container Container reference with identifier
     * @return array<string, mixed>|null The container data or null if not found
     */
    public static function getContainerData(array $containers, array $container): ?array
    {
        if (empty($containers) || empty($container)) {
            return null;
        }

        $identifier = $container['identifier'] ?? null;

        if (! $identifier || ! isset($containers[$identifier])) {
            return null;
        }

        return $containers[$identifier];
    }

    /**
     * Generate HTML attributes from an associative array
     *
     * @param array<string, mixed> $attributes Array of attribute names and values
     * @return string HTML attributes string
     */
    public static function htmlAttributes(array $attributes): string
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
                $html .= ' ' . $key . '="' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }

        return $html;
    }

    /**
     * Default rendering for content when a framework-specific implementation is not available
     *
     * @param array<string, mixed> $content Content data
     * @return string Simple HTML representation of the content
     */
    public static function simpleContentHtml(array $content): string
    {
        if (empty($content)) {
            return '';
        }

        $title = $content['title'] ?? $content['name'] ?? 'No Title';

        return '<div class="dotcms-content" data-content-type="' .
            htmlspecialchars($content['contentType'] ?? 'unknown', ENT_QUOTES, 'UTF-8') .
            '"><h3>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h3></div>';
    }
}
