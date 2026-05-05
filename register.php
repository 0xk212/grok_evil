<?php
require_once 'db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, plan) VALUES (?, ?, 'user', 'free')");
            $stmt->execute([$username, $hashed]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grok | New Account Enrollment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @font-face {
            font-family: 'universalSans';
            src: url('https://cdn.grok.com/_next/static/media/UniversalSans_Text_400.p.8e69d71d.woff2') format('woff2');
            font-weight: 400; font-style: normal;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family: 'universalSans', -apple-system, sans-serif; }
        body { background: #000; color: #fff; height: 100vh; display: flex; overflow: hidden; }

        .login-layout { flex: 1; display: flex; }

        /* Form Side */
        .auth-side {
            flex: 1; display: flex; flex-direction: column; justify-content: center;
            align-items: center; padding: 40px; background: #000;
        }
        .auth-container { width: 100%; max-width: 380px; }
        
        .x-logo { font-size: 1.5rem; margin-bottom: 80px; align-self: flex-start; }
        .h1-title { font-size: 2.2rem; font-weight: 600; margin-bottom: 40px; letter-spacing: -0.5px; }

        .login-form input {
            width: 100%; background: #000; border: 1px solid #333; padding: 14px 16px;
            border-radius: 8px; color: #fff; font-size: 1rem; margin-bottom: 20px; outline: none;
        }
        .login-form input:focus { border-color: #e11d48; }

        .btn-submit {
            width: 100%; padding: 14px; background: #fff; color: #000; border: none;
            border-radius: 30px; font-weight: bold; cursor: pointer; font-size: 1rem;
        }
        
        .footer-links { margin-top: 30px; font-size: 0.85rem; color: #777; text-align: left; }
        .footer-links a { color: #fff; text-decoration: none; font-weight: 600; }

        /* Art Side */
        .art-side {
            flex: 1.2; background: linear-gradient(135deg, #0a0a0a 0%, #000 100%);
            display: flex; align-items: center; justify-content: center; position: relative;
            border-left: 1px solid rgba(255,255,255,0.05);
        }
        .large-grok-icon { width: 450px; height: 450px; opacity: 0.15; filter: blur(2px); }

        .error-msg { background: rgba(225, 29, 72, 0.1); color: #e11d48; padding: 12px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 20px; text-align: center; }

        @media (max-width: 900px) {
            .art-side { display: none; }
        }
    </style>
</head>
<body>
    <div class="login-layout">
        <section class="auth-side">
            <div class="auth-container">
                <div class="x-logo"><i class="fa-solid fa-xmark"></i></div>
                <h1 class="h1-title">Enroll for an account</h1>

                <?php if ($error): ?>
                    <div class="error-msg"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="login-form">
                    <input type="text" name="username" placeholder="Choose a Username" required autocomplete="off">
                    <input type="password" name="password" placeholder="Create a Password" required>
                    <button type="submit" class="btn-submit">Sign up</button>
                </form>

                <div class="footer-links">
                    Already have an account? <a href="login.php">Log in</a>
                </div>
            </div>
        </section>

        <section class="art-side">
            <svg class="large-grok-icon" viewBox="0 0 24 24" fill="white">
                <circle cx="12" cy="12" r="11" stroke="white" stroke-width="0.5" fill="none"></circle>
                <path d="M6 18L18 6" stroke="white" stroke-width="0.8" stroke-linecap="butt"></path>
            </svg>
        </section>
    </div>
</body>
</html>
