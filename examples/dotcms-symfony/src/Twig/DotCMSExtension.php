<?php

namespace App\Twig;

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
        return implode(' ', array_map(
            fn($key, $value) => is_bool($value) 
                ? sprintf('%s="%s"', $key, $value ? 'true' : 'false')
                : sprintf('%s="%s"', $key, htmlspecialchars((string)$value, ENT_QUOTES)),
            array_keys($attrs),
            $attrs
        ));
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
            return '';
        }

        try {
            return $twig->render($template, ['content' => $content]);
        } catch (\Exception $e) {
            return '';
        }
    }

    public function getContainersData(array $containers, array $containerRef): array 
    {
        $identifier = $containerRef['identifier'] ?? throw new RuntimeException("Missing container identifier");
        $uuid = $containerRef['uuid'] ?? throw new RuntimeException("Missing container UUID");
        
        $containerData = $containers[$identifier] ?? throw new RuntimeException("Container not found: $identifier");
        $structures = $containerData['containerStructures'] ?? [];
        $container = $containerData['container'] ?? [];
        
        $contentlets = $containerData['contentlets']["uuid-$uuid"] 
            ?? $containerData['contentlets']["uuid-dotParser_$uuid"] 
            ?? [];
        
        if (empty($contentlets)) {
            error_log("No contentlets found for container: $identifier, uuid: $uuid");
        }
        
        return [
            ...$container,
            'acceptTypes' => implode(',', array_column($structures, 'contentTypeVar')),
            'contentlets' => $contentlets,
            'variantId' => $container['parentPermissionable']['variantId'] ?? null
        ];
    }
} 