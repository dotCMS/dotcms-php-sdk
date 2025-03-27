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
        $uuid = $container['uuid'] ?? null;

        if (! $identifier || ! isset($containers[$identifier])) {
            return null;
        }

        if (! is_array($containers[$identifier])) {
            return null;
        }

        $containerData = $containers[$identifier];
        $structures = $containerData['containerStructures'] ?? [];
        $container = $containerData['container'] ?? [];

        $contentlets = $containerData['contentlets']["uuid-$uuid"]
            ?? $containerData['contentlets']["uuid-dotParser_$uuid"]
            ?? [];

        return [
            ...$container,
            'acceptTypes' => implode(',', array_column($structures, 'contentTypeVar')),
            'contentlets' => $contentlets,
            'maxContentlets' => $container['maxContentlets'] ?? 0,
            'variantId' => $container['parentPermissionable']['variantId'] ?? null,
        ];
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
                // Convert value to string safely
                if (is_scalar($value)) {
                    $stringValue = (string)$value;
                } elseif (is_null($value)) {
                    $stringValue = '';
                } else {
                    $encoded = json_encode($value);
                    $stringValue = $encoded !== false ? $encoded : '[complex value]';
                }

                $html .= ' ' . $key . '="' . htmlspecialchars($stringValue, ENT_QUOTES, 'UTF-8') . '"';
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

        $title = '';
        if (isset($content['title']) && is_string($content['title'])) {
            $title = $content['title'];
        } elseif (isset($content['name']) && is_string($content['name'])) {
            $title = $content['name'];
        } else {
            $title = 'No Title';
        }

        $contentType = 'unknown';
        if (isset($content['contentType']) && is_string($content['contentType'])) {
            $contentType = $content['contentType'];
        }

        return '<div class="dotcms-content" data-content-type="' .
            htmlspecialchars($contentType, ENT_QUOTES, 'UTF-8') .
            '"><h3>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h3></div>';
    }
}
