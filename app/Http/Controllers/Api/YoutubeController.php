<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class YoutubeController extends Controller
{
    public function showLiveVideo(Request $request)
    {
        $channelId = $request->channelId;

        if (!$channelId) {
            return response('Channel ID is required.', 400);
        }

        $apiKey = env('YOUTUBE_API_KEY');
        $url = 'https://www.googleapis.com/youtube/v3/search';

        $response = Http::get($url, [
            'part' => 'snippet',
            'channelId' => $channelId,
            'eventType' => 'live',
            'type' => 'video',
            'key' => $apiKey
        ]);

        if ($response->failed()) {
            return response('YouTube API Error', 500);
        }

        $data = $response->json();

        // যদি ভিডিও পাওয়া যায়
        if (!empty($data['items']) && isset($data['items'][0]['id']['videoId'])) {
            $videoId = $data['items'][0]['id']['videoId'];

            $html = view('youtube.live', compact('videoId'))->render();

            return response($html)->header('Content-Type', 'text/html');
        }

        return response('No live video found.', 404);
    }
}

