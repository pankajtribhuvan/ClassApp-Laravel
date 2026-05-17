<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class InquiryController extends Controller
{
    /**
     * Display a listing of all inquiries.
     * GET /api/inquiries
     */
    public function index(): JsonResponse
    {
        try {
            $inquiries = Inquiry::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data'    => $inquiries
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load inquiries list records.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created inquiry in storage.
     * POST /api/inquiries
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'full_name'          => 'required|string|max:255',
            'mobile'             => 'required|string|max:20',
            'whatsapp'           => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:255',
            'college_school'     => 'nullable|string|max:255',
            'current_class'      => 'nullable|string|max:50',
            'interested_courses' => 'required|array', 
            'referred_by'        => 'nullable|string|max:255',
            'inquiry_date'       => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error encountered on parameters.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $inquiry = new Inquiry();
            $inquiry->full_name = $request->input('full_name');
            $inquiry->mobile = $request->input('mobile');
            $inquiry->whatsapp = $request->input('whatsapp');
            $inquiry->email = $request->input('email');
            $inquiry->college_school = $request->input('college_school');
            $inquiry->current_class = $request->input('current_class');
            
            // --- FIXED: DO NOT json_encode() HERE! PASS THE RAW ARRAY DIRECTLY ---
            // Laravel handles the JSON translation into your database table automatically
            $inquiry->interested_courses = $request->input('interested_courses');
            
            $inquiry->referred_by = $request->input('referred_by');
            $inquiry->inquiry_date = $request->input('inquiry_date');
            $inquiry->save();

            return response()->json([
                'success' => true,
                'message' => 'Inquiry registered cleanly into system backend records!',
                'data'    => $inquiry
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An internal database insertion transaction error crashed operations.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified inquiry in storage.
     * PUT /api/inquiries/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'full_name'          => 'required|string|max:255',
            'mobile'             => 'required|string|max:20',
            'whatsapp'           => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:255',
            'college_school'     => 'nullable|string|max:255',
            'current_class'      => 'nullable|string|max:50',
            'interested_courses' => 'required|array', 
            'referred_by'        => 'nullable|string|max:255',
            'inquiry_date'       => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation formatting rejections present.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $inquiry = Inquiry::find($id);

            if (!$inquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Target database index key item row record missing.'
                ], 404);
            }

            $inquiry->full_name = $request->input('full_name');
            $inquiry->mobile = $request->input('mobile');
            $inquiry->whatsapp = $request->input('whatsapp');
            $inquiry->email = $request->input('email');
            $inquiry->college_school = $request->input('college_school');
            $inquiry->current_class = $request->input('current_class');
            
            // --- FIXED: PASS THE RAW ARRAY DIRECTLY ---
            $inquiry->interested_courses = $request->input('interested_courses');
            
            $inquiry->referred_by = $request->input('referred_by');
            $inquiry->inquiry_date = $request->input('inquiry_date');
            $inquiry->save();

            return response()->json([
                'success' => true,
                'message' => 'Inquiry fields updated perfectly!',
                'data'    => $inquiry
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An operational failure exception blocked record modifications.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified inquiry from storage.
     * DELETE /api/inquiries/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $inquiry = Inquiry::find($id);

            if (!$inquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Target database index selection element row not found.'
                ], 404);
            }

            $inquiry->delete();

            return response()->json([
                'success' => true,
                'message' => 'Inquiry record cleared successfully.'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed processing target deletion sequence transactions.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}