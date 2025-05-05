<?php
$title = $contentlet->title ?? '';
$caption = $contentlet->caption ?? '';
$image = $contentlet->image ?? null;
$link = $contentlet->link ?? '#';
$buttonText = $contentlet->buttonText ?? 'Learn More';
?>
<div style="position: relative; width: 100%; padding: 1rem; background-color: #e5e7eb; height: 24rem;">
    <?php if ($image): ?>
        <img 
            src="https://demo.dotcms.com/dA/<?= htmlspecialchars($image->idPath ?? $image) ?>" 
            style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;"
            alt="<?= htmlspecialchars($title) ?>"
        />
    <?php endif; ?>
    <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem; text-align: center; color: white;">
        <h2 style="margin-bottom: 0.5rem; font-size: 3.75rem; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
            <?= htmlspecialchars($title) ?>
        </h2>
        <?php if (!empty($caption)): ?>
            <p style="margin-bottom: 1rem; font-size: 1.25rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"><?= htmlspecialchars($caption) ?></p>
        <?php endif; ?>
        <a 
            href="<?= htmlspecialchars($link) ?>"
            style="padding: 1rem; font-size: 1.25rem; background-color: #8b5cf6; color: white; text-decoration: none; border-radius: 0.25rem; transition: background-color 0.3s;"
            onmouseover="this.style.backgroundColor='#7c3aed'"
            onmouseout="this.style.backgroundColor='#8b5cf6'"
        >
            <?= htmlspecialchars($buttonText) ?>
        </a>
    </div>
</div>
