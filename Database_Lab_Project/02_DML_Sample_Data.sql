-- =============================================================================
-- 02_DML_Sample_Data.sql
-- FIFA World Cup Manager - Sample Data
-- =============================================================================

-- Insert Tournaments
INSERT INTO tournaments (name, host_country, year, start_date, end_date, total_teams)
VALUES ('FIFA World Cup 2022', 'Qatar', 2022, TO_DATE('2022-11-20', 'YYYY-MM-DD'), TO_DATE('2022-12-18', 'YYYY-MM-DD'), 32);

INSERT INTO tournaments (name, host_country, year, start_date, end_date, total_teams)
VALUES ('FIFA World Cup 2018', 'Russia', 2018, TO_DATE('2018-06-14', 'YYYY-MM-DD'), TO_DATE('2018-07-15', 'YYYY-MM-DD'), 32);

-- Insert Teams
INSERT INTO teams (country_name, abbreviation, continent, fifa_ranking) VALUES ('Argentina', 'ARG', 'CONMEBOL', 1);
INSERT INTO teams (country_name, abbreviation, continent, fifa_ranking) VALUES ('France', 'FRA', 'UEFA', 2);
INSERT INTO teams (country_name, abbreviation, continent, fifa_ranking) VALUES ('Brazil', 'BRA', 'CONMEBOL', 3);
INSERT INTO teams (country_name, abbreviation, continent, fifa_ranking) VALUES ('England', 'ENG', 'UEFA', 4);
INSERT INTO teams (country_name, abbreviation, continent, fifa_ranking) VALUES ('Portugal', 'POR', 'UEFA', 9);

-- Insert Coaches
INSERT INTO coaches (first_name, last_name, nationality, date_of_birth) 
VALUES ('Lionel', 'Scaloni', 'Argentina', TO_DATE('1978-05-16', 'YYYY-MM-DD'));
INSERT INTO coaches (first_name, last_name, nationality, date_of_birth) 
VALUES ('Didier', 'Deschamps', 'France', TO_DATE('1968-10-15', 'YYYY-MM-DD'));

-- Insert Stadiums
INSERT INTO stadiums (name, city, capacity, build_year) VALUES ('Lusail Stadium', 'Lusail', 88966, 2021);
INSERT INTO stadiums (name, city, capacity, build_year) VALUES ('Al Bayt Stadium', 'Al Khor', 68895, 2021);

-- Insert Referees
INSERT INTO referees (first_name, last_name, nationality, experience_years) VALUES ('Szymon', 'Marciniak', 'Poland', 15);
INSERT INTO referees (first_name, last_name, nationality, experience_years) VALUES ('Daniele', 'Orsato', 'Italy', 20);

-- Insert Groups
INSERT INTO tournament_groups (tournament_id, group_name) VALUES (1, 'C');
INSERT INTO tournament_groups (tournament_id, group_name) VALUES (1, 'D');

-- Insert Team Tournament Registration
-- Assuming: ARG=1, FRA=2, Tourney2022=1, GroupC=1, GroupD=2
INSERT INTO team_tournament (team_id, tournament_id, group_id, coach_id, seed_position) VALUES (1, 1, 1, 1, 1);
INSERT INTO team_tournament (team_id, tournament_id, group_id, coach_id, seed_position) VALUES (2, 1, 2, 2, 2);

-- Insert Players
INSERT INTO players (first_name, last_name, date_of_birth, position, team_id, club_name)
VALUES ('Lionel', 'Messi', TO_DATE('1987-06-24', 'YYYY-MM-DD'), 'Forward', 1, 'Inter Miami');

INSERT INTO players (first_name, last_name, date_of_birth, position, team_id, club_name)
VALUES ('Kylian', 'Mbappe', TO_DATE('1998-12-20', 'YYYY-MM-DD'), 'Forward', 2, 'Real Madrid');

INSERT INTO players (first_name, last_name, date_of_birth, position, team_id, club_name)
VALUES ('Cristiano', 'Ronaldo', TO_DATE('1985-02-05', 'YYYY-MM-DD'), 'Forward', 5, 'Al Nassr');

-- Insert Matches
INSERT INTO matches (tournament_id, stadium_id, referee_id, home_team_id, away_team_id, match_date, stage, home_score, away_score, has_extra_time, has_penalties, status)
VALUES (1, 1, 1, 1, 2, TO_DATE('2022-12-18', 'YYYY-MM-DD'), 'FINAL', 3, 3, 'Y', 'Y', 'COMPLETED');

-- Insert Goals
INSERT INTO goals (match_id, scorer_player_id, team_id, goal_minute, goal_type, half)
VALUES (1, 1, 1, 23, 'PENALTY', 1);

INSERT INTO goals (match_id, scorer_player_id, team_id, goal_minute, goal_type, half)
VALUES (1, 2, 2, 80, 'PENALTY', 2);
