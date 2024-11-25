<?php
namespace App\Services;

use App\Models\Article;
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

    public function fetchAndStoreArticles()
    {
        $this->fetchNewsApiArticles();
        $this->fetchGuardianArticles();
        $this->fetchNytArticles();
    }

    public function fetchNewsApiArticles()
    {
        $response = Http::get('https://newsapi.org/v2/everything', [
            'apiKey' => $this->newsApiKey,
            'q' => 'latest',
        ]);

        if ($response->successful()) {
            $articles = $response->json()['articles'] ?? [];

            foreach ($articles as $article) {
                Article::updateOrCreate(
                    ['title' => $article['title']],
                    [
                        'content' => $article['content'] ?? null,
                        'source' => $article['source']['name'] ?? 'NewsAPI',
                        'url' => $article['url'] ?? null,
                        'author' => $article['author'] ?? 'Unknown',
                        'category' => 'General', // Assign default if not present
                    ]
                );
            }
        } else {
            \Log::error('Failed to fetch NewsAPI articles', $response->json());
        }
    }

    public function fetchGuardianArticles()
    {
        $response = Http::get('https://content.guardianapis.com/search', [
            'api-key' => $this->guardianApiKey,
        ]);

        if ($response->successful()) {
            $articles = $response->json()['response']['results'] ?? [];


            foreach ($articles as $article) {
                Article::updateOrCreate(
                    ['title' => $article['webTitle']],
                    [
                        'content' => $article['webTitle'], // Adjust as needed
                        'source' => 'The Guardian',
                        'url' => $article['webUrl'],
                        'author' => 'The Guardian Team', // Example default
                        'category' => $article['pillarName'] ?? 'General',
                    ]
                );
            }
        } else {
            \Log::error('Failed to fetch Guardian articles', $response->json());
        }
    }

    public function fetchNytArticles()
    {
        $response = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json', [
            'api-key' => $this->nytApiKey,
        ]);

        if ($response->successful()) {
            $articles = $response->json()['response']['docs'] ?? [];
            foreach ($articles as $article) {
                // Extract keywords and transform them into a comma-separated string
                $categories = collect($article['keywords'] ?? [])
                    ->pluck('value') // Get the 'value' of each keyword
                    ->implode(', '); // Join them into a string separated by commas

                Article::updateOrCreate(
                    ['title' => $article['headline']['main']],
                    [
                        'content' => $article['lead_paragraph'] ?? null,
                        'source' => 'The New York Times',
                        'url' => $article['web_url'],
                        'author' => $article['byline']['original'] ?? 'Unknown',
                        'category' => $categories ?: 'General', // Use 'General' if no keywords exist
                    ]
                );
            }
        } else {
            \Log::error('Failed to fetch NYT articles', $response->json());
        }
    }
}
