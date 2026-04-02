-- ============================================================
-- ICS / BSIT ARDUINO QUIZ SYSTEM — DATABASE SETUP
-- Run this in phpMyAdmin or MySQL command line
-- ============================================================

CREATE DATABASE IF NOT EXISTS bsit_quiz
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bsit_quiz;

-- ── TABLE: quiz_results ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS quiz_results (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  nickname    VARCHAR(20)     NOT NULL,
  score       TINYINT         NOT NULL DEFAULT 0,
  time_taken  SMALLINT        NOT NULL DEFAULT 0,
  date_taken  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip_address  VARCHAR(45)     DEFAULT NULL,
  PRIMARY KEY (id),
  INDEX idx_score_time (score DESC, time_taken ASC),
  INDEX idx_nickname (nickname),
  INDEX idx_date (date_taken)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── VIEW: leaderboard_top10 ───────────────────────────────────
-- Fixed: removed variable — ROW_NUMBER() works in MySQL 8+
-- If you are on MySQL 5.x (older XAMPP), use the fallback below
CREATE OR REPLACE VIEW leaderboard_top10 AS
  SELECT
    ROW_NUMBER() OVER (ORDER BY score DESC, time_taken ASC) AS `rank`,
    nickname,
    score,
    time_taken,
    date_taken
  FROM quiz_results
  ORDER BY score DESC, time_taken ASC
  LIMIT 10;

-- ── FALLBACK VIEW for MySQL 5.x (older XAMPP) ─────────────────
-- If you get an error about ROW_NUMBER(), delete the VIEW above
-- and run this one instead:
--
-- CREATE OR REPLACE VIEW leaderboard_top10 AS
--   SELECT
--     nickname,
--     score,
--     time_taken,
--     date_taken
--   FROM quiz_results
--   ORDER BY score DESC, time_taken ASC
--   LIMIT 10;

SELECT 'Database setup complete!' AS status;