<?php
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_admin_id(): ?int {
    return $_SESSION['admin_id'] ?? null;
}

function current_admin_username(): ?string {
    return $_SESSION['admin_username'] ?? null;
}

function require_login(): void {
    if (!current_admin_id()) {
        redirect('login.php');
    }
}

function attempt_login(string $username, string $password): bool {
    $stmt = get_db()->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }
    return false;
}

function logout(): void {
    $_SESSION = [];
    session_destroy();
}
