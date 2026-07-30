<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Student;

class CourseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET ALL COURSES
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $courses = Course::latest()->get();
        
        return response()->json([

            'success' => true,

            'data' => $courses
        ]);
    }

    public function activeCourses()
{
    $courses = Course::where('status', 'active')
        ->orderBy('course_name')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $courses
    ]);
}

    /*
    |--------------------------------------------------------------------------
    | STORE COURSE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

        
            'course_name' => 'required',

            'duration' => 'required',

            'fees' => 'required|numeric',

            'installments' => 'required|integer',
        ]);

        $course = Course::create([

    'uuid' => Str::uuid(),

    'course_name' =>
        $request->course_name,

    'description' =>
        $request->description,

    'duration' =>
        $request->duration,

    'fees' =>
        $request->fees,

    'installments' =>
        $request->installments,

    'course_mode' =>
        $request->course_mode,

    'status' =>
        $request->status ?? 'active',

        
]);

        return response()->json([

            'success' => true,

            'message' => 'Course created successfully',

            'data' => $course

        ], 201);
    }
/*
|--------------------------------------------------------------------------
| UPDATE COURSE
|--------------------------------------------------------------------------
*/

public function update(Request $request, $uuid)
{
    $course = Course::where(
        'uuid',
        $uuid
    )->first();

    if (!$course) {

        return response()->json([

            'success' => false,

            'message' => 'Course not found'

        ], 404);
    }

    $course->update([

        'course_name' =>
            $request->course_name,

        'description' =>
            $request->description,

        'duration' =>
            $request->duration,

        'fees' =>
            $request->fees,

        'installments' =>
            $request->installments,

        'course_mode' =>
            $request->course_mode,

        'status' =>
            $request->status,
    ]);

    return response()->json([

        'success' => true,

        'message' =>
            'Course updated successfully',

        'data' => $course
    ]);
}
/*
    |--------------------------------------------------------------------------
    | DELETE COURSE WITH STUDENT DEPENDENCY CHECK
    |--------------------------------------------------------------------------
    */
/*
    |--------------------------------------------------------------------------
    | DELETE COURSE WITH STUDENT ENROLLMENT CHECK (STRICT BLOCK)
    |--------------------------------------------------------------------------
    */

    public function destroy($uuid)
{
    $course = Course::where('uuid', $uuid)->firstOrFail();

    $studentCount = Student::where('course_uuid', $uuid)->count();

    if ($studentCount > 0) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot delete course. Students are enrolled in this course.'
        ], 400);
    }

    $course->delete();

    return response()->json([
        'success' => true,
        'message' => 'Course deleted successfully.'
    ]);
}


}