-- ============================================================
-- ICS / BSIT ARDUINO QUIZ SYSTEM — DATABASE SETUP
-- Run this in phpMyAdmin or MySQL command line
-- ============================================================

CREATE DATABASE IF NOT EXISTS bsit_quiz
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bsit_quiz;

-- ── TABLE: quiz_results ──────────────────────────────────────
-- Stores every quiz attempt permanently
CREATE TABLE IF NOT EXISTS quiz_results (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  nickname    VARCHAR(20)     NOT NULL,
  score       TINYINT         NOT NULL DEFAULT 0,   -- 0 to 5
  time_taken  SMALLINT        NOT NULL DEFAULT 0,   -- total seconds
  date_taken  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip_address  VARCHAR(45)     DEFAULT NULL,          -- optional logging
  PRIMARY KEY (id),
  INDEX idx_score_time (score DESC, time_taken ASC),
  INDEX idx_nickname (nickname),
  INDEX idx_date (date_taken)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── VIEW: leaderboard_top10 ───────────────────────────────────
-- Ranked view: highest score first, fastest time as tiebreaker
CREATE OR REPLACE VIEW leaderboard_top10 AS
  SELECT
    (@row_num := @row_num + 1) AS `rank`,
    nickname,
    score,
    time_taken,
    date_taken
  FROM quiz_results,
       (SELECT @row_num := 0) AS init
  ORDER BY score DESC, time_taken ASC
  LIMIT 10;

-- ── SAMPLE DATA (optional — remove if you want a clean start) ─
-- INSERT INTO quiz_results (nickname, score, time_taken) VALUES
--   ('SAMPLE_01', 5, 95),
--   ('SAMPLE_02', 4, 112),
--   ('SAMPLE_03', 3, 200);

SELECT 'Database setup complete!' AS status;