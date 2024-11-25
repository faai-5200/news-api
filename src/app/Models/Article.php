<?php
// app/Models/Article.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     title="Article",
 *     required={"title", "content", "source"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Breaking News"),
 *     @OA\Property(property="content", type="string", example="This is the article content."),
 *     @OA\Property(property="source", type="string", example="BBC News"),
 *     @OA\Property(property="category", type="string", example="Technology"),
 *     @OA\Property(property="author", type="string", example="John Doe"),
 *     @OA\Property(property="url", type="string", example="https://example.com/article"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-25T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-25T10:00:00Z")
 * )
 */
class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'source',
        'category',
        'author',
        'url',
    ];
}
