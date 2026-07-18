-- =============================================================================
-- 04_Views.sql
-- FIFA World Cup Manager - Views
-- =============================================================================

-- Group Standings View
-- Computes the live standings for the group stage automatically from matches table.
CREATE OR REPLACE VIEW group_standings AS
WITH match_results AS (
    SELECT tournament_id, group_id, home_team_id AS team_id,
           home_score AS goals_for, away_score AS goals_against
    FROM matches
    WHERE stage = 'GROUP' AND status = 'COMPLETED'
    
    UNION ALL
    
    SELECT tournament_id, group_id, away_team_id AS team_id,
           away_score AS goals_for, home_score AS goals_against
    FROM matches
    WHERE stage = 'GROUP' AND status = 'COMPLETED'
)
SELECT
    group_id,
    tournament_id,
    team_id,
    COUNT(*)                                                            AS played,
    SUM(CASE WHEN goals_for > goals_against THEN 1 ELSE 0 END)          AS won,
    SUM(CASE WHEN goals_for = goals_against THEN 1 ELSE 0 END)          AS drawn,
    SUM(CASE WHEN goals_for < goals_against THEN 1 ELSE 0 END)          AS lost,
    SUM(goals_for)                                                      AS goals_for,
    SUM(goals_against)                                                  AS goals_against,
    SUM(goals_for) - SUM(goals_against)                                 AS goal_difference,
    SUM(CASE WHEN goals_for > goals_against THEN 3
             WHEN goals_for = goals_against THEN 1 ELSE 0 END)          AS points
FROM match_results
GROUP BY group_id, tournament_id, team_id;
