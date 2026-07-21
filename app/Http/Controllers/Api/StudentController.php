<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function activeStudents()
    {
        $students = Student::active()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $students,
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

            // 'interested_courses' =>
            //     json_decode(
            //         $request->interested_courses,
            //         true
            //     ),

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

                'course_uuid' =>
    $request->course_uuid,

'course_name' =>
    $request->course_name,

'total_fees' =>
    $request->total_fees ?? 0,

'paid_fees' =>
    $request->paid_fees ?? 0,

'balance_fees' =>
    $request->balance_fees ?? 0,



'installments' =>
    $request->installments ?? 1,

'admission_no' =>
    $request->admission_no,
    
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

    /*
|--------------------------------------------------------------------------
| DELETE STUDENT
|--------------------------------------------------------------------------
*/

    public function destroy($uuid)
    {
        try {

            $student = Student::where(
                'uuid',
                $uuid
            )->first();

            if (!$student) {

                return response()->json([

                    'success' => false,

                    'message' => 'Student not found'

                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE STUDENT PHOTO
            |--------------------------------------------------------------------------
            */

            if (
                $student->student_photo &&
                Storage::disk('public')->exists(
                    $student->student_photo
                )
            ) {

                Storage::disk('public')->delete(
                    $student->student_photo
                );
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE AADHAR PHOTO
            |--------------------------------------------------------------------------
            */

            if (
                $student->aadhar_photo &&
                Storage::disk('public')->exists(
                    $student->aadhar_photo
                )
            ) {

                Storage::disk('public')->delete(
                    $student->aadhar_photo
                );
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE STUDENT
            |--------------------------------------------------------------------------
            */

            $student->delete();

            return response()->json([

                'success' => true,

                'message' =>
                    'Student deleted successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ], 500);
        }
    }

    /*
|--------------------------------------------------------------------------
| UPDATE STUDENT
|--------------------------------------------------------------------------
*/

    public function update(Request $request,$uuid)
    {
        $student = Student::where(
            'uuid',
            $uuid
        )->first();

        if (!$student) {

            return response()->json([

                'success' => false,

                'message' => 'Student not found'

            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | STUDENT PHOTO
        |--------------------------------------------------------------------------
        */

        $studentPhotoPath =
            $student->student_photo;

        if ($request->hasFile(
            'student_photo'
        )) {

            if (
                $student->student_photo &&
                Storage::disk('public')->exists(
                    $student->student_photo
                )
            ) {

                Storage::disk('public')->delete(
                    $student->student_photo
                );
            }

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

        $aadharPhotoPath =
            $student->aadhar_photo;

        if ($request->hasFile(
            'aadhar_photo'
        )) {

            if (
                $student->aadhar_photo &&
                Storage::disk('public')->exists(
                    $student->aadhar_photo
                )
            ) {

                Storage::disk('public')->delete(
                    $student->aadhar_photo
                );
            }

            $aadharPhotoPath =
                $request->file('aadhar_photo')
                    ->store(
                        'students',
                        'public'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        $student->update([

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

            // 'interested_courses' =>
            //     json_decode(
            //         $request->interested_courses,
            //         true
            //     ),

            'referred_by' =>
                $request->referred_by,

            'parent_phone' =>
                $request->parent_phone,

            'father_occupation' =>
                $request->father_occupation,

            'student_photo' =>
                $studentPhotoPath,

            'aadhar_photo' =>
                $aadharPhotoPath,

                'course_uuid' =>
                $request->course_uuid,

            'course_name' =>
                $request->course_name,

            'total_fees' =>
                $request->total_fees ?? 0,

            'paid_fees' =>
                $request->paid_fees ?? 0,

            'balance_fees' =>
                $request->balance_fees ?? 0,

            'installments' =>
                $request->installments ?? 1,

 
            'admission_no' =>
                $request->admission_no,

                        'status' =>
                            $request->status,
                    ]);

        return response()->json([

            'success' => true,

            'message' =>
                'Student updated successfully',

            'data' => $student
        ]);
    }

    public function show($uuid)
    {
        $student = Student::where(
            'uuid',
            $uuid
        )->first();

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }



    public function updateStatus(Request $request, $uuid)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,completed,archived,cancelled',
        ]);

        $student = Student::where('uuid', $uuid)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }

        $student->status = $validated['status'];
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Student status updated successfully.',
            'data' => $student,
        ]);
    }

    

}