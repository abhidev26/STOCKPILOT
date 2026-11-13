<?php
/** @var string $title */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#080d1b">
    <title><?= e($title) ?> | <?= e(config('app.name')) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/login.css')) ?>">
</head>
<body>

<div class="login-page">

    <div class="background-glow glow-one"></div>
    <div class="background-glow glow-two"></div>
    <div class="grid-overlay"></div>

    <header class="login-header">
        <a href="<?= e(url('/')) ?>" class="brand">
            <span class="brand-icon">SP</span>
            <span>STOCK<span class="brand-highlight">PILOT</span></span>
        </a>

        <span class="header-status">
            <span class="status-dot"></span>
            INVENTORY MANAGEMENT SYSTEM
        </span>
    </header>

    <main class="login-main">

        <section class="welcome-section">
            <div class="eyebrow">
                <span></span> SMART BUSINESS MANAGEMENT
            </div>

            <h1>
                Manage smarter.<br>
                <span>Grow faster.</span>
            </h1>

            <p class="welcome-description">
                Take control of your inventory, streamline operations
                and make better business decisions with StockPilot 360.
            </p>

            <div class="feature-list">
                <div class="feature-item">
                    <span class="feature-icon">↗</span>
                    <div>
                        <strong>Real-time visibility</strong>
                        <small>Stay updated with your inventory.</small>
                    </div>
                </div>

                <div class="feature-item">
                    <span class="feature-icon">◇</span>
                    <div>
                        <strong>Smarter operations</strong>
                        <small>Manage products and suppliers efficiently.</small>
                    </div>
                </div>

                <div class="feature-item">
                    <span class="feature-icon">⌁</span>
                    <div>
                        <strong>Business intelligence</strong>
                        <small>Turn business data into insights.</small>
                    </div>
                </div>
            </div>

            <div class="welcome-footer">
                STOCKPILOT 360 <span>•</span> SMART INVENTORY PLATFORM
            </div>
        </section>

        <section class="login-card">

            <div class="card-top">
                <div class="card-logo">SP</div>
                <span class="secure-label">
                    <span class="status-dot"></span> SECURE ACCESS
                </span>
            </div>

            <div class="form-heading">
                <p class="form-eyebrow">WELCOME BACK</p>
                <h2>Sign in to your account</h2>
                <p>Enter your credentials to access your workspace.</p>
            </div>

            <form id="loginForm" novalidate>

                <div class="form-group">
                    <label for="email">Email address</label>
                    <div class="input-wrap">
                        <span class="input-icon">✉</span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="you@company.com"
                            autocomplete="username"
                            required
                        >
                    </div>
                    <small class="field-error" id="emailError"></small>
                </div>

                <div class="form-group">
                    <div class="label-row">
                        <label for="password">Password</label>
                        <a href="#" class="forgot-link" id="forgotLink">
                            Forgot password?
                        </a>
                    </div>

                    <div class="input-wrap">
                        <span class="input-icon">♧</span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword"
                            aria-label="Show password"
                        >SHOW</button>
                    </div>
                    <small class="field-error" id="passwordError"></small>
                </div>

                <div class="form-options">
                    <label class="remember-option">
                        <input type="checkbox" id="remember">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="login-button">
                    <span>Sign in to workspace</span>
                    <span class="button-arrow">→</span>
                </button>

                <p class="form-message" id="formMessage" aria-live="polite"></p>

            </form>

            <div class="card-bottom">
                <span class="bottom-lock">▣</span>
                Your workspace access is protected.
            </div>

        </section>

    </main>

    <footer class="page-footer">
        <span>© <?= date('Y') ?> StockPilot 360</span>
        <span>SMART INVENTORY &amp; SUPPLY CHAIN MANAGEMENT</span>
    </footer>

</div>

<script src="<?= e(asset('js/login.js')) ?>"></script>
</body>
</html>