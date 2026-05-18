<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET ALL STUDENTS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $students = Student::latest()->get();

        return response()->json([

            'success' => true,

            'data' => $students
        ]);
    }


    public function admittedInquiryIds()
    {
        return Student::whereNotNull('inquiry_id')
            ->pluck('inquiry_id');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE STUDENT
    |--------------------------------------------------------------------------
    */
public function store(Request $request)
{
    $validated = $request->validate([

        'full_name' => 'required',

        'mobile' => 'required',

        'admission_date' => 'required',
    ]);

    /*
    |--------------------------------------------------------------------------
    | STUDENT PHOTO
    |--------------------------------------------------------------------------
    */

    $studentPhotoPath = null;

    if ($request->hasFile('student_photo')) {

        $studentPhotoPath =
            $request->file('student_photo')
                ->store(
                    'students',
                    'public'
                );
    }

    /*
    |--------------------------------------------------------------------------
    | AADHAR PHOTO
    |--------------------------------------------------------------------------
    */

    $aadharPhotoPath = null;

    if ($request->hasFile('aadhar_photo')) {

        $aadharPhotoPath =
            $request->file('aadhar_photo')
                ->store(
                    'students',
                    'public'
                );
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE STUDENT
    |--------------------------------------------------------------------------
    */

    $student = Student::create([

    'inquiry_id' =>
    $request->inquiry_id,

        'full_name' =>
            $request->full_name,

        'mobile' =>
            $request->mobile,

        'whatsapp' =>
            $request->whatsapp,

        'email' =>
            $request->email,

        'college_school' =>
            $request->college_school,

        'current_class' =>
            $request->current_class,

        'interested_courses' =>
            json_decode(
                $request->interested_courses,
                true
            ),

        'referred_by' =>
            $request->referred_by,

        'inquiry_date' =>
            $request->inquiry_date,

        'parent_phone' =>
            $request->parent_phone,

        'father_occupation' =>
            $request->father_occupation,

        'student_photo' =>
            $studentPhotoPath,

        'aadhar_photo' =>
            $aadharPhotoPath,

        'admission_date' =>
            $request->admission_date,

        'status' =>
            $request->status ?? 'active',
    ]);

    return response()->json([

        'success' => true,

        'message' =>
            'Student admitted successfully',

        'data' => $student

    ], 201);
}
}