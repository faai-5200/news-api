<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsService
{
    protected $newsApiKey;
    protected $guardianApiKey;
    protected $nytApiKey;

    public function __construct()
    {
        $this->newsApiKey = env('NEWSAPI_KEY');
        $this->guardianApiKey = env('GUARDIAN_API_KEY');
        $this->nytApiKey = env('NYTIMES_API_KEY');
    }

    public function fetchNewsApiArticles($params = [])
    {
        $defaultParams = [
            'q' => $params['q'] ?? 'latest',   // Default keyword for search
            'from' => $params['from'] ?? null,  // Start date filter (optional)
            'to' => $params['to'] ?? null,      // End date filter (optional)
            'sources' => $params['sources'] ?? null, // Specific sources (optional)
        ];

        $response = Http::get('https://newsapi.org/v2/everything', array_merge($params, $defaultParams, [
            'apiKey' => $this->newsApiKey, // Add API key
        ]));

        return $response->successful() ? $response->json() : [];
    }

    public function fetchGuardianArticles($params = [])
    {
        $response = Http::get('https://content.guardianapis.com/search', array_merge($params, [
            'q' => $params['q'] ?? 'latest',
            'from-date' => $params['from'] ?? null,  // From date
            'to-date' => $params['to'] ?? null,      // To date
            'section' => $params['category'] ?? null, // Category filter
            'api-key' => $this->guardianApiKey,      // Add API key
        ]));

        return $response->successful() ? $response->json() : [];
    }

    public function fetchNytArticles($params = [])
    {
        $response = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json', array_merge($params, [
            'q' => $params['q'] ?? 'latest',
            'begin_date' => isset($params['from']) ? str_replace('-', '', $params['from']) : null, // From date
            'end_date' => isset($params['to']) ? str_replace('-', '', $params['to']) : null,       // To date
            'api-key' => $this->nytApiKey,  // Add API key
        ]));


        return $response->successful() ? $response->json() : [];
    }
}
