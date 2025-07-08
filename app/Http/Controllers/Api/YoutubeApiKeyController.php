<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YouTubeApiKey;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class YoutubeApiKeyController extends Controller
{
    public function index()
    {
        try {
            $branches = YouTubeApiKey::get();

            return response()->json([
                'status' => true,
                'message' => 'API Key list fetched successfully.',
                'data' => $branches,
            ]);
        } catch(Exception $e) {

            Log::error('Error in fetching API Key data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!!!',
                'data' => [],
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'api_key' => 'required|string|unique:youtube_api_keys,api_key',
            'is_active' => 'nullable|boolean',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid',
                'data' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $youtubeApiKeys = YouTubeApiKey::create([
                'api_key' => $requestData['api_key'],
                'is_active' => $requestData['is_active'],
                'comment' => $requestData['comment'],
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'YouTubeApiKey created successfully.',
                'data' => $youtubeApiKeys,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error in storing YouTubeApiKey: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Something went wrong!!!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function activateAll()
    {
        try {
            $updated = YouTubeApiKey::where('is_active', false)->update(['is_active' => true]);

            return response()->json([
                'status' => true,
                'message' => "Total {$updated} API key(s) activated successfully.",
                'data' => [],
            ]);
        } catch (Exception $e) {
            Log::error('Error in get File : ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!!!',
                'data' => [],
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
