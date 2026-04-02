<?php
// ============================================================
// get_leaderboard.php — Fetch top 10 leaderboard from MySQL
// Endpoint: GET /api/get_leaderboard.php
// Optional: GET /api/get_leaderboard.php?limit=20
// ============================================================

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respond(array('success' => false, 'error' => 'Method not allowed'), 405);
}

$limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 50) : 10;
if ($limit < 1) $limit = 10;

$db = getDB();

$stmt = $db->prepare(
    'SELECT
        id,
        nickname,
        score,
        time_taken,
        DATE_FORMAT(date_taken, "%Y-%m-%d %H:%i") AS date_taken
     FROM quiz_results
     ORDER BY score DESC, time_taken ASC
     LIMIT :lim'
);
$stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
$stmt->execute();

$rows = $stmt->fetchAll();

$ranked = array_map(function($row, $idx) {
    $row['rank'] = $idx + 1;
    return $row;
}, $rows, array_keys($rows));

$total = (int) $db->query('SELECT COUNT(*) FROM quiz_results')->fetchColumn();

respond(array(
    'success'  => true,
    'total'    => $total,
    'limit'    => $limit,
    'entries'  => $ranked,
));