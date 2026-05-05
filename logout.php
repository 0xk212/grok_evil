<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['token'] ?? '')) {
    header('HTTP/1.1 403 Forbidden');
    die("<h1>PROTOCOL VIOLATION: INVALID CSRF TOKEN ON LOGOUT</h1>");
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();
header('Location: login.php');
exit;
?>
