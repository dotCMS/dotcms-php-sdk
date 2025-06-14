<?php

namespace App\Twig;

use Dotcms\PhpSdk\Utils\DotCmsHelper;
use Dotcms\PhpSdk\Model\Content\Contentlet;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use InvalidArgumentException;
use RuntimeException;

class DotCMSExtension extends AbstractExtension
{
    public function __construct(
        private Environment $twig
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getGridClass', [$this, 'getGridClass']),
            new TwigFunction('generateHtmlBasedOnProperty', [$this, 'generateHtmlBasedOnProperty'], ['is_safe' => ['html']]),
            new TwigFunction('htmlAttr', [$this, 'htmlAttr'], ['is_safe' => ['html']]),
            new TwigFunction('renderContainer', [$this, 'renderContainer'], ['is_safe' => ['html']]),
            new TwigFunction('getEmptyContainerCSS', [$this, 'getEmptyContainerCSS'], ['is_safe' => ['html']]),
            new TwigFunction('getContainerAttributes', [$this, 'getContainerAttributes']),
            new TwigFunction('getContentletAttributes', [$this, 'getContentletAttributes']),
            new TwigFunction('getGhostContentletAttributes', [$this, 'getGhostContentletAttributes']),
            new TwigFunction('isEditMode', [$this, 'isEditMode']),
            new TwigFunction('generateEmptyContainerPlaceholder', [$this, 'generateEmptyContainerPlaceholder'], ['is_safe' => ['html']]),
            new TwigFunction('template_exists', [$this, 'templateExists'])
        ];
    }

    public function htmlAttr(array $attrs): string 
    {
        return DotCmsHelper::htmlAttributes($attrs);
    }

    public function getGridClass(int $position, string $type = 'start'): string 
    {
        return match($type) {
            'start' => "col-start-{$position}",
            'end' => "col-end-{$position}",
            default => throw new InvalidArgumentException('Invalid grid class type')
        };
    }

    public function generateHtmlBasedOnProperty(Contentlet $content, ?string $dynamicHost = null): string 
    {
        if (empty($content)) {
            return '';
        }

        $contentType = $content->contentType;
        if ($contentType) {
            $template = 'dotcms/content-types/' . strtolower($contentType) . '.twig';
            if ($this->twig->getLoader()->exists($template)) {
                return $this->twig->render($template, [
                    'content' => $content,
                    'dotcms_host' => $dynamicHost ?? $_ENV['DOTCMS_HOST'] ?? 'https://demo.dotcms.com'
                ]);
            }
        }

        // Fall back to the SDK simple HTML renderer
        return DotCmsHelper::simpleContentHtml($content->jsonSerialize());
    }

    /**
     * Rewrite container identifier to use dynamic host instead of hardcoded demo.dotcms.com
     *
     * @param string $identifier Original container identifier
     * @param string|null $dynamicHost Dynamic host to use
     * @return string Rewritten identifier
     */
    private function rewriteContainerIdentifier(string $identifier, ?string $dynamicHost): string
    {
        if (!$dynamicHost) {
            return $identifier;
        }
        
        // Parse the dynamic host to get the hostname
        $parsedHost = parse_url($dynamicHost);
        $newHost = $parsedHost['host'] ?? $dynamicHost;
        if (isset($parsedHost['port'])) {
            $newHost .= ':' . $parsedHost['port'];
        }
        
        // Replace demo.dotcms.com with the dynamic host
        return str_replace('//demo.dotcms.com/', '//' . $newHost . '/', $identifier);
    }

    /**
     * Get container attributes with dynamic host rewriting for UVE compatibility
     *
     * @param ContainerRef $containerRef Container reference
     * @param string|null $dynamicHost Dynamic host for rewriting
     * @return array<string, mixed> Container attributes
     */
    public function getContainerAttributes(ContainerRef $containerRef, ?string $dynamicHost = null): array
    {
        $identifier = $containerRef->identifier ?? '';
        if ($dynamicHost) {
            $identifier = $this->rewriteContainerIdentifier($identifier, $dynamicHost);
        }
        
        return [
            'data-dot-object' => 'container',
            'data-dot-identifier' => $identifier,
            'data-dot-accept-types' => $containerRef->acceptTypes ?? '',
            'data-max-contentlets' => $containerRef->maxContentlets ?? '',
            'data-dot-uuid' => $containerRef->uuid ?? ''
        ];
    }

    /**
     * Get contentlet attributes with dynamic host rewriting for UVE compatibility
     *
     * @param Contentlet $content Contentlet object
     * @param ContainerRef $containerRef Container reference for context
     * @param string|null $dynamicHost Dynamic host for rewriting
     * @return array<string, mixed> Contentlet attributes
     */
    public function getContentletAttributes(Contentlet $content, ContainerRef $containerRef, ?string $dynamicHost = null): array
    {
        // Build container data manually from ContainerRef properties
        $containerData = [
            'acceptTypes' => $containerRef->acceptTypes,
            'identifier' => $containerRef->identifier,
            'maxContentlets' => $containerRef->maxContentlets,
            'variantId' => $containerRef->variantId ?? 'DEFAULT',
            'uuid' => $containerRef->uuid
        ];
        
        // Rewrite container identifier if dynamic host is provided
        if ($dynamicHost && isset($containerData['identifier'])) {
            $containerData['identifier'] = $this->rewriteContainerIdentifier($containerData['identifier'], $dynamicHost);
        }
        
        return [
            'data-dot-object' => 'contentlet',
            'data-dot-identifier' => $content->identifier ?? '',
            'data-dot-basetype' => $content->baseType ?? '',
            'data-dot-title' => $content->title ?? '',
            'data-dot-inode' => $content->inode ?? '',
            'data-dot-type' => $content->contentType ?? '',
            'data-dot-container' => json_encode($containerData)
        ];
    }

    /**
     * Get ghost contentlet attributes with dynamic host rewriting for UVE compatibility
     *
     * @param ContainerRef $containerRef Container reference
     * @param string|null $dynamicHost Dynamic host for rewriting
     * @return array<string, mixed> Ghost contentlet attributes
     */
    public function getGhostContentletAttributes(ContainerRef $containerRef, ?string $dynamicHost = null): array
    {
        // Build container data manually from ContainerRef properties
        $containerData = [
            'acceptTypes' => $containerRef->acceptTypes,
            'identifier' => $containerRef->identifier,
            'maxContentlets' => $containerRef->maxContentlets,
            'variantId' => $containerRef->variantId ?? 'DEFAULT',
            'uuid' => $containerRef->uuid
        ];
        
        // Rewrite container identifier if dynamic host is provided
        if ($dynamicHost && isset($containerData['identifier'])) {
            $containerData['identifier'] = $this->rewriteContainerIdentifier($containerData['identifier'], $dynamicHost);
        }
        
        return [
            'data-dot-object' => 'contentlet',
            'data-dot-identifier' => '',
            'data-dot-basetype' => '',
            'data-dot-title' => '',
            'data-dot-inode' => '',
            'data-dot-type' => '',
            'data-dot-container' => json_encode($containerData)
        ];
    }

    /**
     * Render a complete container using custom logic with dynamic host rewriting
     *
     * @param ContainerRef $containerRef Container reference
     * @param array<Contentlet> $contentlets Array of contentlets
     * @param string|null $mode Current mode for UVE detection
     * @param string|null $dynamicHost Dynamic DotCMS host (for UVE)
     * @return string Container HTML
     */
    public function renderContainer(ContainerRef $containerRef, array $contentlets, ?string $mode = null, ?string $dynamicHost = null): string
    {
        // Use our custom implementation with dynamic host rewriting instead of SDK's renderContainer
        $containerAttrs = $this->getContainerAttributes($containerRef, $dynamicHost);
        $containerAttrsHtml = DotCmsHelper::htmlAttributes($containerAttrs);
        $hasContentlets = !empty($contentlets);
        $isEditMode = $mode === 'EDIT_MODE';
        
        $html = "<div{$containerAttrsHtml}>";
        
        if ($hasContentlets) {
            // Render contentlets with rewritten container references
            foreach ($contentlets as $content) {
                $contentAttrs = $this->getContentletAttributes($content, $containerRef, $dynamicHost);
                $contentAttrsHtml = DotCmsHelper::htmlAttributes($contentAttrs);
                
                $contentHtml = $this->generateHtmlBasedOnProperty($content, $dynamicHost);
                
                $html .= "<div{$contentAttrsHtml}>{$contentHtml}</div>";
            }
        } elseif ($isEditMode) {
            // Render empty container with ghost contentlet for UVE
            $ghostAttrs = $this->getGhostContentletAttributes($containerRef, $dynamicHost);
            $ghostAttrsHtml = DotCmsHelper::htmlAttributes($ghostAttrs);
            $placeholder = DotCmsHelper::generateEmptyContainerPlaceholder();
            
            $html .= "<div{$ghostAttrsHtml} class=\"uve-ghost-contentlet\">{$placeholder}</div>";
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Get CSS for empty container styling
     *
     * @return string CSS styles
     */
    public function getEmptyContainerCSS(): string
    {
        return DotCmsHelper::getEmptyContainerCSS();
    }

    /**
     * Check if we're in edit mode for UVE
     *
     * @param string|null $mode Current mode parameter
     * @return bool True if in edit mode
     */
    public function isEditMode(?string $mode): bool
    {
        return DotCmsHelper::isEditMode($mode);
    }

    /**
     * Generate empty container placeholder HTML
     *
     * @param string $message Custom message for empty container
     * @return string HTML for empty container placeholder
     */
    public function generateEmptyContainerPlaceholder(string $message = 'This container is empty.'): string
    {
        return DotCmsHelper::generateEmptyContainerPlaceholder($message);
    }

    /**
     * Check if a template exists
     *
     * @param string $template Template name
     * @return bool True if template exists
     */
    public function templateExists(string $template): bool
    {
        return $this->twig->getLoader()->exists($template);
    }

    public function getContainersData(array $containers, array $containerRef): array 
    {
        $containerData = DotCmsHelper::getContainerData($containers, $containerRef);
        
        if (!$containerData) {
            throw new RuntimeException("Container not found: " . ($containerRef['identifier'] ?? 'unknown'));
        }
        
        if (empty($containerData['contentlets'])) {
            error_log("No contentlets found for container: " . ($containerRef['identifier'] ?? 'unknown') . ", uuid: " . ($containerRef['uuid'] ?? 'unknown'));
        }
        
        return $containerData;
    }
} 