<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($pageAsset->page->title ?? 'dotCMS Page') ?></title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/layout.css">
</head>
<body>
    <?php if ($pageAsset->layout->header): ?>
    <header>
        <?php include __DIR__ . '/navigation.php'; ?>
    </header>
    <?php endif; ?>
    
    <main>
        <?php include __DIR__ . '/page.php'; ?>
    </main>
    
    <?php if ($pageAsset->layout->footer): ?>
    <footer>
        <div class="footer-content container">
            <p>&copy; <?= date('Y') ?> Your Company Name. All rights reserved.</p>
        </div>
    </footer>
    <?php endif; ?>

    <script src="https://demo.dotcms.com/ext/uve/dot-uve.js"></script>
    <script>
        if (window.dotUVE) {
            window.dotUVE.createSubscription('changes', (changes) => {
                window.location.reload();
            })
        }
    </script>
</body>
</html>
