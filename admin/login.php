<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/../includes/db.php';   // db() + e() yahin se aate hain

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
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
                header('Location: dashboard.php');
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
    <link rel="stylesheet" href="../assets/css/login.css">
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
                        <img src="../assets/images/logo.jpg" alt="Suruchi Creation logo">
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
    <script src="../assets/js/login.js" defer></script>
</body>
</html>