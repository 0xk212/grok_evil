<?php
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header('HTTP/1.1 403 Forbidden');
    die('<h1>PROTOCOL VIOLATION: DIRECT ACCESS FORBIDDEN</h1>');
}
require_once 'config.php';

function audit_log($action, $user_id = 0) {
    if (!is_dir(__DIR__ . '/secure_data')) mkdir(__DIR__ . '/secure_data', 0755, true);
    $log_file = __DIR__ . '/secure_data/audit.log';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] [IP: $ip] [USER ID: $user_id] ACTION: $action" . PHP_EOL;
    @file_put_contents($log_file, $entry, FILE_APPEND);
}

function repair_missing_conversation(PDO $pdo, int $user_id, int $conversation_id, string $title = 'Protocol Inquiry'): bool {
    if ($conversation_id <= 0 || $user_id <= 0) {
        return false;
    }

    $stmt = $pdo->prepare("SELECT id FROM conversations WHERE id = ? AND user_id = ?");
    $stmt->execute([$conversation_id, $user_id]);
    if ($stmt->fetch()) {
        return true;
    }

    $safe_title = trim($title);
    if ($safe_title === '') {
        $safe_title = 'Protocol Inquiry';
    }

    $stmt = $pdo->prepare("INSERT OR IGNORE INTO conversations (id, user_id, title) VALUES (?, ?, ?)");
    $stmt->execute([$conversation_id, $user_id, $safe_title]);

    return $stmt->rowCount() > 0;
}

try {
    $pdo = new PDO("sqlite:" . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'user',
        plan TEXT DEFAULT 'free',
        must_change_password INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create login_attempts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
        ip_address TEXT NOT NULL,
        attempts INTEGER DEFAULT 1,
        last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (ip_address)
    )");

    // Create conversations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS conversations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Create chats table
    $pdo->exec("CREATE TABLE IF NOT EXISTS chats (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        conversation_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        role TEXT NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    try {
        $pdo->exec("ALTER TABLE chats ADD COLUMN cost INTEGER DEFAULT 1");
    } catch (PDOException $e) {
        // Ignored. Column exists.
    }

    // Optional admin bootstrap for first deployment only.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    if ((int) $stmt->fetchColumn() === 0 && BOOTSTRAP_ADMIN_USERNAME !== '' && BOOTSTRAP_ADMIN_PASSWORD !== '') {
        $admin_pass = password_hash(BOOTSTRAP_ADMIN_PASSWORD, PASSWORD_DEFAULT);
        
        // Check if the username already exists as a regular user
        $stmt_check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->execute([BOOTSTRAP_ADMIN_USERNAME]);
        if ($stmt_check->fetch()) {
            // Upgrade existing user to Admin
            $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin', plan = 'pro', must_change_password = 1 WHERE username = ?");
            $stmt->execute([$admin_pass, BOOTSTRAP_ADMIN_USERNAME]);
            audit_log("SYSTEM UPGRADE: User " . BOOTSTRAP_ADMIN_USERNAME . " was promoted to first admin.");
        } else {
            // Create brand new admin account
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, plan, must_change_password) VALUES (?, ?, 'admin', 'pro', 1)");
            $stmt->execute([BOOTSTRAP_ADMIN_USERNAME, $admin_pass]);
            audit_log("INITIAL SYSTEM INSTALLATION: Bootstrap admin created from environment.");
        }
    }

} catch (PDOException $e) {
    audit_log("DATABASE CONNECTION FAILURE: " . $e->getMessage());
    die("CRITICAL FAILURE: Database bridge compromised.");
}
?>
