<?php
if (!isset($containerRef)) {
    echo "<!-- Error: Container identifier missing -->";
    return;
}

global $pageAsset;

$identifier = $containerRef->identifier ?? null;
$uuid = $containerRef->uuid ?? null;
$contentlets = $containerRef->contentlets ?? null;

// Container attributes for UVE
$containerAttrs = [
    'data-dot-object' => 'container',
    'data-dot-identifier' => $identifier,
    'data-dot-uuid' => $uuid,
    'data-dot-accept-types' => $containerRef->acceptTypes ?? '',
    'data-max-contentlets' => $containerRef->maxContentlets ?? 0
];

// Build container HTML attributes string
$containerHtmlAttrs = '';
foreach ($containerAttrs as $attr => $value) {
    if ($value !== null && $value !== '') {
        $containerHtmlAttrs .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
    }
}
?>
<div<?= $containerHtmlAttrs ?>>
    <?php
    if ($contentlets) {
        foreach ($contentlets as $contentlet) {
            // Contentlet attributes for UVE
            $contentletAttrs = [
                'data-dot-object' => 'contentlet',
                'data-dot-identifier' => $contentlet->identifier ?? '',
                'data-dot-inode' => $contentlet->inode ?? '',
                'data-dot-type' => $contentlet->contentType ?? '',
                'data-dot-basetype' => $contentlet->baseType ?? '',
                'data-dot-title' => $contentlet->title ?? '',
                'data-dot-container' => json_encode([
                    'identifier' => $identifier,
                    'uuid' => $uuid,
                    'acceptTypes' => $containerRef->acceptTypes ?? [],
                    'maxContentlets' => $containerRef->maxContentlets ?? 0,
                    'variantId' => $containerRef->variantId ?? null
                ])
            ];

            // Build contentlet HTML attributes string
            $contentletHtmlAttrs = '';
            foreach ($contentletAttrs as $attr => $value) {
                if ($value !== null && $value !== '') {
                    $contentletHtmlAttrs .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
                }
            }

            $contentTypeVar = $contentlet->contentType ?? 'unknown';
            $templatePath = dirname(__DIR__) . '/content-type/' . strtolower($contentTypeVar) . '.php';
            ?>
            <div<?= $contentletHtmlAttrs ?>>
                <?php
                if (file_exists($templatePath)) {
                    include $templatePath;
                } else {
                    include dirname(__DIR__) . '/content-type/content-type-not-found.php';
                }
                ?>
            </div>
            <?php
        }
    } else {
        echo "<!-- Container $identifier doesn't have contentlets -->";
    }
    ?>
</div>
