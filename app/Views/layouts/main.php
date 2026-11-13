<?php
/** @var string $content */
/** @var string|null $title */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? 'Welcome') . ' | ' . config('app.name')) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
    <header class="topbar">
        <div class="container topbar__inner">
            <a class="brand" href="<?= e(url('/')) ?>">
                <span class="brand__mark" aria-hidden="true">SP</span>
                <span><?= e(config('app.name')) ?></span>
            </a>
            <span class="badge badge--env"><?= e(config('app.env')) ?> &middot; v<?= e(config('app.version')) ?></span>
        </div>
    </header>

    <main class="container">
        <?= $content ?>
    </main>

    <footer class="footer container">
        Smart Inventory &amp; Supply Chain Management
    </footer>
</body>
</html>
