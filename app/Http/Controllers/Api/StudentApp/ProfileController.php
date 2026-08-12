<?php

namespace App\Http\Controllers\Api\StudentApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW PROFILE
    |--------------------------------------------------------------------------
    */

    public function show(Request $request)
    {
        $student = $request->user();

        return response()->json([
            'success' => true,

            'data' => [
                'uuid' => $student->uuid,
                'full_name' => $student->full_name,
                'mobile' => $student->mobile,
                'whatsapp' => $student->whatsapp,
                'email' => $student->email,
                'college_school' => $student->college_school,
                'current_class' => $student->current_class,
                'parent_phone' => $student->parent_phone,
                'father_occupation' => $student->father_occupation,
                'student_photo' => $student->student_photo,

                // Read-only admission information
                'admission_no' => $student->admission_no,
                'admission_date' => $student->admission_date
                    ? $student->admission_date->format('Y-m-d')
                    : null,
                'course_name' => $student->course_name,
                'status' => $student->status,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PROFILE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        $student = $request->user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',

            'whatsapp' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'college_school' => [
                'nullable',
                'string',
                'max:255',
            ],

            'current_class' => [
                'nullable',
                'string',
                'max:255',
            ],

            'parent_phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'father_occupation' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $student->update([
            'full_name' => $validated['full_name'],

            'whatsapp' =>
                $validated['whatsapp'] ?? null,

            'email' =>
                $validated['email'] ?? null,

            'college_school' =>
                $validated['college_school'] ?? null,

            'current_class' =>
                $validated['current_class'] ?? null,

            'parent_phone' =>
                $validated['parent_phone'] ?? null,

            'father_occupation' =>
                $validated['father_occupation'] ?? null,
        ]);

        $student->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',

            'data' => [
                'uuid' => $student->uuid,
                'full_name' => $student->full_name,
                'mobile' => $student->mobile,
                'whatsapp' => $student->whatsapp,
                'email' => $student->email,
                'college_school' => $student->college_school,
                'current_class' => $student->current_class,
                'parent_phone' => $student->parent_phone,
                'father_occupation' => $student->father_occupation,
                'student_photo' => $student->student_photo,

                'admission_no' => $student->admission_no,
                'admission_date' => $student->admission_date
                    ? $student->admission_date->format('Y-m-d')
                    : null,
                'course_name' => $student->course_name,
                'status' => $student->status,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CHANGE PASSWORD
    |--------------------------------------------------------------------------
    */

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',

            'new_password' =>
                'required|string|min:6|confirmed',
        ]);

        $student = $request->user();

        if (!Hash::check(
            $request->current_password,
            $student->password
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $student->update([
            'password' => Hash::make(
                $request->new_password
            ),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }
}