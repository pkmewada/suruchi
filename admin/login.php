<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/../includes/db.php';   // db() + e() yahin se aate hain

if (!empty($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

$error = '';
$email = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $csrf     = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        $error = 'Your session expired. Please refresh the page and try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Please enter a valid email address and password.';
    } else {
        try {
            $stmt = db()->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']    = (int)$user['id'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                header('Location: home.php');
                exit;
            }
            $error = 'Email or password is incorrect.';
        } catch (PDOException $e) {
            $error = 'Unable to sign in right now. Check your database setup and try again.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#25162d">
    <title>Login | Suruchi Creation</title>
    <style>
        :root {
            --purple: #4b2862;
            --deep-purple: #24152d;
            --maroon: #8e254b;
            --red: #c43b48;
            --ink: #251c2c;
            --muted: #786d7d;
            --glass: rgba(255,255,255,.72);
            --line: rgba(255,255,255,.72);
        }
        * { box-sizing: border-box; }
        html { min-height: 100%; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 28px 16px;
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 12% 18%, rgba(105, 57, 130, .30), transparent 32%),
                radial-gradient(circle at 88% 80%, rgba(196, 59, 72, .23), transparent 34%),
                linear-gradient(135deg, #f8f5fa 0%, #f5edf3 45%, #fdf6f4 100%);
            overflow-x: hidden;
        }
        .orb { position: fixed; border-radius: 50%; filter: blur(1px); pointer-events: none; z-index: 0; }
        .orb-one { width: 210px; height: 210px; top: 7%; left: 7%; background: linear-gradient(135deg, rgba(82, 42, 112, .20), rgba(82, 42, 112, .02)); }
        .orb-two { width: 280px; height: 280px; right: 4%; bottom: 4%; background: linear-gradient(135deg, rgba(196, 59, 72, .16), rgba(142, 37, 75, .02)); }
        .page { width: min(100%, 1020px); position: relative; z-index: 1; }
        .topline {
            display: flex; justify-content: center; align-items: center; gap: 9px;
            margin-bottom: 20px; color: #66566e; font-size: 11px; font-weight: 800;
            letter-spacing: .22em; text-transform: uppercase;
        }
        .topline::before, .topline::after { content: ""; width: 30px; height: 1px; background: linear-gradient(90deg, transparent, #a77caa); }
        .topline::after { transform: rotate(180deg); }
        .login-shell {
            display: grid; grid-template-columns: .92fr 1.08fr; min-height: 580px;
            border: 1px solid rgba(255,255,255,.8); border-radius: 30px; overflow: hidden;
            background: rgba(255,255,255,.35);
            box-shadow: 0 28px 90px rgba(63, 32, 70, .15), inset 0 1px 0 rgba(255,255,255,.8);
            backdrop-filter: blur(22px); -webkit-backdrop-filter: blur(22px);
        }
        .brand-panel {
            position: relative; isolation: isolate; display: flex; flex-direction: column;
            justify-content: space-between; padding: 42px 34px; color: white; overflow: hidden;
            background: linear-gradient(145deg, rgba(62, 34, 83, .97), rgba(101, 39, 81, .95) 58%, rgba(157, 43, 66, .95));
        }
        .brand-panel::before, .brand-panel::after { content: ""; position: absolute; border: 1px solid rgba(255,255,255,.19); border-radius: 50%; z-index: -1; }
        .brand-panel::before { width: 310px; height: 310px; right: -145px; top: -95px; }
        .brand-panel::after { width: 240px; height: 240px; left: -135px; bottom: -90px; }
        .brand-kicker { font-size: 10px; letter-spacing: .24em; text-transform: uppercase; color: rgba(255,255,255,.72); }
        .logo-glass {
            margin: 38px auto 28px; width: min(100%, 265px); padding: 20px 20px 16px;
            display: grid; place-items: center; border-radius: 23px;
            background: rgba(255,255,255,.91); border: 1px solid rgba(255,255,255,.85);
            box-shadow: 0 16px 40px rgba(19, 7, 30, .18), inset 0 1px 0 #fff;
        }
        .logo-glass img { display: block; width: 100%; max-width: 220px; height: auto; object-fit: contain; }
        .brand-copy { text-align: center; }
        .brand-copy h1 { margin: 0 0 10px; font-size: clamp(23px, 3vw, 31px); letter-spacing: .015em; font-weight: 700; }
        .brand-copy p { max-width: 290px; margin: 0 auto; color: rgba(255,255,255,.76); font-size: 13px; line-height: 1.8; }
        .brand-foot { display: flex; align-items: center; justify-content: center; gap: 8px; color: rgba(255,255,255,.68); font-size: 11px; }
        .brand-foot span { width: 6px; height: 6px; border-radius: 50%; background: #f1b8bd; box-shadow: 0 0 12px #f1b8bd; }
        .form-panel { display: flex; align-items: center; justify-content: center; padding: 48px clamp(24px, 5vw, 62px); }
        .form-content { width: 100%; max-width: 365px; }
        .eyebrow { color: var(--maroon); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .2em; }
        .form-content h2 { margin: 12px 0 9px; color: #281d30; font-size: clamp(29px, 3.5vw, 36px); letter-spacing: -.045em; }
        .intro { margin: 0 0 30px; color: var(--muted); font-size: 13px; line-height: 1.7; }
        .alert { margin: 0 0 18px; padding: 12px 14px; border-radius: 12px; font-size: 12px; line-height: 1.5; color: #8a243e; border: 1px solid rgba(196,59,72,.2); background: rgba(255,238,241,.9); }
        label { display: block; margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #413449; }
        .field { margin-bottom: 19px; }
        .input-wrap {
            display: flex; align-items: center; gap: 11px; min-height: 51px; padding: 0 14px;
            border: 1px solid rgba(112, 78, 119, .17); border-radius: 13px;
            background: rgba(255,255,255,.62); box-shadow: inset 0 1px 3px rgba(57, 29, 65, .025);
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .input-wrap:focus-within { border-color: rgba(142,37,75,.62); background: rgba(255,255,255,.94); box-shadow: 0 0 0 4px rgba(142,37,75,.08); }
        .field-icon { width: 17px; height: 17px; flex: 0 0 auto; color: #8c6d91; }
        .input-wrap input { width: 100%; min-width: 0; border: 0; outline: 0; background: transparent; color: #2d2135; font: inherit; font-size: 13px; padding: 15px 0; }
        .input-wrap input::placeholder { color: #a99eac; }
        .toggle-password { border: 0; background: transparent; color: #80516f; cursor: pointer; font-size: 11px; font-weight: 700; padding: 8px 0 8px 5px; }
        .submit-btn {
            width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 10px;
            border: 0; border-radius: 13px; padding: 16px 18px; color: white; cursor: pointer;
            font: inherit; font-size: 13px; font-weight: 800; letter-spacing: .025em;
            background: linear-gradient(105deg, #4b2862, #762d62 56%, #b33b50);
            box-shadow: 0 11px 22px rgba(111, 42, 86, .22);
            transition: transform .2s, box-shadow .2s, filter .2s;
        }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 15px 26px rgba(111,42,86,.28); filter: saturate(1.08); }
        .submit-btn:active { transform: translateY(0); }
        .secure-note { display: flex; justify-content: center; align-items: center; gap: 7px; margin-top: 24px; color: #958a99; font-size: 10px; }
        .secure-note svg { color: #7b527e; }
        .copyright { margin-top: 22px; text-align: center; color: #9c909f; font-size: 10px; }
        @media (max-width: 760px) {
            body { padding: 20px 13px; }
            .topline { margin-bottom: 14px; }
            .login-shell { grid-template-columns: 1fr; max-width: 470px; margin: auto; border-radius: 25px; }
            .brand-panel { padding: 25px 24px 21px; }
            .brand-kicker { text-align: center; }
            .logo-glass { width: 180px; margin: 20px auto 17px; padding: 12px; border-radius: 17px; }
            .logo-glass img { max-width: 155px; }
            .brand-copy h1 { font-size: 22px; }
            .brand-copy p { font-size: 12px; }
            .brand-foot { margin-top: 24px; }
            .form-panel { padding: 32px 25px 35px; }
            .form-content h2 { font-size: 30px; }
            .intro { margin-bottom: 23px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition: none !important; scroll-behavior: auto !important; }
        }
    </style>
</head>
<body>
    <div class="orb orb-one" aria-hidden="true"></div>
    <div class="orb orb-two" aria-hidden="true"></div>
    <main class="page">
        <div class="topline">Welcome back</div>
        <section class="login-shell" aria-label="Suruchi Creation login">
            <aside class="brand-panel">
                <div class="brand-kicker">A little elegance in every creation</div>
                <div>
                    <div class="logo-glass">
                        <img src="../images/logo.jpg" alt="Suruchi Creation logo">
                    </div>
                    <div class="brand-copy">
                        <h1>Suruchi Creation</h1>
                        <p>Beautifully crafted. Thoughtfully created. Welcome to your workspace.</p>
                    </div>
                </div>
                <div class="brand-foot"><span></span> Elegance • Creativity • Quality</div>
            </aside>

            <section class="form-panel">
                <div class="form-content">
                    <div class="eyebrow">Your workspace</div>
                    <h2>Sign in</h2>
                    <p class="intro">Enter your account details to continue to your dashboard.</p>

                    <?php if ($error !== ''): ?>
                        <div class="alert" role="alert"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form method="post" action="login.php" autocomplete="on">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <div class="field">
                            <label for="email">Email address</label>
                            <div class="input-wrap">
                                <svg class="field-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3" stroke="currentColor" stroke-width="1.7"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <input id="email" name="email" type="email" placeholder="Enter your email" value="<?= e($email) ?>" autocomplete="username" required>
                            </div>
                        </div>

                        <div class="field">
                            <label for="password">Password</label>
                            <div class="input-wrap">
                                <svg class="field-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
                                <button class="toggle-password" type="button" id="togglePassword" aria-label="Show password">Show</button>
                            </div>
                        </div>

                        <button class="submit-btn" type="submit">
                            Sign in securely
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </form>
                    <div class="secure-note">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 5 6v5c0 4.5 2.9 8 7 10 4.1-2 7-5.5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Your sign-in details are protected
                    </div>
                    <div class="copyright">© <?= date('Y') ?> Suruchi Creation. All rights reserved.</div>
                </div>
            </section>
        </section>
    </main>
    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        togglePassword.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            togglePassword.textContent = isHidden ? 'Hide' : 'Show';
            togglePassword.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        });
    </script>
</body>
</html>