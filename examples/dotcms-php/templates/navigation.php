<?php
// Remove the placeholder content
?>
<nav class="container" role="navigation" aria-label="Main navigation">
    <ul>
        <?php if (isset($nav)): ?>
            <?php 
            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            foreach ($nav->children as $item): 
                $isActive = $currentPath === $item->href;
            ?>
                <li>
                    <a href="<?= htmlspecialchars($item->href ?? '#') ?>" 
                       target="<?= htmlspecialchars($item->target ?? '_self') ?>"
                       <?= $isActive ? 'class="active"' : '' ?>>
                        <?= htmlspecialchars($item->title ?? 'Menu Item') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li><a href="/">Home (Fallback)</a></li>
        <?php endif; ?>
    </ul>
</nav>
