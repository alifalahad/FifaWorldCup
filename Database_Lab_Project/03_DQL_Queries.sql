-- =============================================================================
-- 03_DQL_Queries.sql
-- FIFA World Cup Manager - Sample Queries Used in the Application
-- =============================================================================

-- 1. Search Algorithm (Multi-word case-insensitive search for players)
-- Used in: Live Global Search & Admin Search
SELECT p.player_id, p.first_name, p.last_name, t.country_name
FROM players p
JOIN teams t ON p.team_id = t.team_id
WHERE UPPER(p.first_name || ' ' || p.last_name) LIKE UPPER('%Lionel Messi%')
   OR UPPER(p.first_name) LIKE UPPER('%Lionel%')
   OR UPPER(p.last_name) LIKE UPPER('%Messi%')
ORDER BY p.last_name, p.first_name;

-- 2. Dashboard Stats: Top 8 Scoring Teams All-Time
-- Used in: Admin Dashboard Chart (Top Scoring Teams)
SELECT t.abbreviation, COUNT(g.goal_id) AS goal_count
FROM goals g
JOIN teams t ON g.team_id = t.team_id
GROUP BY t.abbreviation
ORDER BY goal_count DESC
FETCH FIRST 8 ROWS ONLY;

-- 3. Dashboard Stats: Goals per Tournament
-- Used in: Admin Dashboard Chart (Goals per Tournament)
SELECT tr.name, tr.year, COUNT(g.goal_id) AS goal_count
FROM tournaments tr
LEFT JOIN matches m ON tr.tournament_id = m.tournament_id
LEFT JOIN goals g ON m.match_id = g.match_id
GROUP BY tr.name, tr.year
ORDER BY tr.year ASC;

-- 4. Dashboard Stats: Disciplinary Cards by Type per Tournament
-- Used in: Admin Dashboard Chart (Cards per Tournament)
SELECT tr.year, 
       c.card_type, 
       COUNT(c.card_id) AS card_count
FROM cards c
JOIN matches m ON c.match_id = m.match_id
JOIN tournaments tr ON m.tournament_id = tr.tournament_id
GROUP BY tr.year, c.card_type
ORDER BY tr.year ASC, c.card_type DESC;

-- 5. Live Match Scores Polling
-- Used in: Public Fixtures Page (AJAX Polling every 30s)
SELECT m.match_id, ht.country_name AS home_team, at.country_name AS away_team, 
       m.home_score, m.away_score, m.has_extra_time, m.has_penalties, m.status
FROM matches m
JOIN teams ht ON m.home_team_id = ht.team_id
JOIN teams at ON m.away_team_id = at.team_id
WHERE m.status = 'ONGOING'
  AND m.tournament_id = 1;

-- 6. Dynamic Group Registration (AJAX)
-- Used in: Admin Register Team page to show how many teams are in each group
SELECT tg.group_name, COUNT(tt.team_id) AS team_count
FROM tournament_groups tg
LEFT JOIN team_tournament tt ON tg.group_id = tt.group_id
WHERE tg.tournament_id = 1
GROUP BY tg.group_name
ORDER BY tg.group_name;
