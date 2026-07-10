<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class FifaApiService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.fifa_api.url', 'http://127.0.0.1:8000/api/mock');
        $this->apiKey = config('services.fifa_api.key', '');
        $this->timeout = config('services.fifa_api.timeout', 10);
    }

    /**
     * Fetch all current matches from the API and return as a Collection.
     *
     * @return Collection
     */
    public function getMatches(): Collection
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept'    => 'application/json',
            ])
            ->timeout($this->timeout)
            ->get("{$this->baseUrl}/matches");

            if ($response->successful()) {
                // Return the matches array wrapped in a Laravel Collection
                return collect($response->json('data', []));
            }

            Log::error('FIFA API error response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('FIFA API connection timeout or failure', [
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            Log::error('Unexpected error while fetching FIFA API', [
                'message' => $e->getMessage()
            ]);
        }

        // Return empty collection on failure so consuming code doesn't crash
        return collect([]);
    }
}
