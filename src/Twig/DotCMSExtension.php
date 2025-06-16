<?php

namespace Dotcms\PhpSdk\Twig;

use Dotcms\PhpSdk\Model\Content\Contentlet;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Dotcms\PhpSdk\Utils\DotCmsHelper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * DotCMS Twig Extension for empty container support
 */
class DotCMSExtension extends AbstractExtension
{
    public function __construct(
        private Environment $twig
    ) {
    }

    /**
     * @return array<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderContainer', [$this, 'renderContainer'], ['is_safe' => ['html']]),
            new TwigFunction('getEmptyContainerCSS', [$this, 'getEmptyContainerCSS'], ['is_safe' => ['html']]),
            new TwigFunction('htmlAttr', [$this, 'htmlAttr'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Render a complete container with empty state support
     *
     * @param ContainerRef $containerRef Container reference
     * @param array<Contentlet> $contentlets Array of contentlets
     * @param string|null $mode Current mode (EDIT_MODE for UVE)
     * @param callable|null $contentRenderer Custom content renderer
     * @return string Rendered container HTML
     */
    public function renderContainer(
        ContainerRef $containerRef,
        array $contentlets,
        ?string $mode = null,
        ?callable $contentRenderer = null
    ): string {
        // If no custom renderer provided, use the existing Twig content rendering logic
        if ($contentRenderer === null) {
            $twig = $this->twig; // Capture for closure
            $contentRenderer = function (Contentlet $content) use ($twig) {
                $contentType = $content->contentType;
                if ($contentType) {
                    $template = 'dotcms/content-types/' . strtolower($contentType) . '.twig';
                    if ($twig->getLoader()->exists($template)) {
                        return $twig->render($template, [
                            'content' => $content,
                            'dotcms_host' => $_ENV['DOTCMS_HOST'] ?? 'https://demo.dotcms.com',
                        ]);
                    }
                }

                // Fall back to the SDK simple HTML renderer
                return DotCmsHelper::simpleContentHtml($content->jsonSerialize());
            };
        }

        return DotCmsHelper::renderContainer($containerRef, $contentlets, $mode, $contentRenderer);
    }

    /**
     * Get CSS styles for empty containers
     *
     * @return string CSS for empty container styling
     */
    public function getEmptyContainerCSS(): string
    {
        return DotCmsHelper::getEmptyContainerCSS();
    }

    /**
     * Generate HTML attributes from array
     *
     * @param array<string, mixed> $attrs Attributes array
     * @return string HTML attributes string
     */
    public function htmlAttr(array $attrs): string
    {
        return DotCmsHelper::htmlAttributes($attrs);
    }
}
