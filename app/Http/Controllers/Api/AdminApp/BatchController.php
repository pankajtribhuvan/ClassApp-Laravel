<?php

namespace App\Http\Controllers\Api\AdminApp;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BatchController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | BATCH LIST
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $batches = Batch::with('teacher')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $batches
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE BATCH
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'batch_name' => 'required',

            'teacher_id' => 'required',

            'course_name' => 'required',

            'start_time' => 'required',

            'end_time' => 'required',
        ]);

        $batch = Batch::create([

            'uuid' => Str::uuid(),

            'batch_name' => $request->batch_name,

            'teacher_id' => $request->teacher_id,

            'course_name' => $request->course_name,

            'start_time' => $request->start_time,

            'end_time' => $request->end_time,

            'days' => $request->days,

            'room_no' => $request->room_no,

            'status' => $request->status ?? 'active',
        ]);

        return response()->json([

            'success' => true,

            'message' => 'Batch Added Successfully',

            'data' => $batch
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW BATCH
    |--------------------------------------------------------------------------
    */

    public function show($uuid)
    {
        $batch = Batch::with('teacher')
            ->where('uuid', $uuid)
            ->first();

        if (!$batch) {

            return response()->json([

                'success' => false,

                'message' => 'Batch Not Found'
            ], 404);
        }

        return response()->json([

            'success' => true,

            'data' => $batch
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE BATCH
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $uuid
    ) {

        $batch = Batch::where(
            'uuid',
            $uuid
        )->first();

        if (!$batch) {

            return response()->json([

                'success' => false,

                'message' => 'Batch Not Found'
            ], 404);
        }

        $batch->update([

            'batch_name' => $request->batch_name,

            'teacher_id' => $request->teacher_id,

            'course_name' => $request->course_name,

            'start_time' => $request->start_time,

            'end_time' => $request->end_time,

            'days' => $request->days,

            'room_no' => $request->room_no,

            'status' => $request->status,
        ]);

        return response()->json([

            'success' => true,

            'message' => 'Batch Updated Successfully',

            'data' => $batch
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE BATCH
    |--------------------------------------------------------------------------
    */

    public function destroy($uuid)
    {
        $batch = Batch::where(
            'uuid',
            $uuid
        )->first();

        if (!$batch) {

            return response()->json([

                'success' => false,

                'message' => 'Batch Not Found'
            ], 404);
        }

        $batch->delete();

        return response()->json([

            'success' => true,

            'message' => 'Batch Deleted Successfully'
        ]);
    }

   public function assignStudents(
    Request $request,
    $uuid
) {

    $batch = Batch::where(
        'uuid',
        $uuid
    )->first();

    if (!$batch) {

        return response()->json([
            'success' => false,
            'message' => 'Batch Not Found'
        ], 404);
    }

    // Clear old students
    DB::table('batch_students')
        ->where('batch_uuid', $uuid)
        ->delete();

    // Insert selected students
    foreach ($request->student_uuids as $studentUuid) {

        DB::table('batch_students')
            ->insert([

                'batch_uuid' => $uuid,

                'student_uuid' => $studentUuid,

                'created_at' => now(),

                'updated_at' => now(),
            ]);
    }

    return response()->json([

        'success' => true,

        'message' =>
            'Students Assigned Successfully'
    ]);
}

public function assignedStudents($uuid)
{
    $students = DB::table('batch_students')
    ->join(
        'students',
        'batch_students.student_uuid',
        '=',
        'students.uuid'
    )
    ->where('batch_students.batch_uuid', $uuid)
    ->where('students.status', 'active')
    ->select('students.*')
    ->get();
    
    /*
    $students = DB::table('batch_students')
        ->join(
            'students',
            'batch_students.student_uuid',
            '=',
            'students.uuid'
        )
        ->where(
            'batch_students.batch_uuid',
            $uuid
        )
        ->select(
            'students.*'
        )
        ->get();
    */
        
    return response()->json([
        'success' => true,
        'data' => $students
    ]);
    }
}