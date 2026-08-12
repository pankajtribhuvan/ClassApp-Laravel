<?php

namespace App\Http\Controllers\Api\StudentApp;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        /** @var Student $student */
        $student = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Get active batch
        |--------------------------------------------------------------------------
        */

        $batch = $student->batches()
            ->where('batches.status', 'active')
            ->first();

        if (!$batch) {
            return response()->json([
                'success' => true,
                'data' => [
                    'batch' => null,
                    'summary' => [
                        'total_days' => 0,
                        'present_days' => 0,
                        'absent_days' => 0,
                        'percentage' => 0,
                    ],
                    'records' => [],
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Get attendance records
        |--------------------------------------------------------------------------
        */

        $attendanceRecords = Attendance::where(
            'batch_uuid',
            $batch->uuid
        )
            ->orderByDesc('attendance_date')
            ->get();

        $records = [];
        $presentDays = 0;

        foreach ($attendanceRecords as $attendance) {

            $presentStudents =
                $attendance->present_student_uuids ?? [];

            /*
             * Handle JSON string as well as already decoded array.
             */
            if (is_string($presentStudents)) {
                $presentStudents =
                    json_decode($presentStudents, true) ?? [];
            }

            $isPresent = in_array(
                $student->uuid,
                $presentStudents
            );

            if ($isPresent) {
                $presentDays++;
            }

            $records[] = [
                'uuid' => $attendance->uuid,
                // 'date' => $attendance->attendance_date,
                'date' => $attendance->attendance_date
                ? $attendance->attendance_date->format('Y-m-d')
                : null,
                'status' => $isPresent
                    ? 'present'
                    : 'absent',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalDays = count($records);

        $absentDays =
            $totalDays - $presentDays;

        $percentage = $totalDays > 0
            ? round(
                ($presentDays / $totalDays) * 100,
                2
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'data' => [

                'batch' => [
                    'uuid' => $batch->uuid,
                    'batch_name' => $batch->batch_name,
                    'course_name' => $batch->course_name,
                ],

                'summary' => [
                    'total_days' => $totalDays,
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'percentage' => $percentage,
                ],

                'records' => $records,
            ],
        ]);
    }
}
