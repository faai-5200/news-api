<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = ['user_id', 'sources', 'categories', 'authors'];

    /**
     * Define the inverse relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
