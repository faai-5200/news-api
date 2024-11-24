<?php

namespace App\Http\Controllers;

use App\Services\NewsService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    // Fetch paginated articles
    public function getArticles(Request $request)
    {
        $params = $request->only(['q', 'from', 'to', 'category', 'sources']);
        $articles = $this->newsService->fetchNewsApiArticles($params);

        return response()->json($articles);
    }

    // Search for articles
    public function searchArticles(Request $request)
    {
        $params = $request->only(['q', 'from', 'to', 'category', 'sources']);
        $newsApiArticles = $this->newsService->fetchNewsApiArticles($params);
        $guardianArticles = $this->newsService->fetchGuardianArticles($params);
        $nytArticles = $this->newsService->fetchNytArticles($params);

        return response()->json([
            'newsApi' => $newsApiArticles,
            'guardian' => $guardianArticles,
            'nyt' => $nytArticles,
        ]);
    }

    // Retrieve single article (dummy implementation)
    public function getArticleDetails($id)
    {
        // In a real implementation, fetch the article details from your database or API
        return response()->json(['article' => 'Article details for ID: ' . $id]);
    }
}
