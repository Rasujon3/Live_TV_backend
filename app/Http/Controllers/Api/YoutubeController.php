<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class YoutubeController extends Controller
{
    public function showLiveVideo(Request $request)
    {
        $channelId = $request->channelId;

        if (!$channelId) {
            return response('Channel ID is required.', 400);
        }

        $cacheKey = 'live_video_' . $channelId;

        if (Cache::has($cacheKey)) {
            $videoId = Cache::get($cacheKey);

            if ($videoId) {
                $html = view('youtube.live', compact('videoId'))->render();
            } else {
                $html = view('youtube.error', ['message' => 'Nothing found from cache.'])->render();
            }

            return response($html)->header('Content-Type', 'text/html');
        }

        try {
            $apiKeys = [
                config('services.youtube.api_key_1'),
                config('services.youtube.api_key_2'),
                config('services.youtube.api_key_3'),
                config('services.youtube.api_key_4'),
                config('services.youtube.api_key_5'),
            ];

            $apiKey = $apiKeys[array_rand($apiKeys)];

            $url = 'https://www.googleapis.com/youtube/v3/search';

            $response = Http::get($url, [
                'part' => 'snippet',
                'channelId' => $channelId,
                'eventType' => 'live',
                'type' => 'video',
                'key' => $apiKey
            ]);

            if ($response->failed() || isset($response->json()['error'])) {
                Log::error('API Get Error : ', [
                    'message' => $response->json()['error']['message'] ? $response->json()['error'] : '',
                    'channelId' => $channelId,
                    'api_key' => $apiKey
                ]);

                $errorMessage = 'Something went wrong!!';
                $html = view('youtube.error', ['message' => $errorMessage])->render();
                return response($html)->header('Content-Type', 'text/html');
            }

            $data = $response->json();

            if (!empty($data['items']) && isset($data['items'][0]['id']['videoId'])) {
                $videoId = $data['items'][0]['id']['videoId'];

                Cache::put($cacheKey, $videoId, now()->addMinutes(10));

                $html = view('youtube.live', compact('videoId'))->render();
                return response($html)->header('Content-Type', 'text/html');
            }

            Log::error('Video not found : ', [
                'message' => 'Nothing found from API.',
                'channelId' => $channelId,
            ]);

            Cache::put($cacheKey, null, now()->addMinutes(10));

            $html = view('youtube.error', ['message' => 'Nothing found.'])->render();
            return response($html)->header('Content-Type', 'text/html');
        } catch (Exception $e) {
            Log::error('Error in get File : ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $html = view('youtube.error', ['message' => 'Something went wrong!!!'])->render();
            return response($html)->header('Content-Type', 'text/html');
        }
    }
}

