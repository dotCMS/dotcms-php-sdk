<?php

namespace Dotcms\PhpSdk\Utils;

use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Container\ContainerStructure;
use Dotcms\PhpSdk\Model\Content\Contentlet;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;

/**
 * Helper class for dotCMS content operations
 */
class DotCmsHelper
{
    /**
     * Extract accept types from container structures
     *
     * @param array<ContainerStructure> $containerStructures Array of container structures
     * @return string Comma-separated list of content type variables
     */
    public static function extractAcceptTypes(array $containerStructures): string
    {
        return implode(',', array_map(fn (ContainerStructure $structure) => $structure->contentTypeVar, $containerStructures));
    }

    /**
     * Extract contentlets from container page based on UUID
     *
     * @param ContainerPage|null $containerPage Container page data
     * @param string|null $uuid UUID to look up contentlets for
     * @return array<Contentlet> Array of contentlets
     */
    public static function extractContentlets(?ContainerPage $containerPage, ?string $uuid): array
    {
        if ($containerPage === null || $uuid === null || ! (is_string($uuid) || is_numeric($uuid))) {
            return [];
        }

        $uuidStr = (string) $uuid;
        $contentlets = $containerPage->contentlets["uuid-$uuidStr"]
            ?? $containerPage->contentlets["uuid-dotParser_$uuidStr"]
            ?? [];

        return is_array($contentlets) ? $contentlets : [];
    }

    /**
     * Get container data from the containers array
     *
     * @param array<string, ContainerPage> $containers Array of containers indexed by identifier
     * @param ContainerRef $containerRef Container reference with identifier
     * @return array<string, mixed>|null The container data or null if not found
     */
    public static function getContainerData(array $containers, ContainerRef $containerRef): ?array
    {
        if (empty($containers)) {
            return null;
        }

        $identifier = $containerRef->identifier ?? null;
        $uuid = $containerRef->uuid ?? null;

        if (! $identifier || ! isset($containers[$identifier])) {
            return null;
        }

        $containerPage = $containers[$identifier];
        $structures = $containerPage->containerStructures;
        $container = $containerPage->container;

        return [
            ...$container->jsonSerialize(),
            'acceptTypes' => self::extractAcceptTypes($structures),
            'contentlets' => self::extractContentlets($containerPage, $uuid),
            'maxContentlets' => $container->maxContentlets,
            'variantId' => $container->additionalProperties['parentPermissionable']['variantId'] ?? null,
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
