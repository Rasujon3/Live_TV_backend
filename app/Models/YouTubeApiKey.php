<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YouTubeApiKey extends Model
{
    protected $table = 'youtube_api_keys';
    protected $fillable = [
        'api_key',
        'is_active',
        'usage_count',
        'comment'
    ];

    public static function getUsableYoutubeApiKey()
    {
        return self::where('is_active', true)->inRandomOrder()->first();
    }
}
