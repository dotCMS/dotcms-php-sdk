<?php
global $pageAsset;

if (!isset($pageAsset)) {
    echo '<div class="container"><p>Error: Page asset is not set.</p></div>';
    return;
}

if (!isset($pageAsset->layout)) {
    echo '<div class="container"><p>Error: Layout data is not set.</p></div>';
    return;
}

if (!isset($pageAsset->layout->body->rows) || !is_array($pageAsset->layout->body->rows)) {
    echo '<div class="container"><p>No layout rows found or page data unavailable.</p></div>';
    return;
}

foreach ($pageAsset->layout->body->rows as $row) {
    include __DIR__ . '/partials/row.php'; 
}
