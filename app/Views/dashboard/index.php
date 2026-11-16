
<?php

use App\Core\Security;

$user = $user ?? [];
$statistics = $statistics ?? [];

$name = e($user['name'] ?? 'User');
$email = e($user['email'] ?? '');
$companyId = (int) ($user['company_id'] ?? 0);

$totalProducts = (int) ($statistics['products'] ?? 0);
$totalWarehouses = (int) ($statistics['warehouses'] ?? 0);
$totalSuppliers = (int) ($statistics['suppliers'] ?? 0);
$totalMovements = (int) ($statistics['stock_movements'] ?? 0);

$csrf = e(Security::csrfToken());

$initial = strtoupper(substr($user['name'] ?? 'U', 0, 1));
?>

<style>
.sp-dashboard {
    min-height: 100vh;
    padding: 28px;
    color: #f1f5f9;
    background:
        radial-gradient(ellipse at 15% 0%,rgba(37,99,235,.2),transparent 40%),
        radial-gradient(ellipse at 90% 10%,rgba(124,58,237,.15),transparent 35%),
        #080d1b;
    font-family: Inter,system-ui,sans-serif;
}

.sp-topbar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:18px;
    margin-bottom:38px;
}

.sp-brand {
    display:flex;
    align-items:center;
    gap:13px;
}

.sp-logo {
    width:48px;
    height:48px;
    display:grid;
    place-items:center;
    border-radius:15px;
    color:white;
    font-size:23px;
    font-weight:900;
    background:linear-gradient(135deg,#2563eb,#9333ea);
    box-shadow:0 8px 30px #2563eb55;
}

.sp-brand h2 {
    margin:0;
    font-size:21px;
    font-weight:850;
}

.sp-brand p {
    margin:4px 0 0;
    color:#94a3b8;
    font-size:12px;
}

.sp-user {
    display:flex;
    align-items:center;
    gap:12px;
    padding:10px 14px;
    border:1px solid #334155;
    border-radius:15px;
    background:#172033;
}

.sp-avatar {
    width:40px;
    height:40px;
    display:grid;
    place-items:center;
    border-radius:12px;
    background:linear-gradient(135deg,#0ea5e9,#6366f1);
    font-weight:800;
}

.sp-user strong,.sp-user small {
    display:block;
}

.sp-user strong {font-size:13px}
.sp-user small {color:#94a3b8;font-size:11px}

.sp-logout {
    padding:10px 14px;
    border:1px solid #7f1d1d;
    border-radius:10px;
    color:#fca5a5;
    background:#451a1a;
    cursor:pointer;
}

.sp-logout:hover {
    color:white;
    background:#dc2626;
}

.sp-welcome {margin-bottom:28px}

.sp-eyebrow {
    color:#38bdf8;
    font-size:11px;
    font-weight:800;
    letter-spacing:2px;
}

.sp-welcome h1 {
    margin:12px 0;
    font-size:clamp(27px,4vw,42px);
    font-weight:850;
    letter-spacing:-1px;
}

.sp-welcome p {color:#94a3b8}

.sp-grid {
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:18px;
    margin-bottom:28px;
}

.sp-card {
    padding:23px;
    border:1px solid #33415588;
    border-radius:19px;
    background:rgba(20,31,55,.85);
    backdrop-filter:blur(14px);
    transition:.3s;
    animation:sp-enter .6s ease both;
}

.sp-card:hover {
    transform:translateY(-6px);
    border-color:#38bdf8;
}

.sp-card-icon {
    width:45px;
    height:45px;
    display:grid;
    place-items:center;
    border-radius:13px;
    margin-bottom:18px;
    font-size:22px;
}

.sp-card h3 {
    margin:0;
    color:#94a3b8;
    font-size:12px;
    font-weight:600;
}

.sp-number {
    display:block;
    margin-top:10px;
    font-size:32px;
    font-weight:850;
}

.sp-card small {
    display:block;
    margin-top:8px;
    color:#94a3b8;
    font-size:11px;
}

.sp-blue {background:#0c4a6e66;color:#38bdf8}
.sp-purple {background:#581c8766;color:#c4b5fd}
.sp-green {background:#064e3b66;color:#34d399}
.sp-orange {background:#7c2d1266;color:#fb923c}

.sp-panel {
    padding:24px;
    margin-bottom:22px;
    border:1px solid #33415588;
    border-radius:19px;
    background:rgba(20,31,55,.85);
}

.sp-heading {
    margin:0 0 20px;
    font-size:17px;
    font-weight:800;
}

.sp-actions {
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:14px;
}

.sp-action {
    display:block;
    padding:19px;
    border:1px solid #334155;
    border-radius:14px;
    color:#f1f5f9;
    background:#0f172a;
    text-decoration:none;
    transition:.25s;
}

.sp-action:hover {
    transform:translateY(-4px);
    border-color:#38bdf8;
    color:#38bdf8;
}

.sp-action span {
    display:block;
    margin-bottom:12px;
    font-size:25px;
}

.sp-action strong {font-size:13px}

.sp-action small {
    display:block;
    margin-top:6px;
    color:#94a3b8;
    font-size:11px;
}

.sp-account {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:14px;
}

.sp-account-item {
    padding:16px;
    border-radius:12px;
    background:#0f172a;
}

.sp-account-item small {
    display:block;
    margin-bottom:8px;
    color:#94a3b8;
    font-size:11px;
}

.sp-account-item strong {
    overflow-wrap:anywhere;
    font-size:13px;
}

.sp-footer {
    padding:20px 0 5px;
    text-align:center;
    color:#64748b;
    font-size:11px;
}

@keyframes sp-enter {
    from {opacity:0;transform:translateY(20px)}
    to {opacity:1;transform:translateY(0)}
}

@media(max-width:950px) {
    .sp-grid,.sp-actions {grid-template-columns:repeat(2,1fr)}
}

@media(max-width:600px) {
    .sp-dashboard {padding:16px}
    .sp-grid {gap:10px}
    .sp-card {padding:15px}
    .sp-number {font-size:25px}
    .sp-user {width:100%;flex-wrap:wrap}
    .sp-actions,.sp-account {grid-template-columns:1fr 1fr}
}
</style>

<div class="sp-dashboard">

    <header class="sp-topbar">

        <div class="sp-brand">
            <div class="sp-logo">S</div>
            <div>
                <h2>STOCKPILOT <span style="color:#38bdf8">360</span></h2>
                <p>Enterprise Inventory Intelligence</p>
            </div>
        </div>

        <div class="sp-user">
            <div class="sp-avatar"><?= e($initial) ?></div>

            <div>
                <strong><?= $name ?></strong>
                <small>Company ID: <?= $companyId ?></small>
            </div>

            <form action="<?= e(url('/logout')) ?>" method="POST">
                <input type="hidden" name="_csrf_token" value="<?= $csrf ?>">
                <button class="sp-logout" type="submit">Logout ↗</button>
            </form>
        </div>

    </header>

    <section class="sp-welcome">
        <span class="sp-eyebrow">● SYSTEM ONLINE</span>
        <h1>Welcome back, <?= $name ?> 👋</h1>
        <p>Your inventory management workspace is ready.</p>
    </section>

    <section class="sp-grid">

        <article class="sp-card">
            <div class="sp-card-icon sp-blue">📦</div>
            <h3>Total Products</h3>
            <span class="sp-number"><?= number_format($totalProducts) ?></span>
            <small>Registered products</small>
        </article>

        <article class="sp-card">
            <div class="sp-card-icon sp-purple">🏭</div>
            <h3>Total Warehouses</h3>
            <span class="sp-number"><?= number_format($totalWarehouses) ?></span>
            <small>Registered warehouses</small>
        </article>

        <article class="sp-card">
            <div class="sp-card-icon sp-green">🚚</div>
            <h3>Total Suppliers</h3>
            <span class="sp-number"><?= number_format($totalSuppliers) ?></span>
            <small>Registered suppliers</small>
        </article>

        <article class="sp-card">
            <div class="sp-card-icon sp-orange">📊</div>
            <h3>Stock Movements</h3>
            <span class="sp-number"><?= number_format($totalMovements) ?></span>
            <small>Recorded stock movements</small>
        </article>

    </section>

    <section class="sp-panel">
        <h2 class="sp-heading">Quick Access</h2>

        <div class="sp-actions">

            <a class="sp-action" href="<?= e(url('/products')) ?>">
                <span>📦</span>
                <strong>Products</strong>
                <small>Manage product catalog</small>
            </a>

            <a class="sp-action" href="<?= e(url('/inventory')) ?>">
                <span>📊</span>
                <strong>Inventory</strong>
                <small>Track stock levels</small>
            </a>

            <a class="sp-action" href="<?= e(url('/suppliers')) ?>">
                <span>🚚</span>
                <strong>Suppliers</strong>
                <small>Manage suppliers</small>
            </a>

            <a class="sp-action" href="<?= e(url('/warehouses')) ?>">
                <span>🏭</span>
                <strong>Warehouses</strong>
                <small>Manage warehouses</small>
            </a>

        </div>
    </section>

    <section class="sp-panel">
        <h2 class="sp-heading">Account Information</h2>

        <div class="sp-account">

            <div class="sp-account-item">
                <small>USER NAME</small>
                <strong><?= $name ?></strong>
            </div>

            <div class="sp-account-item">
                <small>EMAIL ADDRESS</small>
                <strong><?= $email ?></strong>
            </div>

            <div class="sp-account-item">
                <small>COMPANY ID</small>
                <strong><?= $companyId ?></strong>
            </div>

        </div>
    </section>

    <footer class="sp-footer">
        STOCKPILOT 360 · SMART INVENTORY MANAGEMENT SYSTEM ·
        © <?= date('Y') ?>
    </footer>

</div>
