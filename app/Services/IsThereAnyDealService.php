<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class IsThereAnyDealService
{
    const HISTORY_LOW_KEYS = ['all', 'y1', 'm3'];

    protected $clientId;
    protected $apiKey;
    protected $endpoint;

    public function __construct()
    {
        $this->clientId = config('services.isthereanydeal.client_id');
        $this->apiKey = config('services.isthereanydeal.api_key');
        $this->endpoint = config('services.isthereanydeal.endpoint');
    }

    public function getDataForApi()
    {
        $json = file_get_contents(resource_path('data/itad_game_map.json'));
        // Remove // comments
        $json = preg_replace('!//.*!', '', $json);
        // Remove /* ... */ comments
        $json = preg_replace('!/\*.*?\*/!s', '', $json);
        $data = json_decode($json, true);
        return $data;
    }
    public function getAllGuidsForApi()
    {
        $data = $this->getDataForApi();
        return array_keys($data['id']);
    }


    public function groupResultsByLogicalGame(array $apiResults)
    {
        $data = $this->getDataForApi();
        $grouped = [];

        foreach ($apiResults as $idx => $result) {
            if (!isset($result['id'])) continue;
            $guid = $result['id'];
            if (!isset($data['id'][$guid])) continue;

            $group_data_keys = $data['id'][$guid];

            // sort and concat keys to form a unique logical key
            sort($group_data_keys);
            $logicalKey = implode('|', $group_data_keys);

            // Initialize the grouped entry if it doesn't exist
            if (!isset($grouped[$logicalKey])) {
                $grouped[$logicalKey] = [
                    //'name' => $data['name'][$logicalKey] ?? $logicalKey,
                    'keys' => $group_data_keys,
                    'guid' => [],
                    'deals' => [],
                ];
            }
            $grouped[$logicalKey]['guid'][] = $guid;
            $grouped[$logicalKey]['deals'] = array_merge($grouped[$logicalKey]['deals'], $result['deals'] ?? []);
        }

        return [
            "sale" => $grouped,
            "name" => $data["name"],
            "order" => $data["order"],
        ];
    }

    /**
     * Get current sales for specific games by plain IDs.
     * @param array $gamePlains Array of game plain IDs (e.g. ['doom', 'halflife'])
     * @return array|null
     */
    public function getCurrentSales(array $gamePlains)
    {
        $response = Http::get($this->endpoint . 'game/prices/', [
            'key' => $this->apiKey,
            'plains' => implode(',', $gamePlains),
        ]);

        if ($response->successful()) {
            return $response->json()['data'] ?? null;
        }

        return null;
    }

    public function getV3PricesAndGroup()
    {
        $guids = $this->getAllGuidsForApi();
        $cacheKey = 'itad_v3prices_' . md5(json_encode($guids));

        // Try to get from cache first
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Use a lock to prevent stampede
        $lock = Cache::lock($cacheKey . '_lock', 10); // 10 seconds timeout

        try {
            if ($lock->get()) {
                // Double-check cache after acquiring lock
                $cached = Cache::get($cacheKey);
                if ($cached !== null) {
                    return $cached;
                }

                $response = Http::post($this->endpoint . 'games/prices/v3?key=' . urlencode($this->apiKey), $guids);

                if ($response->successful()) {
                    $results = $this->groupResultsByLogicalGame($response->json());
                    Cache::put($cacheKey, $results, 600);
                    return $results;
                }

                return null;
            } else {
                // Wait for the lock to be released and return the cached value
                return Cache::get($cacheKey);
            }
        } finally {
            optional($lock)->release();
        }
    }
}
