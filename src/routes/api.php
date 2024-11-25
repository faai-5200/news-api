<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserPreferenceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/articles', [ArticleController::class, 'getArticles']);
Route::get('/articles/search', [ArticleController::class, 'searchArticles']);
Route::get('/articles/{id}', [ArticleController::class, 'getArticleDetails']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/preferences', [UserPreferenceController::class, 'setPreferences']);
    Route::get('/preferences', [UserPreferenceController::class, 'getPreferences']);
    Route::get('/personalized-feed', [UserPreferenceController::class, 'personalizedFeed']);

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

