<?php
/**
 * @var list<array{label:string,status:string,detail:string}> $checks
 * @var bool $allPassed
 * @var string|null $dbError
 */
$labels = ['ok' => 'Pass', 'warn' => 'Notice', 'fail' => 'Fail'];
?>
<section class="hero">
    <h1>StockPilot is running</h1>
    <p class="muted">Stage 1 &mdash; project foundation. This page checks that your setup is ready for Stage 2.</p>
</section>

<section class="card">
    <div class="card__head">
        <h2>System check</h2>
        <?php if ($allPassed): ?>
            <span class="badge badge--ok">All required checks passed</span>
        <?php else: ?>
            <span class="badge badge--fail">Action needed</span>
        <?php endif; ?>
    </div>

    <ul class="checks">
        <?php foreach ($checks as $check): ?>
            <li class="check">
                <span class="badge badge--<?= e($check['status']) ?>"><?= e($labels[$check['status']] ?? $check['status']) ?></span>
                <div>
                    <strong><?= e($check['label']) ?></strong>
                    <div class="muted"><?= e($check['detail']) ?></div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($dbError !== null): ?>
        <pre class="debug"><?= e($dbError) ?></pre>
    <?php endif; ?>
</section>

<section class="card">
    <h2>Useful links</h2>
    <p><a href="<?= e(url('health')) ?>">/health</a> &mdash; JSON status endpoint</p>
</section>
