<?php

namespace App\Twig;

use Dotcms\PhpSdk\Utils\DotCmsHelper;
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
            new TwigFunction('getContainersData', [$this, 'getContainersData']),
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

    public function generateHtmlBasedOnProperty(array $content): string 
    {
        if (!isset($content['contentType'])) {
            return '';
        }

        $twig = $this->twig;
        $template = match($content['contentType']) {
            'Banner' => 'dotcms/content-types/banner.twig',
            'Product' => 'dotcms/content-types/product.twig',
            'Activity' => 'dotcms/content-types/activity.twig',
            default => ''
        };

        if (empty($template)) {
            // Fall back to the SDK simple content HTML renderer if no template is found
            return DotCmsHelper::simpleContentHtml($content);
        }

        try {
            return $twig->render($template, ['content' => $content]);
        } catch (\Exception $e) {
            // Fall back to the SDK simple content HTML renderer if rendering fails
            return DotCmsHelper::simpleContentHtml($content);
        }
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