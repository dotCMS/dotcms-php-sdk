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
     * Generate container HTML attributes for UVE compatibility
     *
     * @param ContainerRef $containerRef Container reference
     * @return array<string, mixed> Container attributes
     */
    public static function getContainerAttributes(ContainerRef $containerRef): array
    {
        return [
            'data-dot-object' => 'container',
            'data-dot-identifier' => $containerRef->identifier ?? '',
            'data-dot-accept-types' => $containerRef->acceptTypes ?? '',
            'data-max-contentlets' => $containerRef->maxContentlets ?? '',
            'data-dot-uuid' => $containerRef->uuid ?? '',
        ];
    }

    /**
     * Generate contentlet HTML attributes for UVE compatibility
     *
     * @param Contentlet $content Contentlet object
     * @param ContainerRef $containerRef Container reference for context
     * @return array<string, mixed> Contentlet attributes
     */
    public static function getContentletAttributes(Contentlet $content, ContainerRef $containerRef): array
    {
        return [
            'data-dot-object' => 'contentlet',
            'data-dot-identifier' => $content->identifier ?? '',
            'data-dot-basetype' => $content->baseType ?? '',
            'data-dot-title' => $content->widgetTitle ?? $content->title ?? '',
            'data-dot-inode' => $content->inode ?? '',
            'data-dot-type' => $content->contentType ?? '',
            'data-dot-container' => json_encode([
                'acceptTypes' => $containerRef->acceptTypes ?? '',
                'identifier' => $containerRef->identifier ?? '',
                'maxContentlets' => $containerRef->maxContentlets ?? '',
                'variantId' => $containerRef->variantId ?? '',
                'uuid' => $containerRef->uuid ?? '',
            ]),
        ];
    }

    /**
     * Generate ghost contentlet attributes for empty containers in UVE mode
     *
     * @param ContainerRef $containerRef Container reference
     * @return array<string, mixed> Ghost contentlet attributes
     */
    public static function getGhostContentletAttributes(ContainerRef $containerRef): array
    {
        return [
            'data-dot-object' => 'contentlet',
            'data-dot-identifier' => 'empty-placeholder',
            'data-dot-basetype' => 'CONTENT',
            'data-dot-title' => 'Empty Container',
            'data-dot-inode' => 'empty-placeholder-inode',
            'data-dot-type' => 'placeholder',
            'data-dot-container' => json_encode([
                'acceptTypes' => $containerRef->acceptTypes ?? '',
                'identifier' => $containerRef->identifier ?? '',
                'maxContentlets' => $containerRef->maxContentlets ?? '',
                'variantId' => $containerRef->variantId ?? '',
                'uuid' => $containerRef->uuid ?? '',
            ]),
        ];
    }

    /**
     * Generate empty container placeholder HTML
     *
     * @param string $message Custom message for empty container
     * @return string HTML for empty container placeholder
     */
    public static function generateEmptyContainerPlaceholder(string $message = 'This container is empty.'): string
    {
        return '<div class="empty-container-placeholder">' . 
               htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . 
               '</div>';
    }

    /**
     * Check if we're in edit mode for UVE
     *
     * @param string|null $mode Current mode parameter
     * @return bool True if in edit mode
     */
    public static function isEditMode(?string $mode): bool
    {
        return $mode === 'EDIT_MODE';
    }

    /**
     * Generate complete container HTML with UVE support and empty state handling
     *
     * @param ContainerRef $containerRef Container reference
     * @param array<Contentlet> $contentlets Array of contentlets for this container
     * @param string|null $mode Current mode (for UVE detection)
     * @param callable|null $contentRenderer Function to render individual contentlets
     * @return string Complete container HTML
     */
    public static function renderContainer(
        ContainerRef $containerRef,
        array $contentlets,
        ?string $mode = null,
        ?callable $contentRenderer = null
    ): string {
        $containerAttrs = self::getContainerAttributes($containerRef);
        $containerAttrsHtml = self::htmlAttributes($containerAttrs);
        $hasContentlets = !empty($contentlets);
        $isEditMode = self::isEditMode($mode);

        $html = "<div{$containerAttrsHtml}>";

        if ($hasContentlets) {
            // Render contentlets
            foreach ($contentlets as $content) {
                $contentAttrs = self::getContentletAttributes($content, $containerRef);
                $contentAttrsHtml = self::htmlAttributes($contentAttrs);

                $contentHtml = '';
                if ($contentRenderer && is_callable($contentRenderer)) {
                    $contentHtml = $contentRenderer($content);
                } else {
                    $contentHtml = self::simpleContentHtml($content->jsonSerialize());
                }

                $html .= "<div{$contentAttrsHtml}>{$contentHtml}</div>";
            }
        } elseif ($isEditMode) {
            // Render empty container with ghost contentlet for UVE
            $ghostAttrs = self::getGhostContentletAttributes($containerRef);
            $ghostAttrsHtml = self::htmlAttributes($ghostAttrs);
            $placeholder = self::generateEmptyContainerPlaceholder();

            $html .= "<div{$ghostAttrsHtml} class=\"uve-ghost-contentlet\">{$placeholder}</div>";
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Get CSS for empty container styling
     *
     * @return string CSS styles for empty containers
     */
    public static function getEmptyContainerCSS(): string
    {
        return '
/* DotCMS SDK - Empty Container Placeholder for UVE */
.empty-container-placeholder {
    padding: 2rem;
    border: 2px dashed #d1d5db;
    border-radius: 0.5rem;
    text-align: center;
    color: #6b7280;
    font-style: italic;
    margin: 0;
    background-color: #f9fafb;
    transition: all 0.2s ease;
    min-height: 4rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-container-placeholder:hover {
    border-color: #9ca3af;
    background-color: #f3f4f6;
}

/* UVE Ghost Contentlet - wrapper that UVE can detect but is visually transparent */
.uve-ghost-contentlet {
    display: block;
    position: relative;
    margin: 0;
    padding: 0;
    min-height: 6rem; /* Ensure adequate hover target for UVE */
}
';
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
