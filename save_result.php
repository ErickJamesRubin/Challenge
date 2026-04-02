<?php
// ============================================================
// save_result.php — Save a quiz attempt to MySQL
// Endpoint: POST /api/save_result.php
// Body (JSON): { "nickname": "ALPHA_01", "score": 4, "time_taken": 113 }
// ============================================================

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'error' => 'Method not allowed'], 405);
}

// ── Parse JSON body ───────────────────────────────────────────
$body = json_decode(file_get_contents('php://input'), true);

if (!$body || !isset($body['nickname'], $body['score'], $body['time_taken'])) {
    respond(['success' => false, 'error' => 'Missing required fields: nickname, score, time_taken'], 400);
}

// ── Sanitise & validate ───────────────────────────────────────
$nickname   = trim((string) $body['nickname']);
$score      = (int) $body['score'];
$time_taken = (int) $body['time_taken'];

if (strlen($nickname) < 2 || strlen($nickname) > 20) {
    respond(['success' => false, 'error' => 'Nickname must be 2–20 characters'], 400);
}
if ($score < 0 || $score > 5) {
    respond(['success' => false, 'error' => 'Score must be 0–5'], 400);
}
if ($time_taken < 0 || $time_taken > 86400) {
    respond(['success' => false, 'error' => 'Invalid time_taken value'], 400);
}

// Optional: capture IP for audit purposes
$ip = $_SERVER['REMOTE_ADDR'] ?? null;

// ── Insert into database ──────────────────────────────────────
$db = getDB();

$stmt = $db->prepare(
    'INSERT INTO quiz_results (nickname, score, time_taken, date_taken, ip_address)
     VALUES (:nickname, :score, :time_taken, NOW(), :ip)'
);

$stmt->execute([
    ':nickname'   => strtoupper($nickname),
    ':score'      => $score,
    ':time_taken' => $time_taken,
    ':ip'         => $ip,
]);

$new_id = (int) $db->lastInsertId();

// ── Compute rank of this new entry ────────────────────────────
$rankStmt = $db->prepare(
    'SELECT COUNT(*) + 1 AS `rank`
     FROM quiz_results
     WHERE score > :score
        OR (score = :score AND time_taken < :time_taken)'
);
$rankStmt->execute([
    ':score'      => $score,
    ':time_taken' => $time_taken,
]);
$rank = (int) $rankStmt->fetchColumn();

respond([
    'success' => true,
    'id'      => $new_id,
    'rank'    => $rank,
    'message' => 'Result saved successfully',
]);