<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Tests\FifaTestCase;
use App\Models\GameMatch;
use App\Models\Team;
use Carbon\Carbon;

class SyncWorldCupApiTest extends FifaTestCase
{
    /**
     * Test the API sync command correctly upserts data without hitting the real network.
     */
    public function test_api_sync_command_upserts_match_data()
    {
        // 1. Fake the HTTP response so we don't make real network calls
        Http::fake([
            config('services.fifa_api.url') . '/*' => Http::response([
                'matches' => [
                    [
                        'id' => 9999,
                        'utcDate' => now()->toDateString(),
                        'status' => 'FINISHED',
                        'stage' => 'FINAL',
                        'homeTeam' => ['name' => 'Testland', 'tla' => 'TES'],
                        'awayTeam' => ['name' => 'Mockovia', 'tla' => 'MOC'],
                        'score' => [
                            'fullTime' => ['home' => 5, 'away' => 2]
                        ]
                    ]
                ]
            ], 200)
        ]);

        // 2. Run the console command
        Artisan::call('api:sync-matches');

        // 3. Assert the Teams were created
        $this->assertDatabaseHas('teams', [
            'country_name' => 'Testland',
            'abbreviation' => 'TES',
            'continent' => 'UEFA'
        ]);

        $this->assertDatabaseHas('teams', [
            'country_name' => 'Mockovia',
            'abbreviation' => 'MOC',
            'continent' => 'UEFA'
        ]);

        // 4. Assert the Match was created/updated correctly
        $homeTeam = Team::where('country_name', 'Testland')->first();
        $awayTeam = Team::where('country_name', 'Mockovia')->first();

        $this->assertDatabaseHas('matches', [
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'home_score' => 5,
            'away_score' => 2,
            'status' => 'COMPLETED'
        ]);
        
        // Ensure another run doesn't create duplicate matches
        Artisan::call('api:sync-matches');
        
        $matchCount = GameMatch::where('home_team_id', $homeTeam->team_id)
                               ->where('away_team_id', $awayTeam->team_id)
                               ->count();
                               
        $this->assertEquals(1, $matchCount, 'The sync command created a duplicate match instead of updating.');
    }
}
