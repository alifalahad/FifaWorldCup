-- =============================================================================
-- 05_PLSQL.sql
-- FIFA World Cup Manager - Triggers, Functions, and Procedures (Oracle PL/SQL)
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. TRIGGER: Auto-Update Match Score
-- -----------------------------------------------------------------------------
-- Whenever a new goal is inserted into the GOALS table, this trigger will
-- automatically update the home_score or away_score in the MATCHES table.
CREATE OR REPLACE TRIGGER trg_update_match_score
AFTER INSERT ON goals
FOR EACH ROW
DECLARE
    v_home_team_id NUMBER;
    v_away_team_id NUMBER;
BEGIN
    -- Find which teams are playing in this match
    SELECT home_team_id, away_team_id
    INTO v_home_team_id, v_away_team_id
    FROM matches
    WHERE match_id = :NEW.match_id;

    -- Update the score depending on which team scored
    IF :NEW.team_id = v_home_team_id THEN
        UPDATE matches
        SET home_score = home_score + 1
        WHERE match_id = :NEW.match_id;
    ELSIF :NEW.team_id = v_away_team_id THEN
        UPDATE matches
        SET away_score = away_score + 1
        WHERE match_id = :NEW.match_id;
    END IF;
END;
/

-- -----------------------------------------------------------------------------
-- 2. FUNCTION: Calculate Player Age
-- -----------------------------------------------------------------------------
-- A reusable PL/SQL function that calculates a player's exact age based
-- on their date_of_birth.
CREATE OR REPLACE FUNCTION fn_get_player_age(p_player_id NUMBER)
RETURN NUMBER
IS
    v_dob DATE;
    v_age NUMBER;
BEGIN
    SELECT date_of_birth INTO v_dob
    FROM players
    WHERE player_id = p_player_id;
    
    -- Calculate age using months between today and DOB, divided by 12
    v_age := TRUNC(MONTHS_BETWEEN(SYSDATE, v_dob) / 12);
    
    RETURN v_age;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RETURN NULL;
END;
/

-- Example Usage of the Function:
-- SELECT first_name, last_name, fn_get_player_age(player_id) AS age FROM players;


-- -----------------------------------------------------------------------------
-- 3. STORED PROCEDURE: Register Player to Tournament
-- -----------------------------------------------------------------------------
-- A procedure that registers a player to a tournament squad and ensures
-- they aren't assigned a jersey number already taken by their teammate.
CREATE OR REPLACE PROCEDURE pr_register_player(
    p_player_id NUMBER,
    p_team_tournament_id NUMBER,
    p_jersey_number NUMBER
)
IS
    v_count NUMBER;
BEGIN
    -- Check if the jersey number is already taken in this squad
    SELECT COUNT(*)
    INTO v_count
    FROM player_tournament
    WHERE team_tournament_id = p_team_tournament_id
      AND jersey_number = p_jersey_number;
      
    IF v_count > 0 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Jersey number is already taken by another player in this squad.');
    ELSE
        -- Insert the player into the squad
        INSERT INTO player_tournament (player_id, team_tournament_id, jersey_number)
        VALUES (p_player_id, p_team_tournament_id, p_jersey_number);
        
        COMMIT;
    END IF;
END;
/

-- Example Usage of the Procedure:
-- EXECUTE pr_register_player(1, 1, 10);
