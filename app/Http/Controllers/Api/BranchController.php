<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchContactInfo;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index()
    {
        try {
            $branches = Branch::with(['contactInfos', 'ratings'])->get();

            return response()->json([
                'status' => true,
                'message' => 'Branch list fetched successfully.',
                'data' => $branches,
            ]);
	    } catch(Exception $e) {

            Log::error('Error in fetching Branch data: ' , [
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

        // Decode JSON string to array if needed
        if (!empty($requestData['contact_infos']) && is_string($requestData['contact_infos'])) {
            $requestData['contact_infos'] = json_decode($requestData['contact_infos'], true);
        }

        $validator = Validator::make($requestData, [
            'name' => 'required|string|max:191',
            'location_url' => 'required|url',
            'contact_infos' => 'nullable|array|min:1',
            'contact_infos.*' => 'required|string|max:191',
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
            $branch = Branch::create([
                'name' => $requestData['name'],
                'location_url' => $requestData['location_url'],
            ]);

            if (!empty($requestData['contact_infos'])) {
                foreach ($requestData['contact_infos'] as $contact) {
                    BranchContactInfo::create([
                        'branch_id' => $branch->id,
                        'contact_no' => $contact,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Branch created successfully.',
                'data' => $branch->load('contactInfos'),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error in storing Branch: ' , [
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
