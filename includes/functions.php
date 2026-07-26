<?php
require_once __DIR__ . '/../config.php';

const GAME_COLORS = ['g1', 'g2', 'g3', 'g4', 'g5'];

const GAME_STATUS_LABELS = [
    'available'   => ['en' => 'Available now',   'fr' => 'Disponible maintenant'],
    'development' => ['en' => 'In development',  'fr' => 'En développement'],
];

const JOB_LOCATION_LABELS = [
    'remote' => ['en' => 'Remote',  'fr' => 'Télétravail'],
    'hybrid' => ['en' => 'Hybrid',  'fr' => 'Hybride'],
    'onsite' => ['en' => 'On-site', 'fr' => 'Sur site'],
];

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path) {
    header("Location: $path");
    exit;
}

function flash(string $message, string $category = 'success'): void {
    $_SESSION['flashes'][] = ['message' => $message, 'category' => $category];
}

function get_flashes(): array {
    $flashes = $_SESSION['flashes'] ?? [];
    unset($_SESSION['flashes']);
    return $flashes;
}

function get_setting(string $key, ?string $default = null): ?string {
    $stmt = get_db()->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $value = $stmt->fetchColumn();
    return $value !== false ? $value : $default;
}

function set_setting(string $key, string $value): void {
    $stmt = get_db()->prepare(
        "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
    );
    $stmt->execute([$key, $value]);
}

function is_recruiting_open(): bool {
    return get_setting('recruiting_open', 'true') === 'true';
}

function csrf_field(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . e($_SESSION['csrf_token']) . '">';
}

function check_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(400);
        die('Invalid or expired form submission. Please go back and try again.');
    }
}
