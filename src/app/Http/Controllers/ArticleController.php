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

    /**
     * Fetch paginated articles
     */
    public function getArticles(Request $request)
    {
        $params = $request->only(['q', 'from', 'to', 'category', 'sources']);
        $page = $request->input('page', 1); // Default to page 1 if not provided
        $perPage = $request->input('per_page', 10); // Default to 10 articles per page

        // Fetch all articles from the service
        $articles = $this->newsService->fetchNewsApiArticles($params);

        // Paginate articles manually
        $paginatedArticles = $this->paginate($articles['articles'], $perPage, $page);

        return response()->json($paginatedArticles);
    }

    /**
     * Search for articles across all APIs with pagination
     */
    public function searchArticles(Request $request)
    {
        $params = $request->only(['q', 'from', 'to', 'category', 'sources']);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        // Default query parameter
        if (empty($params['q'])) {
            $params['q'] = 'latest'; // Fallback to a default keyword if none is provided
        }

        // Fetch articles from different APIs
        $newsApiArticles = $this->newsService->fetchNewsApiArticles($params);
        $guardianArticles = $this->newsService->fetchGuardianArticles($params);
        $nytArticles = $this->newsService->fetchNytArticles($params);

        // Combine all articles
        $allArticles = array_merge(
            $newsApiArticles['articles'] ?? [],
            $guardianArticles['response']['results'] ?? [],
            $nytArticles['response']['docs'] ?? []
        );

        // Format articles from different APIs for uniform structure
        $formattedArticles = array_map(function ($article) {
            return [
                'title' => $article['title'] ?? $article['webTitle'] ?? $article['headline']['main'] ?? 'No Title',
                'content' => $article['content'] ?? $article['body'] ?? ($article['abstract'] ?? 'No Content'),
                'source' => $article['source']['name'] ?? $article['sectionName'] ?? 'Unknown Source',
                'published_at' => $article['publishedAt'] ?? $article['webPublicationDate'] ?? $article['pub_date'] ?? null,
                'url' => $article['url'] ?? $article['webUrl'] ?? $article['web_url'] ?? null,
            ];
        }, $allArticles);

        // Paginate the combined and formatted articles
        $paginatedArticles = $this->paginate($formattedArticles, $perPage, $page);

        // Return paginated results
        return response()->json($paginatedArticles);
    }

    /**
     * Retrieve single article details (dummy implementation)
     */
    public function getArticleDetails($id)
    {
        $article = \App\Models\Article::find($id);

        // Check if the article exists
        if (!$article) {
            return response()->json([
                'message' => 'Article not found.',
            ], 404);
        }

        // Return the article details
        return response()->json([
            'article' => $article,
        ]);
    }

    /**
     * Helper function to paginate an array
     */
    protected function paginate(array $items, $perPage, $page)
    {
        $offset = ($page - 1) * $perPage;
        $paginatedItems = array_slice($items, $offset, $perPage);

        return [
            'data' => $paginatedItems,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => count($items),
            'last_page' => ceil(count($items) / $perPage),
        ];
    }
}
