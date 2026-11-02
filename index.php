<?php require 'config.php';
if (!empty($_SESSION['u'])) {
    header('Location: dashboard.php');
    exit;
}
$err = '';
if ($_POST) {
    $s = $pdo->prepare("SELECT * FROM staff WHERE email=? AND active=1");
    $s->execute([trim($_POST['email'])]);
    $u = $s->fetch();
    if ($u && password_verify($_POST['password'], $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['u'] = ['id' => $u['id'],'name' => $u['name'],'role' => $u['role']];
        header('Location: dashboard.php');
        exit;
    }
    $err = 'Wrong email or password, or the account is deactivated.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login | Visitor Pass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css?v=6">
</head>
<body class="login">
    <form method="post" class="lf card">
        <div class="lf-head">
            <span class="mark">VP</span>
            <h1>Smart Visitor Pass</h1>
        </div>
        <p class="muted">Sign in to the gate system</p>
        <?php if ($err) { ?>
            <div class="msg bad"><?=h($err)?></div>
        <?php } ?>
        <label>
            Email
            <input type="email" name="email" placeholder="you@company.com" required autofocus>
        </label>
        <!-- START CR: JF/ICT/24/26 - Login password visibility toggle -->
        <label>
            Password
            <div class="password-field">
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                <button type="button" class="password-eye" id="togglePassword" aria-label="Show password" aria-pressed="false" data-visible="false" title="Show password">
                    <svg class="eye-open" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="eye-closed" viewBox="0 0 24 24" aria-hidden="true" hidden><path d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.8 10.8 0 0112 5c6.4 0 10 7 10 7a17 17 0 01-3.2 4.1M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10.7 10.7 0 004.1-.8"/></svg>
                </button>
            </div>
        </label>
        <!-- END CR: JF/ICT/24/26 - Login password visibility toggle -->
        <button>Sign in</button>
    </form>
    <!-- START CR: JF/ICT/24/26 - Password toggle behaviour -->
    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const eyeOpen = togglePassword.querySelector('.eye-open');
        const eyeClosed = togglePassword.querySelector('.eye-closed');
        // Always start with the password masked, including after page reload.
        passwordInput.type = 'password';
        togglePassword.setAttribute('aria-pressed', 'false');
        togglePassword.dataset.visible = 'false';
        eyeOpen.hidden = false;
        eyeClosed.hidden = true;
        togglePassword.addEventListener('click', () => {
            const showPassword = passwordInput.type === 'password';
            passwordInput.type = showPassword ? 'text' : 'password';
            togglePassword.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            togglePassword.setAttribute('aria-pressed', showPassword ? 'true' : 'false');
            togglePassword.dataset.visible = showPassword ? 'true' : 'false';
            togglePassword.title = showPassword ? 'Hide password' : 'Show password';
            eyeOpen.hidden = showPassword;
            eyeClosed.hidden = !showPassword;
        });
    </script>
    <!-- END CR: JF/ICT/24/26 - Password toggle behaviour -->
</body>
</html>
