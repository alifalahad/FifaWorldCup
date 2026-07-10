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
            // 1. Ensure Home Team exists
            $homeTeam = Team::firstOrCreate(
                ['country_name' => $matchData['home_team']],
                ['abbreviation' => strtoupper(substr($matchData['home_team'], 0, 3)), 'continent' => 'UEFA']
            );

            // 2. Ensure Away Team exists
            $awayTeam = Team::firstOrCreate(
                ['country_name' => $matchData['away_team']],
                ['abbreviation' => strtoupper(substr($matchData['away_team'], 0, 3)), 'continent' => 'UEFA']
            );

            // 3. Map status to our ENUM ('SCHEDULED','LIVE','COMPLETED','POSTPONED','CANCELLED')
            $status = match($matchData['status'] ?? '') {
                'Match Finished' => 'COMPLETED',
                'Not Started' => 'SCHEDULED',
                'Live' => 'LIVE',
                default => 'SCHEDULED'
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
                    'match_date' => Carbon::parse($matchData['date'])->toDateString(),
                    'stage'      => 'GROUP', // Defaulting for simple mock
                    'home_score' => $matchData['home_score'],
                    'away_score' => $matchData['away_score'],
                    'status'     => $status,
                ]
            );

            $upsertCount++;
        }

        $this->info("Successfully synchronized {$upsertCount} matches from API!");
    }
}
