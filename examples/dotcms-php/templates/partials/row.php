<?php
if (!isset($row)) {
    echo "<!-- Error: Row data missing -->";
    return;
}

$rowStyleClass = isset($row->styleClass) ? ' ' . htmlspecialchars($row->styleClass) : '';
?>
<div class="container">
    <div class="row<?= $rowStyleClass ?>">
        <?php
        if (isset($row->columns) && is_array($row->columns)) {
            foreach ($row->columns as $column) {
                include __DIR__ . '/column.php';
            }
        } else {
            echo "<!-- No Columns found in this Row -->";
        }
        ?>
    </div>
</div>
