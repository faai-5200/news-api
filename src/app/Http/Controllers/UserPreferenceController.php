<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function setPreferences(Request $request)
    {
        $preferences = $request->validate([
            'sources' => 'array',
            'categories' => 'array',
            'authors' => 'array',
        ]);

        $user = $request->user();
        $user->preferences()->updateOrCreate(['user_id' => $user->id], $preferences);

        return response()->json(['message' => 'Preferences updated successfully']);
    }

    public function getPreferences(Request $request)
    {
        return response()->json($request->user()->preferences);
    }

    public function personalizedFeed(Request $request)
    {
        $preferences = $request->user()->preferences;

        // Use preferences to filter articles
        $articles = app(NewsService::class)->fetchNewsApiArticles([
            'sources' => implode(',', $preferences->sources ?? []),
            'category' => implode(',', $preferences->categories ?? []),
        ]);

        return response()->json($articles);
    }
}
