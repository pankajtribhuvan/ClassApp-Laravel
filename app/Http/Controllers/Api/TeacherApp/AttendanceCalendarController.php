<?php

namespace App\Http\Controllers\Api\TeacherApp;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceCalendarController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'year' => [
                'required',
                'integer',
                'min:2024',
                'max:2100',
            ],

            'month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
            ],
        ]);

        $batchUuid = $request->route('batchUuid');

        if (!preg_match(
            '/^[0-9a-fA-F-]{36}$/',
            $batchUuid
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid batch UUID.',
            ], 422);
        }
        
        $teacher = $request->user();

        // ------------------------------------------------------
        // Find requested batch
        // ------------------------------------------------------

        $batch = Batch::where(
            'uuid',
            // $validated['batch_uuid']
            $batchUuid
        )->first();

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found.',
            ], 404);
        }

        // ------------------------------------------------------
        // SECURITY:
        // Teacher can only access assigned batches.
        // ------------------------------------------------------

        if ((int) $batch->teacher_id !== (int) $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this batch.',
            ], 403);
        }

        // ------------------------------------------------------
        // Total students in this batch
        // ------------------------------------------------------

        $totalStudents = DB::table('batch_students')
            ->where(
                'batch_uuid',
                $batch->uuid
            )
            ->count();

        // ------------------------------------------------------
        // Monthly attendance
        // ------------------------------------------------------

        $attendances = Attendance::where(
                'batch_uuid',
                $batch->uuid
            )
            ->whereYear(
                'attendance_date',
                $validated['year']
            )
            ->whereMonth(
                'attendance_date',
                $validated['month']
            )
            ->orderBy(
                'attendance_date',
                'asc'
            )
            ->get();

        // ------------------------------------------------------
        // Prepare calendar data
        // ------------------------------------------------------

        $calendarData = $attendances->map(
            function ($attendance) use (
                $totalStudents
            ) {
                $presentStudents =
                    $attendance
                        ->present_student_uuids ?? [];

                $presentCount =
                    count($presentStudents);

                $absentCount =
                    max(
                        0,
                        $totalStudents -
                        $presentCount
                    );

                return [
                    'attendance_uuid' =>
                        $attendance->uuid,

                    'date' =>
                        $attendance
                            ->attendance_date
                            ->format('Y-m-d'),

                    'present_count' =>
                        $presentCount,

                    'absent_count' =>
                        $absentCount,

                    'total_students' =>
                        $totalStudents,

                    'marked_by_type' =>
                        $attendance
                            ->marked_by_type,

                    'marked_by_uuid' =>
                        $attendance
                            ->marked_by_uuid,
                ];
            }
        )->values();

        // ------------------------------------------------------
        // Response
        // ------------------------------------------------------

        return response()->json([
            'success' => true,

            'data' => [
                'batch' => [
                    'uuid' =>
                        $batch->uuid,

                    'batch_name' =>
                        $batch->batch_name,

                    'course_name' =>
                        $batch->course_name,
                ],

                'year' =>
                    (int) $validated['year'],

                'month' =>
                    (int) $validated['month'],

                'total_students' =>
                    $totalStudents,

                'attendance_days' =>
                    $calendarData->count(),

                'records' =>
                    $calendarData,
            ],
        ], 200);
    }
}