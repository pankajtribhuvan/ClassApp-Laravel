<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class CredentialController extends Controller
{
    // //
    // public function students(Request $request)
    // {
    //     $query = Student::query();

    //     if ($request->filled('search')) {

    //         $search = $request->search;

    //         $query->where(function ($q) use ($search) {

    //             $q->where('full_name', 'like', "%{$search}%")
    //             ->orWhere('mobile', 'like', "%{$search}%")
    //             ->orWhere('email', 'like', "%{$search}%");

    //         });

    //     }

    //     if ($request->filled('course_uuid')) {

    //         $query->where('course_uuid', $request->course_uuid);

    //     }

    //     if ($request->filled('status')) {

    //         $query->where('status', $request->status);

    //     }

    //     $students = $query
    //         ->orderBy('full_name')
    //         ->get([
    //             'uuid',
    //             'full_name',
    //             'mobile',
    //             'email',
    //             'course_uuid',
    //             'course_name',
    //             'status'
    //         ]);

    //     return response()->json([
    //         'success' => true,
    //         'data' => $students
    //     ]);

    // }

    public function resetStudentPassword(Request $request)
    {
        $request->validate([
            'uuid' => 'required',
            'password' => 'required|min:8',
        ]);

        $student = Student::where('uuid', $request->uuid)->first();

        if (!$student) {

            return response()->json([
                'success' => false,
                'message' => 'Student not found.'
            ],404);

        }

        $student->password = Hash::make($request->password);

        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.'
        ]);

    }

    public function resetTeacherPassword(Request $request)
    {
        $request->validate([
            'uuid' => 'required',
        ]);

        $teacher = Teacher::where('uuid', $request->uuid)->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found.',
            ], 404);
        }

        $teacher->password = Hash::make('Teacher@123');
        $teacher->save();

        return response()->json([
            'success' => true,
            'message' => 'Teacher password reset successfully.',
        ]);
    }
}
