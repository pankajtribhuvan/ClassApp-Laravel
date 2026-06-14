<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TEACHER LIST
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $teachers = Teacher::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE TEACHER
    |--------------------------------------------------------------------------
    */

public function store(Request $request)
{
    $request->validate([

        'full_name' => 'required',

        'mobile' => 'required',
    ]);

    $photoName = null;

    if ($request->hasFile('photo')) {

        $photoName =
            time() . '.' .
            $request->photo->extension();

        $request->photo->storeAs(
            'teachers',
            $photoName,
            'public'
        );
    }

    $teacher = Teacher::create([

        'uuid' => Str::uuid(),

        'full_name' => $request->full_name,

        'mobile' => $request->mobile,

        'whatsapp' => $request->whatsapp,

        'email' => $request->email,

        'qualification' => $request->qualification,

        'specialization' => $request->specialization,

        'joining_date' => $request->joining_date,

        'salary' => $request->salary,

        'photo' => $photoName,

        'address' => $request->address,

        'status' => $request->status ?? 'active',
    ]);

    return response()->json([

        'success' => true,

        'message' => 'Teacher Added Successfully',

        'data' => $teacher
    ]);
}
    /*
    |--------------------------------------------------------------------------
    | SHOW TEACHER
    |--------------------------------------------------------------------------
    */

    public function show($uuid)
    {
        $teacher = Teacher::where(
            'uuid',
            $uuid
        )->first();

        if (!$teacher) {

            return response()->json([

                'success' => false,

                'message' => 'Teacher Not Found'
            ], 404);
        }

        return response()->json([

            'success' => true,

            'data' => $teacher
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE TEACHER
    |--------------------------------------------------------------------------
    */

public function update(
    Request $request,
    $uuid
) {

    $teacher = Teacher::where(
        'uuid',
        $uuid
    )->first();

    if (!$teacher) {

        return response()->json([

            'success' => false,

            'message' => 'Teacher Not Found'
        ], 404);
    }

    $photoName = $teacher->photo;

    if ($request->hasFile('photo')) {

        $photoName =
            time() . '.' .
            $request->photo->extension();

        $request->photo->storeAs(
            'teachers',
            $photoName,
            'public'
        );
    }

    $teacher->update([

        'full_name' => $request->full_name,

        'mobile' => $request->mobile,

        'whatsapp' => $request->whatsapp,

        'email' => $request->email,

        'qualification' => $request->qualification,

        'specialization' => $request->specialization,

        'joining_date' => $request->joining_date,

        'salary' => $request->salary,

        'photo' => $photoName,

        'address' => $request->address,

        'status' => $request->status,
    ]);

    return response()->json([

        'success' => true,

        'message' => 'Teacher Updated Successfully',

        'data' => $teacher
    ]);
}
    /*
    |--------------------------------------------------------------------------
    | DELETE TEACHER
    |--------------------------------------------------------------------------
    */

    public function destroy($uuid)
    {
        $teacher = Teacher::where(
            'uuid',
            $uuid
        )->first();

        if (!$teacher) {

            return response()->json([

                'success' => false,

                'message' => 'Teacher Not Found'
            ], 404);
        }

        $teacher->delete();

        return response()->json([

            'success' => true,

            'message' => 'Teacher Deleted Successfully'
        ]);
    }
}