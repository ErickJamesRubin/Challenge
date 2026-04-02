<?php
// ============================================================
// config.php — Database connection settings
// Place this file in: C:\xampp\htdocs\quiz_system\api\
// ============================================================

define('DB_HOST',     'localhost');
define('DB_USER',     'root');        // Default XAMPP user
define('DB_PASS',     '');            // Default XAMPP password (empty)
define('DB_NAME',     'bsit_quiz');
define('DB_PORT',     3306);
define('DB_CHARSET',  'utf8mb4');

// CORS — allow the quiz HTML to call this API from the same localhost
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ── Create PDO connection ─────────────────────────────────────
function getDB(): PDO {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error'   => 'Database connection failed: ' . $e->getMessage()
        ]);
        exit();
    }
}

// ── Helper: send JSON response ────────────────────────────────
function respond(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}