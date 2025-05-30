<?php

namespace App\Twig;

use Dotcms\PhpSdk\Utils\DotCmsHelper;
use Dotcms\PhpSdk\Model\Content\Contentlet;
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
            new TwigFunction('htmlAttr', [$this, 'htmlAttr'], ['is_safe' => ['html']])
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

    public function generateHtmlBasedOnProperty(Contentlet $content): string 
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
                    'dotcms_host' => $_ENV['DOTCMS_HOST'] ?? 'https://demo.dotcms.com'
                ]);
            }
        }

        // Fall back to the SDK simple HTML renderer
        return DotCmsHelper::simpleContentHtml($content->jsonSerialize());
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