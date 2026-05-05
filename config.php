<?php
// Security: Disable error display in production
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/secure_data/php_errors.log');

$env_path = __DIR__ . '/.env';
if (is_readable($env_path)) {
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || !strpos($line, '=')) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

function env_value($name, $default = '')
{
    $value = getenv($name);
    if ($value !== false && $value !== '') {
        return $value;
    }

    if (isset($_ENV[$name]) && $_ENV[$name] !== '') {
        return $_ENV[$name];
    }

    if (isset($_SERVER[$name]) && $_SERVER[$name] !== '') {
        return $_SERVER[$name];
    }

    return $default;
}

define('API_KEY', env_value('OPENROUTER_API_KEY', ''));
define('DB_PATH', __DIR__ . '/secure_data/database.sqlite');
define('BOOTSTRAP_ADMIN_USERNAME', env_value('BOOTSTRAP_ADMIN_USERNAME', ''));
define('BOOTSTRAP_ADMIN_PASSWORD', env_value('BOOTSTRAP_ADMIN_PASSWORD', ''));

// Session Security Configuration
$is_https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $is_https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

?>
