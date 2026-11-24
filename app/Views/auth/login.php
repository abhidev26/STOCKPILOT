
<?php

use App\Core\Security;

$title = $title ?? 'Login | StockPilot 360';
$error = $error ?? null;
$old = $old ?? [];

$companyId = htmlspecialchars(
    (string) ($old['company_id'] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$email = htmlspecialchars(
    (string) ($old['email'] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$errorMessage = htmlspecialchars(
    (string) ($error ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$csrfToken = htmlspecialchars(
    Security::csrfToken(),
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: #f4f8ff;
            background:
                radial-gradient(circle at 15% 20%, #123b69 0, transparent 35%),
                radial-gradient(circle at 85% 80%, #073e54 0, transparent 35%),
                #050b17;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 25px;
            overflow-x: hidden;
        }

        .background-glow {
            position: fixed;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            filter: blur(100px);
            opacity: .22;
            background: #00c8ff;
            top: 10%;
            left: 10%;
            animation: float 7s infinite alternate;
            pointer-events: none;
        }

        @keyframes float {
            to { transform: translate(100px, 80px); }
        }

        .login-wrapper {
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 50px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: clamp(36px, 5vw, 65px);
            line-height: 1.1;
            font-weight: 800;
            margin: 25px 0;
        }

        .hero h1 span {
            color: #36d5ff;
        }

        .hero p {
            color: #a6b8d1;
            line-height: 1.8;
            max-width: 480px;
        }

        .brand {
            color: #38d5ff;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 3px;
        }

        .orbital {
            width: 210px;
            height: 210px;
            margin: 40px auto 0;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 25%, #9af1ff, #0789c5 25%, #09284c 65%, #050b17);
            box-shadow: 0 0 50px #00bfff66, inset -20px -20px 40px #020817;
            position: relative;
            animation: rotate 12s linear infinite;
        }

        .orbital::before,
        .orbital::after {
            content: "";
            position: absolute;
            border: 1px solid #35d8ff99;
            border-radius: 50%;
            inset: -30px -65px;
            transform: rotate(-25deg);
        }

        .orbital::after {
            transform: rotate(65deg);
        }

        @keyframes rotate {
            to { transform: rotate(360deg); }
        }

        .login-card {
            background: linear-gradient(145deg, #14243bdc, #081323f5);
            border: 1px solid #3d83a966;
            border-radius: 25px;
            padding: 38px;
            box-shadow: 0 25px 80px #0008;
            backdrop-filter: blur(20px);
        }

        .login-card h2 {
            font-size: 29px;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #94a9c5;
            font-size: 13px;
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            font-size: 13px;
            color: #c4d5e9;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 15px;
            color: white;
            background: #071323;
            border: 1px solid #294565;
            border-radius: 11px;
            outline: none;
            font-size: 14px;
            transition: .3s;
        }

        input:focus {
            border-color: #29cfff;
            box-shadow: 0 0 15px #16bfff33;
        }

        input::placeholder {
            color: #61758e;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 75px;
        }

        .toggle-password {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #40d5ff;
            cursor: pointer;
        }

        .submit-btn {
            width: 100%;
            border: 0;
            border-radius: 11px;
            padding: 16px;
            background: linear-gradient(100deg, #078ac9, #21d3e8);
            color: #001321;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            transition: .3s;
            margin-top: 8px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px #00bfff55;
        }

        .error-message {
            background: #702b3840;
            border: 1px solid #ff7181;
            color: #ffadb6;
            padding: 12px;
            border-radius: 9px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .security-note {
            text-align: center;
            color: #7890aa;
            font-size: 11px;
            margin-top: 23px;
        }

        .footer {
            margin-top: 22px;
            color: #7188a3;
            font-size: 11px;
            text-align: center;
        }

        @media(max-width: 800px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 480px;
                gap: 35px;
            }

            .hero {
                text-align: center;
            }

            .hero p {
                margin: auto;
            }

            .orbital {
                width: 120px;
                height: 120px;
                margin-top: 35px;
            }

            .login-card {
                padding: 25px;
            }
        }
    </style>
</head>

<body>

<div class="background-glow"></div>

<main class="login-wrapper">

    <section class="hero">
        <div class="brand">STOCKPILOT 360</div>

        <h1>
            Control Your<br>
            <span>Inventory Future.</span>
        </h1>

        <p>
            A smarter way to manage inventory, warehouses,
            suppliers and business operations.
            Secure access to your enterprise workspace.
        </p>

        <div class="orbital"></div>
    </section>

    <section class="login-card">

        <h2>Welcome Back</h2>

        <p class="subtitle">
            Sign in to access your StockPilot workspace.
        </p>

        <?php if ($errorMessage !== ''): ?>
            <div class="error-message" role="alert">
                <?= $errorMessage ?>
            </div>
        <?php endif; ?>

        <form
            id="loginForm"
            action="<?= htmlspecialchars(url('/login'), ENT_QUOTES, 'UTF-8') ?>"
            method="POST"
        >

            <input
                type="hidden"
                name="_csrf_token"
                value="<?= $csrfToken ?>"
            >

            <div class="form-group">
                <label for="company_id">Company ID</label>

                <input
                    type="number"
                    id="company_id"
                    name="company_id"
                    placeholder="Enter your Company ID"
                    min="1"
                    value="<?= $companyId ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your business email"
                    autocomplete="username"
                    value="<?= $email ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <div class="password-wrap">
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
                        class="toggle-password"
                        id="togglePassword"
                    >
                        Show
                    </button>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                Sign In Securely →
            </button>

        </form>

        <div class="security-note">
            🔒 Protected enterprise authentication
        </div>

        <div class="footer">
            © <?= date('Y') ?> StockPilot 360. All rights reserved.
        </div>

    </section>

</main>

<script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', function () {
        const hidden = passwordInput.type === 'password';

        passwordInput.type = hidden ? 'text' : 'password';
        togglePassword.textContent = hidden ? 'Hide' : 'Show';
    });
</script>

</body>
</html>
