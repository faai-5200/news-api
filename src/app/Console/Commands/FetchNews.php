<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsService;

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

        // Fetch and store articles from all APIs
        $newsService->fetchAndStoreArticles();

        $this->info('News articles fetched and stored successfully!');
    }
}
