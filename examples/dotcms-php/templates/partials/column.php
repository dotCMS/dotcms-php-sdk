<?php
if (!isset($column)) {
    echo "<!-- Error: Column data missing -->";
    return;
}

// Calculate grid classes based on dotCMS layout properties
$leftOffset = $column->leftOffset ?? 1;
$width = $column->width ?? 12;
$columnClasses = 'col-start-' . $leftOffset . ' col-end-' . ($width + $leftOffset);

if (isset($column->styleClass)) {
    $columnClasses .= ' ' . htmlspecialchars($column->styleClass);
}
?>
<div 
    data-dot-object="column"
    class="<?= $columnClasses ?>"
>
    <?php
    if (isset($column->containers) && is_array($column->containers)) {
        foreach ($column->containers as $containerRef) {
            include __DIR__ . '/container.php';
        }
    } else {
        echo "<!-- No Containers found in this Column -->";
    }
    ?>
</div>
