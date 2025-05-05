<?php
if (!isset($contentlet)) {
    echo "<!-- Error: Contentlet data missing -->";
    return;
}

$contentTypeVar = $contentlet->contentType ?? 'unknown';
?>
<div class="content-type-not-found">
    <p>Template Not Found: <b><?= htmlspecialchars($contentTypeVar) ?></b></p>
</div>
