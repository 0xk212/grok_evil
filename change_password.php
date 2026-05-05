<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Security: CSRF Generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    if (!hash_equals($csrf, $token)) {
        die("<h1>PROTOCOL VIOLATION: INVALID CSRF TOKEN</h1>");
    }

    $current_pass = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch();

    if (!$user_data || !password_verify($current_pass, $user_data['password'])) {
        $error = 'Current access key is incorrect. Integrity check failed.';
    } elseif (strlen($new_pass) < 8) {
        $error = 'Insecure access key. Minimum 8 characters required.';
    } elseif ($new_pass !== $confirm_pass) {
        $error = 'Keys do not match. Integrity check failed.';
    } else {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?");
        $stmt->execute([$hashed, $_SESSION['user_id']]);
        $success = 'Security protocols updated. Redirecting...';
        header('Refresh: 2; URL=index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeepSeek Evil | Security Update</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { justify-content: center; align-items: center; background: #0d0d0d; }
        .auth-card {
            background: var(--ds-bg-surface);
            border: 1px solid var(--ds-accent-evil);
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.8rem; color: var(--ds-text-secondary); }
        .form-group input {
            width: 100%;
            padding: 12px;
            background: #000;
            border: 1px solid var(--ds-border-color);
            border-radius: 8px;
            color: #fff;
            outline: none;
        }
        .btn-auth {
            width: 100%;
            padding: 14px;
            background: var(--ds-accent-evil);
            border: none;
            border-radius: 8px;
            color: #000;
            font-weight: 700;
            cursor: pointer;
        }
        .error-box { color: var(--ds-accent-evil); margin-bottom: 16px; font-size: 0.8rem; text-align: center; }
        .success-box { color: #00ff00; margin-bottom: 16px; font-size: 0.8rem; text-align: center; }
    </style>
</head>
<body>
    <div class="scanlines"></div>
    <div class="auth-card">
        <h2 class="glitch" data-text="SECURITY OVERRIDE">SECURITY OVERRIDE</h2>
        <p style="font-size: 0.75rem; color: var(--ds-text-tertiary); margin-bottom: 20px; text-align: center;">
            You are using a temporary access key. Update required to proceed.
        </p>
        
        <?php if ($error): ?>
            <div class="error-box"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-box"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="token" value="<?php echo $csrf; ?>">
            <div class="form-group">
                <label>CURRENT ACCESS KEY</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label>NEW ACCESS KEY</label>
                <input type="password" name="new_password" required minlength="8">
            </div>
            <div class="form-group">
                <label>CONFIRM KEY</label>
                <input type="password" name="confirm_password" required minlength="8">
            </div>
            <button type="submit" class="btn-auth">UPDATE PROTOCOLS</button>
        </form>
    </div>
</body>
</html>
