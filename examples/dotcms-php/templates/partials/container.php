<?php
if (!isset($containerRef)) {
    echo "<!-- Error: Container identifier missing -->";
    return;
}

global $pageAsset;

$identifier = $containerRef->identifier ?? null;
$uuid = $containerRef->uuid ?? null;

// Note: dotCMS stores contentlets with a "uuid-" prefix in lowercase
$contentlets = $pageAsset->containers[$identifier]->contentlets[strtolower("uuid-" . $uuid)] ?? null;


if ($contentlets) {
    foreach ($contentlets as $contentlet) {
        $contentTypeVar = $contentlet->contentType ?? 'unknown';
        $templatePath = dirname(__DIR__) . '/content-type/' . strtolower($contentTypeVar) . '.php';

        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            include dirname(__DIR__) . '/content-type/content-type-not-found.php';
        }
    }
} else {
    echo "<!-- Container $identifier doesn't have contentlets -->";
}
