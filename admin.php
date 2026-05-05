<?php
require_once 'config.php';
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $token = $_POST['token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }
    
    $target_id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'];

    if ($action === 'upgrade' && $target_id) {
        $pdo->prepare("UPDATE users SET plan = 'pro' WHERE id = ?")->execute([$target_id]);
    } elseif ($action === 'downgrade' && $target_id) {
        $pdo->prepare("UPDATE users SET plan = 'free' WHERE id = ?")->execute([$target_id]);
    } elseif ($action === 'delete' && $target_id && $target_id != $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$target_id]);
    }
    header('Location: admin.php');
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grok Evil | Admin Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family: -apple-system, system-ui, sans-serif; }
        body { background: #000; color: #fff; padding: 40px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; border-bottom: 1px solid #222; padding-bottom: 20px; }
        .logo { display:flex; align-items:center; gap:12px; font-size:1.5rem; font-weight:bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #080808; border-radius: 12px; overflow: hidden; }
        th, td { padding: 18px; text-align: left; border-bottom: 1px solid #1a1a1a; }
        th { background: #111; color: #888; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
        
        .badge { padding: 4px 10px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; }
        .badge-pro { background: #e11d48; color: #fff; }
        .badge-free { background: #333; color: #ccc; }
        .badge-admin { background: #fff; color: #000; }

        .btn { padding: 8px 16px; border-radius: 6px; border: none; font-size: 0.8rem; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-upgrade { background: #fff; color: #000; }
        .btn-downgrade { background: transparent; border: 1px solid #333; color: #fff; }
        .btn-delete { background: #1a1a1a; color: #e11d48; border: 1px solid #e11d4833; }
        .btn-delete:hover { background: #e11d48; color: #fff; }

        .back-link { color: #888; text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Core Terminal</a>
        <div class="header">
            <div class="logo">
                <svg viewBox="0 0 24 24" width="32" height="32" fill="white"><circle cx="12" cy="12" r="11" stroke="white" stroke-width="2" fill="none"></circle><path d="M7 17L17 7" stroke="white" stroke-width="2"></path></svg>
                Grok Evil Administration
            </div>
            <div style="color:#888; font-size:0.9rem">Authorized Access Only</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>ID</th>
                    <th>Plan</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                    <td><code style="color:#666">#<?php echo $u['id']; ?></code></td>
                    <td><span class="badge badge-<?php echo $u['plan']; ?>"><?php echo strtoupper($u['plan']); ?></span></td>
                    <td><span class="badge <?php echo $u['role'] === 'admin' ? 'badge-admin' : ''; ?>"><?php echo strtoupper($u['role']); ?></span></td>
                    <td style="color:#666; font-size:0.85rem"><?php echo date('Y-m-d', strtotime($u['created_at'])); ?></td>
                    <td>
                        <form method="POST" style="display:inline-block">
                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                            <input type="hidden" name="token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <?php if($u['plan'] === 'free'): ?>
                                <button type="submit" name="action" value="upgrade" class="btn btn-upgrade">PRO</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="downgrade" class="btn btn-downgrade">RESTRICT</button>
                            <?php endif; ?>
                        </form>
                        <?php if($u['id'] != $_SESSION['user_id']): ?>
                        <form method="POST" style="display:inline-block">
                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                            <input type="hidden" name="token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit" name="action" value="delete" class="btn btn-delete">DELETE</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
