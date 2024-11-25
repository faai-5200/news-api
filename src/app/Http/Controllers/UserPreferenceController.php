<?php
namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\NewsService;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="User Preferences", description="Manage user preferences and show using preferred articles")
 */
class UserPreferenceController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Set user preferences",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="sources",
     *                 type="array",
     *                 @OA\Items(type="string", example="BBC News")
     *             ),
     *             @OA\Property(
     *                 property="categories",
     *                 type="array",
     *                 @OA\Items(type="string", example="Arts")
     *             ),
     *             @OA\Property(
     *                 property="authors",
     *                 type="array",
     *                 @OA\Items(type="string", example="John Doe")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Preferences updated successfully")
     * )
     */
    public function setPreferences(Request $request)
    {
        $preferences = $request->validate([
            'sources' => 'array',
            'categories' => 'array',
            'authors' => 'array',
        ]);

        $user = $request->user();

        // Ensure the preferences are properly encoded into JSON format before saving
        $preferences = [
            'sources' => json_encode($preferences['sources'] ?? []),
            'categories' => json_encode($preferences['categories'] ?? []),
            'authors' => json_encode($preferences['authors'] ?? []),
        ];

        // Update or create preferences for the user
        $user->preferences()->updateOrCreate(
            ['user_id' => $user->id],
            $preferences
        );

        return response()->json(['message' => 'Preferences updated successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Get user preferences",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="sources", type="array", @OA\Items(type="string", example="BBC News")),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string", example="Arts")),
     *             @OA\Property(property="authors", type="array", @OA\Items(type="string", example="John Doe"))
     *         )
     *     )
     * )
     */
    public function getPreferences(Request $request)
    {
        return response()->json($request->user()->preferences);
    }
    /**
     * @OA\Get(
     *     path="/api/personalized-feed",
     *     summary="Get personalized articles based on user preferences",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Personalized feed retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="Tech News Update"),
     *                     @OA\Property(property="content", type="string", example="Content of the article"),
     *                     @OA\Property(property="source", type="string", example="BBC"),
     *                     @OA\Property(property="author", type="string", example="John Doe"),
     *                     @OA\Property(property="category", type="string", example="Technology"),
     *                     @OA\Property(property="url", type="string", example="https://bbc.com/article")
     *                 )
     *             ),
     *             @OA\Property(property="total", type="integer", example=100),
     *             @OA\Property(property="per_page", type="integer", example=10),
     *             @OA\Property(property="last_page", type="integer", example=10),
     *             @OA\Property(property="next_page_url", type="string", example="http://localhost:8080/api/personalized-feed?page=2"),
     *             @OA\Property(property="prev_page_url", type="string", example="http://localhost:8080/api/personalized-feed?page=1")
     *         )
     *     )
     * )
     */
    public function personalizedFeed(Request $request)
    {
        $preferences = $request->user()->preferences;

        $sources = json_decode($preferences->sources ?? '[]', true);
        $categories = json_decode($preferences->categories ?? '[]', true);
        $authors = json_decode($preferences->authors ?? '[]', true);

        // Query the local database for matching articles
        $articles = Article::query()
            ->when(!empty($sources), function ($query) use ($sources) {
                foreach ($sources as $source) {
                    $query->orWhere('source', 'like', '%' . $source . '%');
                }
            })
            ->when(!empty($categories), function ($query) use ($categories) {
                foreach ($categories as $category) {
                    $query->orWhere('category', 'like', '%' . $category . '%');
                }
            })
            ->when(!empty($authors), function ($query) use ($authors) {
                foreach ($authors as $author) {
                    $query->orWhere('author', 'like', '%' . $author . '%');
                }
            })
            ->orderBy('created_at', 'desc') // Order by latest articles
            ->paginate(10); // Paginate the results

        return response()->json($articles);
    }
}
