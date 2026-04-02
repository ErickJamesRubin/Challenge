<?php
// ============================================================
// get_leaderboard.php — Fetch top 10 leaderboard from MySQL
// Endpoint: GET /api/get_leaderboard.php
// Optional: GET /api/get_leaderboard.php?limit=20
// ============================================================

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respond(['success' => false, 'error' => 'Method not allowed'], 405);
}

$limit = min((int)($_GET['limit'] ?? 10), 50); // max 50
if ($limit < 1) $limit = 10;

$db = getDB();

// ── Fetch top entries ranked by score DESC, then time ASC ─────
$stmt = $db->prepare(
    'SELECT
        id,
        nickname,
        score,
        time_taken,
        DATE_FORMAT(date_taken, "%Y-%m-%d %H:%i") AS date_taken
     FROM quiz_results
     ORDER BY score DESC, time_taken ASC
     LIMIT :limit'
);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

$rows = $stmt->fetchAll();

// Add rank numbers server-side
$ranked = array_map(function($row, $idx) {
    $row['rank'] = $idx + 1;
    return $row;
}, $rows, array_keys($rows));

// ── Also return total record count ────────────────────────────
$total = (int) $db->query('SELECT COUNT(*) FROM quiz_results')->fetchColumn();

respond([
    'success'  => true,
    'total'    => $total,
    'limit'    => $limit,
    'entries'  => $ranked,
]);