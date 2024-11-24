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
        $response = Http::get('https://newsapi.org/v2/everything', array_merge($params, [
            'apiKey' => $this->newsApiKey,
        ]));

        return $response->json();
    }

    public function fetchGuardianArticles($params = [])
    {
        $response = Http::get('https://content.guardianapis.com/search', array_merge($params, [
            'api-key' => $this->guardianApiKey,
        ]));

        return $response->json();
    }

    public function fetchNytArticles($params = [])
    {
        $response = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json', array_merge($params, [
            'api-key' => $this->nytApiKey,
        ]));

        return $response->json();
    }
}
