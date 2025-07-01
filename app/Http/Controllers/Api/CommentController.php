<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Rating;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $offset = $request->input('offset') ?? 0;
            $limit = $request->input('limit') ?? 2;

            $comments = Comment::where('branch_id', $request->branch_id)
                ->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Comments fetched successfully.',
                'data' => $comments,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch comments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // ✅ Step 1: Validate the request
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
            'author' => 'required|string|max:191',
            'rating' => 'nullable|integer',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // ✅ Step 2: Save comment
            $comment = Comment::create([
                'branch_id' => $request->branch_id,
                'author' => $request->author,
                'comment' => $request->comment,
            ]);

            if (!empty($request->rating)) {
                // ✅ Step 3: Save ratings
                $rating = Rating::create([
                    'branch_id' => $request->branch_id,
                    'rating' => $request->rating,
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Comment added successfully.',
                'data' => $comment,
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error in fetching Comment data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!!!',
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
