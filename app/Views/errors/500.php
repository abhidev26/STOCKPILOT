<?php /** @var string|null $message */ ?>
<section class="hero">
    <h1>500 &mdash; Something went wrong</h1>
    <p class="muted">The error has been logged. Please try again in a moment.</p>
    <?php if (!empty($message)): ?>
        <pre class="debug"><?= e($message) ?></pre>
    <?php endif; ?>
    <p><a class="btn" href="<?= e(url('/')) ?>">Back to home</a></p>
</section>
