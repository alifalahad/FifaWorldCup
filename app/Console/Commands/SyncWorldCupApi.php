<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FifaApiService;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\GameMatch;
use App\Models\Stadium;
use Carbon\Carbon;

class SyncWorldCupApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:sync-matches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest matches from FIFA API and sync to database';

    /**
     * Execute the console command.
     */
    public function handle(FifaApiService $apiService)
    {
        $this->info('Starting FIFA API sync...');

        $matches = $apiService->getMatches();

        if ($matches->isEmpty()) {
            $this->warn('No data received from API or request failed.');
            return;
        }

        // To ensure we don't mix API data with your manual lab data randomly,
        // we'll create a dedicated "API World Cup" tournament context for this sync.
        $tournament = Tournament::firstOrCreate(
            ['name' => 'API Synced World Cup', 'year' => 2099],
            [
                'host_country' => 'Global',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'total_teams' => 32,
                'status' => 'ONGOING'
            ]
        );

        // We also need a default stadium since it's required by our schema
        $stadium = Stadium::firstOrCreate(
            ['name' => 'API Default Stadium'],
            [
                'city' => 'API City',
                'country' => 'API Country',
                'capacity' => 50000,
                'surface_type' => 'GRASS'
            ]
        );

        $upsertCount = 0;

        foreach ($matches as $matchData) {
            // Check if homeTeam and awayTeam exist (sometimes missing in TBD matches)
            if (empty($matchData['homeTeam']['name']) || empty($matchData['awayTeam']['name'])) {
                continue;
            }

            // 1. Ensure Home Team exists
            $homeTla = substr($matchData['homeTeam']['tla'] ?? strtoupper(substr($matchData['homeTeam']['name'], 0, 3)), 0, 3);
            $homeTeam = $this->resolveTeam($matchData['homeTeam']['name'], $homeTla);

            // 2. Ensure Away Team exists
            $awayTla = substr($matchData['awayTeam']['tla'] ?? strtoupper(substr($matchData['awayTeam']['name'], 0, 3)), 0, 3);
            $awayTeam = $this->resolveTeam($matchData['awayTeam']['name'], $awayTla);
            
            if (!$homeTeam || !$awayTeam) {
                continue; // Skip if we couldn't safely resolve/create the teams
            }

            // 3. Map status to our ENUM ('SCHEDULED','LIVE','COMPLETED','POSTPONED','CANCELLED')
            $status = match($matchData['status'] ?? '') {
                'FINISHED' => 'COMPLETED',
                'SCHEDULED', 'TIMED' => 'SCHEDULED',
                'IN_PLAY', 'PAUSED' => 'LIVE',
                'POSTPONED' => 'POSTPONED',
                'CANCELLED' => 'CANCELLED',
                default => 'SCHEDULED'
            };

            // Map stage string
            $stageStr = strtoupper($matchData['stage'] ?? '');
            $stage = match(true) {
                str_contains($stageStr, 'GROUP') => 'GROUP',
                str_contains($stageStr, '16') => 'ROUND_OF_16',
                str_contains($stageStr, 'QUARTER') => 'QUARTER_FINAL',
                str_contains($stageStr, 'SEMI') => 'SEMI_FINAL',
                str_contains($stageStr, 'THIRD') => 'THIRD_PLACE',
                str_contains($stageStr, 'FINAL') => 'FINAL',
                default => 'GROUP'
            };

            // 4. Upsert the match (Update if exists, Create if new)
            // We use tournament_id + home_team_id + away_team_id as the unique composite key for this lab
            GameMatch::updateOrCreate(
                [
                    'tournament_id' => $tournament->tournament_id,
                    'home_team_id'  => $homeTeam->team_id,
                    'away_team_id'  => $awayTeam->team_id,
                ],
                [
                    'stadium_id' => $stadium->stadium_id,
                    'match_date' => Carbon::parse($matchData['utcDate'])->toDateString(),
                    'stage'      => $stage, 
                    'home_score' => $matchData['score']['fullTime']['home'] ?? null,
                    'away_score' => $matchData['score']['fullTime']['away'] ?? null,
                    'status'     => $status,
                ]
            );

            $upsertCount++;
        }

        $this->info("Successfully synchronized {$upsertCount} matches from API!");
    }

    private function resolveTeam(string $name, string $tla)
    {
        // Find by exact name OR abbreviation
        $team = Team::where('country_name', $name)->orWhere('abbreviation', $tla)->first();
        
        if ($team) {
            return $team;
        }

        try {
            return Team::create([
                'country_name' => $name,
                'abbreviation' => $tla,
                'continent' => 'UEFA'
            ]);
        } catch (\Exception $e) {
            // If it still violates a constraint (e.g. name exists but abbreviation doesn't match our logic)
            // Just return null so we can skip this match instead of crashing
            return null;
        }
    }
}
