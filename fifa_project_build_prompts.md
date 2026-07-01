# FIFA World Cup Management System — Build Prompts (Tier 2, 14 tables + 1 view)

**How to use this:** Start a Claude Code session in your project folder. For Prompt 1, attach `fifa_world_cup_database_design_v2.md` so Claude has the full schema in context. Run the prompts in order, one at a time — each one builds on the last. Check the output of each step before moving to the next (run migrations, view the page, etc.) rather than firing all 25 in a row blind.

---

## Phase A — Project Setup

### Prompt 1 — Initialize the project
```
I'm building a FIFA World Cup Management System as a web lab project using Laravel and Oracle Database. I've attached my full database design document (fifa_world_cup_database_design_v2.md) — read it fully before doing anything, it defines all 14 tables, the group_standings view, and relationships.

Set up a fresh Laravel project for this. Install and configure the yajra/laravel-oci8 package for Oracle connectivity, and set up the .env file with placeholder Oracle connection variables (host, port, service name, username, password) that I'll fill in myself. Confirm the Oracle connection config in config/database.php is correct. Don't create any migrations yet — just get the base project running with `php artisan serve` and confirm Oracle driver is registered.
```

### Prompt 2 — Base layout & navigation shell
```
Create the base Blade layout for this project (resources/views/layouts/app.blade.php) using Tailwind CSS (already bundled with Laravel via Vite). Include a top navigation bar with placeholder links for: Home, Tournaments, Teams, Players, Fixtures, Standings, and a login/logout area on the right that will later show different links for guests vs. logged-in admins. Keep it clean and simple — this is a lab project, not a design showcase. Create a placeholder home page that extends this layout.
```

---

## Phase B — Database Layer

### Prompt 3 — Auth migrations
```
Create Laravel migrations for the ROLE and USERS tables exactly as specified in fifa_world_cup_database_design_v2.md section 4.1–4.2. Use Oracle-appropriate column types via the oci8 driver. USERS should extend Laravel's default users migration (add role_id as a foreign key to roles, plus is_active) rather than being a totally separate table, so Laravel's built-in auth scaffolding still works with it. Do not add Tier-1-only assumptions — keep role_id nullable=false with a default role of VIEWER if you want a sensible fallback.
```

### Prompt 4 — Core entity migrations
```
Create Laravel migrations for TOURNAMENT, TEAM, COACH, PLAYER, STADIUM, and REFEREE exactly matching sections 4.3–4.8 of the design document, including all CHECK constraints (status enums, continent/confederation values, position GK/DF/MF/FW, card-related constraints don't apply here). Use Oracle CHECK constraint syntax compatible with the oci8 driver. Add appropriate indexes on columns that will be searched or filtered often (country_name, nationality, position).
```

### Prompt 5 — Structural & junction table migrations
```
Create Laravel migrations for TOURNAMENT_GROUP, TEAM_TOURNAMENT, and PLAYER_TOURNAMENT exactly matching sections 4.9–4.11 of the design document. Make sure to implement the composite UNIQUE constraints described in the doc: (tournament_id, group_name) on TOURNAMENT_GROUP, (team_id, tournament_id) on TEAM_TOURNAMENT, and (player_id, team_tournament_id) on PLAYER_TOURNAMENT. Get the foreign key dependency order right in the migration filenames so `php artisan migrate` runs cleanly.
```

### Prompt 6 — Match, goal, and card migrations
```
Create Laravel migrations for MATCHES, GOAL, and CARD exactly matching sections 4.12–4.14 of the design document. Implement the business rule CHECK (home_team_id != away_team_id) on MATCHES. Make sure referee_id, group_id on MATCHES are nullable as specified, and assist_player_id on GOAL is nullable. Run all migrations now (roles through cards) and confirm the full schema is created in Oracle with no FK errors.
```

### Prompt 7 — Group standings view + seeders
```
Create a raw SQL migration that creates the group_standings VIEW exactly as defined in section 6 of the design document (the CTE with UNION ALL over home/away perspectives). In Laravel this needs to go in a migration using DB::statement() with raw SQL rather than the schema builder, since views aren't supported by Schema::create. Then create database seeders for: ROLE (ADMIN, VIEWER), and a starter set of realistic sample data — 2 tournaments (e.g. 2022, 2026), 8 teams, 2 stadiums, 1 referee, so I have something to test the app against as I build it.
```

---

## Phase C — Models & Relationships

### Prompt 8 — Eloquent models for all 14 tables
```
Create Eloquent models for all 14 tables (Role, User update, Tournament, Team, Coach, Player, Stadium, Referee, TournamentGroup, TeamTournament, PlayerTournament, Match, Goal, Card) with the correct relationships between them as described in section 5 and the Laravel notes in section 8 of the design document — including the belongsToMany for Team↔Tournament through team_tournament, the two separate belongsTo relationships on Match for homeTeam/awayTeam, and the two separate belongsTo relationships on Goal for scorer/assister. Add $fillable arrays matching each migration. Note: "Match" is a reserved-ish name in PHP — call the model Match but make sure it doesn't collide with anything, or rename to GameMatch if you hit issues, your call.
```

### Prompt 9 — Standings view model
```
Create a read-only GroupStanding Eloquent model backed by the group_standings view, following the pattern in section 8 of the design document (no primary key, no timestamps, not incrementing). Add a static method or query scope that returns standings for a given group_id ranked by points, then goal difference, then goals_for (matching the RANK() OVER query in section 6). Write a quick test route to confirm it returns correct data against the seeded sample matches.
```

---

## Phase D — Authentication & Access Control

### Prompt 10 — Auth scaffolding + role middleware
```
Install Laravel Breeze for authentication (login/register/logout views). Then create a custom middleware called EnsureUserHasRole that checks the logged-in user's role_id against the ROLE table and restricts access accordingly. Register it in the HTTP kernel as 'role'. I'll use it like Route::middleware('role:ADMIN') on admin-only routes. Update the seeded users table with one ADMIN test user and one VIEWER test user so I can test both permission levels.
```

### Prompt 11 — Admin dashboard shell
```
Build an admin dashboard page at /admin/dashboard, protected by the 'role:ADMIN' middleware, showing quick summary cards: total tournaments, total teams, total players, total matches played, total goals scored — pulled live from the database, not hardcoded. Add an admin sidebar navigation linking to sections we'll build next: Tournaments, Teams, Players, Coaches, Stadiums, Referees, Matches. Non-admin users hitting /admin routes should get redirected with a "not authorized" message, not a raw 403 page.
```

---

## Phase E — Admin CRUD Modules

### Prompt 12 — Tournament CRUD
```
Build full CRUD (index, create, edit, delete, show) for TOURNAMENT under /admin/tournaments, admin-only. Use Laravel Form Request classes for validation (name required, year required + unique + must be a valid 4-digit year, start_date before end_date, status must be one of the enum values from the design doc). The index page should be a table with search-by-name and filter-by-status. The show page should list that tournament's groups and teams (empty for now, we'll wire those up next).
```

### Prompt 13 — Team, Coach CRUD + tournament registration
```
Build full CRUD for TEAM and COACH under /admin/teams and /admin/coaches. Then, on the tournament show page from the previous step, add a "Register Team" action that creates a TEAM_TOURNAMENT record — a form where the admin picks an existing team, assigns it to a group (dropdown of that tournament's groups, or "unassigned"), and optionally assigns a coach and seed position. Validate against the (team_id, tournament_id) uniqueness constraint and show a friendly error if that team is already registered for this tournament.
```

### Prompt 14 — Player CRUD + roster management
```
Build full CRUD for PLAYER under /admin/players (fields per section 4.6, with position as a dropdown GK/DF/MF/FW). Then, on each team-tournament registration (from the previous prompt), add a "Manage Roster" screen that lets the admin add players to that squad via PLAYER_TOURNAMENT — assigning jersey_number and is_captain. Enforce the (player_id, team_tournament_id) uniqueness constraint, enforce that jersey numbers are unique within a single team_tournament roster (this isn't a DB constraint in the design but should be validated at the application level), and show current roster count against a max of 26 players.
```

### Prompt 15 — Stadium & Referee CRUD
```
Build full CRUD for STADIUM and REFEREE under /admin/stadiums and /admin/referees, following the same pattern as the previous CRUD modules (Form Requests for validation, searchable index tables). Keep these simple — they're supporting entities, not core focus of the project.
```

### Prompt 16 — Match scheduling & results
```
Build full CRUD for MATCHES under /admin/matches. The create/edit form should let the admin pick: tournament, stadium, referee (optional), home team and away team (both filtered to only teams registered for the selected tournament via TEAM_TOURNAMENT), group (only if stage is GROUP, hidden/nulled for knockout stages), match_date, and stage. Enforce the CHECK (home_team_id != away_team_id) rule with a clear validation message. Add a separate simple "Enter Result" action on already-scheduled matches to update home_score, away_score, has_extra_time, has_penalties, and status — this is the screen that will indirectly drive the group_standings view once matches are marked COMPLETED.
```

### Prompt 17 — Goal & Card entry
```
On the match show/edit page from the previous prompt, add two inline sections: "Goals" and "Cards". Each should let the admin add a GOAL (scorer, optional assist, team — both scorer/assist limited to players from that match's two team rosters, goal_minute, goal_type, half) or a CARD (player, team, card_type, card_minute, reason) directly from the match page, with an editable/deletable list of existing goals and cards for that match shown below. This is where all the match-event data entry happens — no separate global "add goal" page needed.
```

---

## Phase F — Public-Facing Pages & Reporting

### Prompt 18 — Public tournament & team pages
```
Build public (no login required) pages: /tournaments (list, with year/status filter), /tournaments/{id} (overview: dates, host, groups, registered teams), /teams (list, searchable by name/continent), /teams/{id} (team profile: current squad if registered for an ongoing/upcoming tournament, tournament history via team_tournament). Reuse the main layout from Prompt 2, not the admin layout.
```

### Prompt 19 — Fixtures, results & standings display
```
Build a public /tournaments/{id}/fixtures page listing all matches for that tournament grouped by stage (Group stage matches grouped by group letter, then knockout stages), showing date, stadium, teams, and score if completed. Add a public /tournaments/{id}/standings page that queries the group_standings view (via the GroupStanding model from Prompt 9) and displays a proper ranked table per group — position, team, played, won, drawn, lost, goals for/against, goal difference, points — matching the standard FIFA group table layout.
```

### Prompt 20 — Statistics & leaderboards
```
Build a public /tournaments/{id}/stats page with three sections: Top Scorers (aggregate GOAL records by player for this tournament, ranked by goal count, using goal_type to note penalties separately if you want), Assist Leaders (same aggregation on assist_player_id), and Disciplinary Table (CARD counts by team, yellow vs red broken out). Use efficient aggregate queries (GROUP BY + COUNT), not N+1 loops in PHP.
```

### Prompt 21 — Search
```
Add a global search bar in the main navigation that searches across TEAM (country_name), PLAYER (first_name + last_name), and COACH (first_name + last_name), returning a combined results page grouped by type. Keep this simple — a single search endpoint with basic LIKE queries is fine for this project's scope, no need for a search engine package.
```

---

## Phase G — Validation, Testing & Polish

### Prompt 22 — Cross-cutting validation review
```
Review every Form Request class created so far across the whole project and make sure they consistently enforce every constraint listed in the design document: all CHECK enum values, all composite UNIQUE constraints, the home_team_id != away_team_id rule, and nullable vs required fields matching the schema exactly. Flag and fix any place where a form currently lets invalid data through that the database schema would otherwise reject with an unhelpful Oracle error.
```

### Prompt 23 — Feature tests
```
Write Laravel feature tests (using the Oracle test database or a transaction rollback strategy) covering: (1) an unauthenticated user cannot access /admin routes, (2) a VIEWER-role user cannot access /admin routes but an ADMIN can, (3) creating a tournament with a duplicate year fails validation, (4) registering the same team twice for one tournament fails, (5) a completed match with scores correctly appears in the group_standings view output. These five are the core business rules worth proving actually work.
```

### Prompt 24 — UI polish & error handling
```
Do a pass over the whole app for polish: consistent pagination on all index tables (15 per page), flash messages on create/update/delete actions, a proper 403 page for unauthorized access, a proper 404 page, and confirm all forms show validation errors inline next to the relevant field rather than as a generic banner. Check mobile responsiveness on the public pages at minimum (fixtures, standings, team list).
```

### Prompt 25 — Documentation & handoff
```
Write a README.md for this project covering: project overview, tech stack (Laravel + Oracle via yajra/laravel-oci8), setup instructions (clone, .env config, migrate, seed), the two test accounts (admin/viewer) with credentials, a summary of the 14 tables + the group_standings view, and a short "known limitations / scope decisions" section explaining that officiating crews, generic match events, and standings are intentionally simplified per the project's lab scope (link back to the design rationale if useful). Embed the ER diagram from fifa_world_cup_database_design_v2.md.
```

---

## Notes on ordering

- Phases B and C (database + models) must be done before anything else — everything downstream depends on them.
- Phase D (auth) needs to exist before Phase E, since all admin CRUD is gated behind it.
- Within Phase E, the order matters: Tournament → Team/Coach → Player → Stadium/Referee → Matches → Goals/Cards, because later screens (e.g. match creation) need to filter teams by tournament registration, which only exists once TEAM_TOURNAMENT is populated.
- Phase F can technically run in parallel with late Phase E if you want to work on public pages and admin CRUD side by side, but doing it after is safer for a first pass.
