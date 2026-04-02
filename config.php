<?php
// ============================================================
// config.php — Database connection (PHP 5/7/8 compatible)
// ============================================================

error_reporting(E_ALL);
ini_set('display_errors', 0);

define('DB_HOST',    'localhost');
define('DB_USER',    'root');
define('DB_PASS',    '');          // default XAMPP = empty password
define('DB_NAME',    'bsit_quiz');
define('DB_CHARSET', 'utf8mb4');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// No return type hint — works on PHP 5, 7, and 8
function getDB() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $options = array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    );
    try {
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array(
            'success' => false,
            'error'   => 'DB connection failed: ' . $e->getMessage()
        ));
        exit();
    }
}

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}