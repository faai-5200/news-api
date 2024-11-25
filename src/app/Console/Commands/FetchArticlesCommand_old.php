<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAPIService;
use App\Services\GuardianAPIService;
use App\Services\NYTAPIService;
use App\Models\Article;

class FetchArticlesCommand extends Command
{
    protected $signature = 'fetch:articles';
    protected $description = 'Fetch articles from news APIs';

    public function handle()
    {
        // Services to fetch articles
        $newsAPIService = new NewsAPIService();
        $guardianAPIService = new GuardianAPIService();
        $nytAPIService = new NYTAPIService();

        $articles = array_merge(
            $newsAPIService->fetchArticles(),
            $guardianAPIService->fetchArticles(),
            $nytAPIService->fetchArticles()
        );

        // Store articles in the database
        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['external_id' => $article['external_id'], 'source' => $article['source']],
                $article
            );
        }

        $this->info('Articles fetched and stored successfully.');
    }
}
