<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\NewsService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Articles", description="Manage news articles")
 */
class ArticleController extends Controller
{
    protected $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get a list of articles",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=false,
     *         description="Search query",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Articles retrieved successfully",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Article"))
     *     )
     * )
     */
    public function searchArticles(Request $request)
    {
        $query = Article::query();

        // Apply filters
        if ($request->has('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        if ($request->has('from')) {
            $query->where('created_at', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('created_at', '<=', $request->to);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('sources')) {
            $query->whereIn('source', (array) $request->sources);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $articles = $query->paginate($perPage);

        return response()->json($articles);
    }

    public function getArticles(Request $request)
    {
        // Store the latest articles from the APIs
        $this->newsService->fetchAndStoreArticles();

        return $this->searchArticles($request);
    }
    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get article details",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the article",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     )
     * )
     */
    public function getArticleDetails($id)
    {
        $article = Article::findOrFail($id);
        return response()->json($article);
    }
}
