<?php
/**
 * Database connection settings.
 *
 * Fill in DB_NAME with your actual database name from the InfinityFree
 * control panel (Control Panel > MySQL Databases). It will look like
 * if0_42427264_something — "if0_42427264" alone is NOT a valid database
 * name, it's just your account prefix.
 */

if (!defined('DB_HOST')) define('DB_HOST', 'sql110.infinityfree.com');
if (!defined('DB_USER')) define('DB_USER', 'if0_42427264');
if (!defined('DB_PASS')) define('DB_PASS', 'Bstudio2026');
if (!defined('DB_NAME')) define('DB_NAME', 'if0_42427264_bstudio2'); // <-- replace XXX with your real database name
if (!defined('DB_PORT')) define('DB_PORT', 3306);

/**
 * Change this to something long and random before putting the site online.
 * It's only used to sign PHP session cookies.
 */
if (!defined('SESSION_SECRET')) define('SESSION_SECRET', 'change-this-to-a-long-random-string');

function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}
