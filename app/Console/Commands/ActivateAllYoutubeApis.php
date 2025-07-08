<?php

namespace App\Console\Commands;

use App\Models\YouTubeApiKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ActivateAllYoutubeApis extends Command
{
    protected $signature = 'youtube:activate-all-keys';
    protected $description = 'Hit the /activate-all route every minute';
    public function handle()
    {
        /*
        $url = config('app.url') . '/api/activate-all';

        $response = Http::get($url);
        */
        $updated = YouTubeApiKey::where('is_active', false)->update(['is_active' => true]);

        $this->info("Total {$updated} API key(s) activated successfully.");
    }
}
