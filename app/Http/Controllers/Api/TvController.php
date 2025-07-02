<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tv;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TvController extends Controller
{
    public function index()
    {
        try {
            $tvs = Tv::where('status', 1)->get();

            return response()->json([
                'message' => 'TV list retrieved successfully',
                'data' => $tvs
            ]);
        } catch (Exception $e) {

            // Log the error
            Log::error('Error in retrieving TV: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'statusCode' => 500,
                'message' => 'Something went wrong!!!',
                'data' => []
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'channel_id' => 'required|string',
            'file' => 'required|file|image:jpg|image:png',
            'status' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid',
                'data' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file upload
            if ($request->hasFile('file')) {
                $filePath = $this->storeFile($request->file('file'));
                $img_url = $filePath ?? '';
            }

            $tv = Tv::create([
                'name' => $request->name,
                'channel_id' => $request->channel_id,
                'img_url' => $img_url,
                'status' => 1,
            ]);

            return response()->json([
                'status' => true,
                'statusCode' => 201,
                'message' => 'TV created successfully',
                'data' => $tv
            ], 201);
        } catch (Exception $e) {

            // Log the error
            Log::error('Error in storing TV: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'statusCode' => 500,
                'message' => 'Something went wrong!!!',
                'data' => []
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
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:tvs,id',
            'name' => 'nullable|string',
            'channel_id' => 'nullable|string',
            'file' => 'nullable|file|image:jpg|image:png|image:jpeg',
            'status' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid',
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $tv = Tv::findOrFail($request->id);

            $requestData = $request->except(['file', 'id']);
            $requestData['name'] = $requestData['name'] ?? $tv->name;
            $requestData['channel_id'] = $requestData['channel_id'] ?? $tv->title;
            $requestData['status'] = $requestData['status'] ?? $tv->singer_name;
            $requestData['img_url'] = $tv->img_url;
            $requestData['updated_at'] = now();

            // Handle file upload
            if ($request->hasFile('file')) {
                $filePath = $this->updateFile($request->file('file'), $tv);
                $requestData['img_url'] = $filePath ?? '';
            }

            $tv->update($requestData);

            return response()->json([
                'message' => 'TV updated successfully',
                'data' => $tv
            ]);
        } catch (Exception $e) {

            // Log the error
            Log::error('Error in updating TV: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'statusCode' => 500,
                'message' => 'Something went wrong!!!',
                'data' => []
            ], 500);
        }
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

    private function storeFile($file)
    {
        // Define the directory path
        $filePath = 'files/music/mp3';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('music_', true) . '.' . $file->getClientOriginalExtension();

        // Move the file to the destination directory
        $file->move($directory, $fileName);

        // path & file name in the database
        # $path = $filePath . '/' . $fileName;
        $path = $fileName;
        return $path;
    }
    public function getMusic($filename)
    {
        try {
            $path = public_path('files/music/mp3/' . $filename);

            if (!file_exists($path)) {
                return $this->sendResponse(false, '404, File not found.', []);
            }

            return response()->file($path);
        } catch (Exception $e) {

            // Log the error
            Log::error('Error in get File: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->sendResponse(false, 'Something went wrong!!!', [], 500);
        }
    }
    private function updateFile($file, $data)
    {
        // Define the directory path
        $filePath = 'files/music/mp3';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('music_', true) . '.' . $file->getClientOriginalExtension();

        // Delete the old file if it exists
        $this->deleteOldFile($data);

        // Move the new file to the destination directory
        $file->move($directory, $fileName);

        // Store path & file name in the database
        # $path = $filePath . '/' . $fileName;
        $path = $fileName;
        return $path;
    }
    private function deleteOldFile($data)
    {
        if (!empty($data->img_url)) {
            $filePath = 'files/music/mp3';
            $directory = $data->img_url;
            $path = $filePath . '/' . $directory;

            $oldFilePath = public_path($path); // Use without prepending $filePath
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath); // Delete the old file
                return true;
            } else {
                Log::warning('Old file not found for deletion', ['path' => $oldFilePath]);
                return false;
            }
        }
    }
}
