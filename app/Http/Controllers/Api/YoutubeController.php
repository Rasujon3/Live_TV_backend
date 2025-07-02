<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class YoutubeController extends Controller
{
    public function showLiveVideo(Request $request)
    {
        $channelId = $request->channelId;

        if (!$channelId) {
            return response('Channel ID is required.', 400);
        }

        try {
            $apiKey = config('services.youtube.api_key');
            $url = 'https://www.googleapis.com/youtube/v3/search';

            $response = Http::get($url, [
                'part' => 'snippet',
                'channelId' => $channelId,
                'eventType' => 'live',
                'type' => 'video',
                'key' => $apiKey
            ]);

            if ($response->failed() || isset($response->json()['error'])) {
                // Log the error
                Log::error('API Get Error : ', [
                    'message' => isset($response->json()['error']['message']) ?? '',
                    'channelId' => $channelId
                ]);

                $errorMessage = 'Something went wrong!!!';
                $html = view('youtube.error', ['message' => $errorMessage])->render();
                return response($html)->header('Content-Type', 'text/html');
            }

            $data = $response->json();

            if (!empty($data['items']) && isset($data['items'][0]['id']['videoId'])) {
                $videoId = $data['items'][0]['id']['videoId'];

                $html = view('youtube.live', compact('videoId'))->render();

                return response($html)->header('Content-Type', 'text/html');
            }

            // Log the error
            Log::error('Video not found : ', [
                'message' => 'Nothing found.',
                'channelId' => $channelId
            ]);

            $html = view('youtube.error', ['message' => 'Nothing found.'])->render();
            return response($html)->header('Content-Type', 'text/html');
        } catch (Exception $e) {

            // Log the error
            Log::error('Error in get File : ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'Something went wrong!!!';
            $html = view('youtube.error', ['message' => $errorMessage])->render();
            return response($html)->header('Content-Type', 'text/html');
        }
    }
}

