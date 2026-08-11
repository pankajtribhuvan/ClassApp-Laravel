<?php

namespace App\Http\Controllers\Api\TeacherApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Teacher;

class ProfileController extends Controller
{
    /**
     * Get logged-in teacher profile
     */
    public function show(Request $request)
    {
        $teacher = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'uuid' => $teacher->uuid,
                'full_name' => $teacher->full_name,
                'mobile' => $teacher->mobile,
                'whatsapp' => $teacher->whatsapp,
                'email' => $teacher->email,
                'qualification' => $teacher->qualification,
                'specialization' => $teacher->specialization,
                'joining_date' => $teacher->joining_date,
                'salary' => $teacher->salary,
                'photo' => $teacher->photo,
                'address' => $teacher->address,
                'status' => $teacher->status,
            ],
        ]);
    }

    /**
     * Update logged-in teacher profile
     */
    public function update(Request $request)
    {
        $teacher = $request->user();

        $validator = Validator::make(
            $request->all(),
            [
                'full_name' => [
                    'sometimes',
                    'string',
                    'max:255',
                ],

                'mobile' => [
                    'sometimes',
                    'string',
                    'max:20',
                ],

                'whatsapp' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'email' => [
                    'sometimes',
                    'email',
                    'max:255',
                    'unique:teachers,email,' . $teacher->id,
                ],

                'qualification' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'specialization' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'address' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $teacher->fill(
            $request->only([
                'full_name',
                'mobile',
                'whatsapp',
                'email',
                'qualification',
                'specialization',
                'address',
            ])
        );

        $teacher->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'uuid' => $teacher->uuid,
                'full_name' => $teacher->full_name,
                'mobile' => $teacher->mobile,
                'whatsapp' => $teacher->whatsapp,
                'email' => $teacher->email,
                'qualification' => $teacher->qualification,
                'specialization' => $teacher->specialization,
                'joining_date' => $teacher->joining_date,
                'salary' => $teacher->salary,
                'photo' => $teacher->photo,
                'address' => $teacher->address,
                'status' => $teacher->status,
            ],
        ]);
    }

    /**
     * Change logged-in teacher password
     */
    public function changePassword(Request $request)
    {
        $teacher = $request->user();

        $validator = Validator::make(
            $request->all(),
            [
                'current_password' => [
                    'required',
                    'string',
                ],

                'new_password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Hash::check(
            $request->current_password,
            $teacher->password
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $teacher->password = Hash::make(
            $request->new_password
        );

        $teacher->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }
}