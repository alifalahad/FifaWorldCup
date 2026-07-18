-- =============================================================================
-- 01_DDL_Schema.sql
-- FIFA World Cup Manager - Database Definition (Oracle Syntax)
-- =============================================================================

-- 1. TOURNAMENTS
CREATE TABLE tournaments (
    tournament_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR2(150) NOT NULL UNIQUE,
    host_country VARCHAR2(100) NOT NULL,
    year NUMBER(4) NOT NULL UNIQUE,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_teams NUMBER(3) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TEAMS
CREATE TABLE teams (
    team_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    country_name VARCHAR2(100) NOT NULL UNIQUE,
    abbreviation CHAR(3) NOT NULL UNIQUE,
    continent VARCHAR2(50) NOT NULL,
    fifa_ranking NUMBER(5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_team_continent CHECK (continent IN ('AFC','CAF','CONCACAF','CONMEBOL','OFC','UEFA'))
);

-- 3. COACHES
CREATE TABLE coaches (
    coach_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    first_name VARCHAR2(100) NOT NULL,
    last_name VARCHAR2(100) NOT NULL,
    nationality VARCHAR2(100) NOT NULL,
    date_of_birth DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. PLAYERS
CREATE TABLE players (
    player_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    first_name VARCHAR2(100) NOT NULL,
    last_name VARCHAR2(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    position VARCHAR2(30) NOT NULL,
    team_id NUMBER NOT NULL,
    club_name VARCHAR2(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_players_team FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    CONSTRAINT chk_player_position CHECK (position IN ('Goalkeeper','Defender','Midfielder','Forward'))
);

-- 5. STADIUMS
CREATE TABLE stadiums (
    stadium_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR2(150) NOT NULL,
    city VARCHAR2(100) NOT NULL,
    capacity NUMBER(10) NOT NULL,
    build_year NUMBER(4),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. REFEREES
CREATE TABLE referees (
    referee_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    first_name VARCHAR2(100) NOT NULL,
    last_name VARCHAR2(100) NOT NULL,
    nationality VARCHAR2(100) NOT NULL,
    experience_years NUMBER(3),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. TOURNAMENT GROUPS
CREATE TABLE tournament_groups (
    group_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    tournament_id NUMBER NOT NULL,
    group_name VARCHAR2(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_groups_tournament FOREIGN KEY (tournament_id) REFERENCES tournaments(tournament_id) ON DELETE CASCADE,
    CONSTRAINT uq_tournament_group UNIQUE (tournament_id, group_name)
);

-- 8. TEAM TOURNAMENT (Roster/Registration)
CREATE TABLE team_tournament (
    team_tournament_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    team_id NUMBER NOT NULL,
    tournament_id NUMBER NOT NULL,
    group_id NUMBER,
    coach_id NUMBER,
    seed_position NUMBER(3),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tt_team FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    CONSTRAINT fk_tt_tournament FOREIGN KEY (tournament_id) REFERENCES tournaments(tournament_id) ON DELETE CASCADE,
    CONSTRAINT fk_tt_group FOREIGN KEY (group_id) REFERENCES tournament_groups(group_id) ON DELETE SET NULL,
    CONSTRAINT fk_tt_coach FOREIGN KEY (coach_id) REFERENCES coaches(coach_id) ON DELETE SET NULL,
    CONSTRAINT uq_team_tournament UNIQUE (team_id, tournament_id)
);

-- 9. PLAYER TOURNAMENT (Squads)
CREATE TABLE player_tournament (
    player_tournament_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    player_id NUMBER NOT NULL,
    team_tournament_id NUMBER NOT NULL,
    jersey_number NUMBER(3) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pt_player FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE,
    CONSTRAINT fk_pt_tt FOREIGN KEY (team_tournament_id) REFERENCES team_tournament(team_tournament_id) ON DELETE CASCADE,
    CONSTRAINT uq_pt_player_tt UNIQUE (player_id, team_tournament_id),
    CONSTRAINT uq_pt_jersey_tt UNIQUE (team_tournament_id, jersey_number)
);

-- 10. MATCHES
CREATE TABLE matches (
    match_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    tournament_id NUMBER NOT NULL,
    stadium_id NUMBER NOT NULL,
    referee_id NUMBER,
    home_team_id NUMBER NOT NULL,
    away_team_id NUMBER NOT NULL,
    group_id NUMBER,
    match_date DATE NOT NULL,
    stage VARCHAR2(50) NOT NULL,
    home_score NUMBER(3) DEFAULT 0 NOT NULL,
    away_score NUMBER(3) DEFAULT 0 NOT NULL,
    has_extra_time CHAR(1) DEFAULT 'N' NOT NULL,
    has_penalties CHAR(1) DEFAULT 'N' NOT NULL,
    status VARCHAR2(50) DEFAULT 'SCHEDULED' NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_match_tournament FOREIGN KEY (tournament_id) REFERENCES tournaments(tournament_id) ON DELETE CASCADE,
    CONSTRAINT fk_match_stadium FOREIGN KEY (stadium_id) REFERENCES stadiums(stadium_id) ON DELETE CASCADE,
    CONSTRAINT fk_match_referee FOREIGN KEY (referee_id) REFERENCES referees(referee_id) ON DELETE SET NULL,
    CONSTRAINT fk_match_home_team FOREIGN KEY (home_team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    CONSTRAINT fk_match_away_team FOREIGN KEY (away_team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    CONSTRAINT fk_match_group FOREIGN KEY (group_id) REFERENCES tournament_groups(group_id) ON DELETE SET NULL,
    CONSTRAINT chk_match_teams CHECK (home_team_id != away_team_id),
    CONSTRAINT chk_match_stage CHECK (stage IN ('GROUP','ROUND_OF_16','QUARTER_FINAL','SEMI_FINAL','THIRD_PLACE','FINAL')),
    CONSTRAINT chk_match_status CHECK (status IN ('SCHEDULED','ONGOING','COMPLETED','POSTPONED','CANCELLED')),
    CONSTRAINT chk_match_et CHECK (has_extra_time IN ('Y','N')),
    CONSTRAINT chk_match_pen CHECK (has_penalties IN ('Y','N'))
);

-- 11. GOALS
CREATE TABLE goals (
    goal_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    match_id NUMBER NOT NULL,
    scorer_player_id NUMBER NOT NULL,
    assist_player_id NUMBER,
    team_id NUMBER NOT NULL,
    goal_minute NUMBER(3) NOT NULL,
    goal_type VARCHAR2(30) DEFAULT 'NORMAL' NOT NULL,
    half NUMBER(1) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_goal_match FOREIGN KEY (match_id) REFERENCES matches(match_id) ON DELETE CASCADE,
    CONSTRAINT fk_goal_scorer FOREIGN KEY (scorer_player_id) REFERENCES players(player_id) ON DELETE CASCADE,
    CONSTRAINT fk_goal_assist FOREIGN KEY (assist_player_id) REFERENCES players(player_id) ON DELETE SET NULL,
    CONSTRAINT fk_goal_team FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    CONSTRAINT chk_goal_type CHECK (goal_type IN ('NORMAL','OWN_GOAL','PENALTY')),
    CONSTRAINT chk_goal_half CHECK (half IN (1,2,3,4))
);

-- 12. CARDS
CREATE TABLE cards (
    card_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    match_id NUMBER NOT NULL,
    player_id NUMBER NOT NULL,
    team_id NUMBER NOT NULL,
    card_type VARCHAR2(10) NOT NULL,
    card_minute NUMBER(3) NOT NULL,
    reason VARCHAR2(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_card_match FOREIGN KEY (match_id) REFERENCES matches(match_id) ON DELETE CASCADE,
    CONSTRAINT fk_card_player FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE,
    CONSTRAINT fk_card_team FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    CONSTRAINT chk_card_type CHECK (card_type IN ('YELLOW','RED'))
);
