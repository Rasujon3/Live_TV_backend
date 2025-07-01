<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tv;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TvController extends Controller
{
    public function index()
    {
        $tvs = Tv::all();

        return response()->json([
            'message' => 'TV list retrieved successfully',
            'data' => $tvs
        ]);
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
            'message' => 'TV created successfully',
            'data' => $tv
        ], 201);
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'channel_id' => 'sometimes|required|string',
            'img_url' => 'sometimes|required|string',
            'status' => 'nullable|in:0,1',
        ]);

        $tv = Tv::findOrFail($id);

        $tv->update($request->only('name', 'channel_id', 'img_url', 'status'));

        return response()->json([
            'message' => 'TV updated successfully',
            'data' => $tv
        ]);
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
}
