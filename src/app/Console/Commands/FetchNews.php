<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsService;
use App\Models\Article;

class FetchNews extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Fetch news articles from APIs';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $newsService = app(NewsService::class);
        $articles = $newsService->fetchNewsApiArticles(['q' => 'latest']);

        foreach ($articles['articles'] as $article) {
            Article::updateOrCreate(
                ['title' => $article['title']],
                ['content' => $article['content'], 'source' => $article['source']['name']]
            );
        }

        $this->info('News articles fetched successfully!');
    }
}
