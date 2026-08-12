<?php

namespace App\Http\Controllers\Api\StudentApp;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceCalendarController extends Controller
{
    public function index(Request $request)
    {
        /** @var Student $student */
        $student = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Validate Month
        |--------------------------------------------------------------------------
        */

        $month = $request->query(
            'month',
            now()->format('Y-m')
        );

        try {
            $monthDate = Carbon::createFromFormat(
                'Y-m',
                $month
            )->startOfMonth();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid month format. Use YYYY-MM.',
            ], 422);
        }

        $startDate = $monthDate->copy()->startOfMonth();
        $endDate = $monthDate->copy()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Get Active Batch
        |--------------------------------------------------------------------------
        */

        $batch = $student->batches()
            ->where('batches.status', 'active')
            ->first();

        if (!$batch) {
            return response()->json([
                'success' => true,
                'data' => [
                    'month' => $month,
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
        | Get Attendance For Selected Month
        |--------------------------------------------------------------------------
        */

        $attendanceRecords = Attendance::where(
            'batch_uuid',
            $batch->uuid
        )
            ->whereBetween(
                'attendance_date',
                [
                    $startDate->format('Y-m-d'),
                    $endDate->format('Y-m-d'),
                ]
            )
            ->orderBy('attendance_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Build Calendar Records
        |--------------------------------------------------------------------------
        */

        $records = [];
        $presentDays = 0;

        foreach ($attendanceRecords as $attendance) {

            $presentStudents =
                $attendance->present_student_uuids ?? [];

            if (is_string($presentStudents)) {
                $presentStudents =
                    json_decode(
                        $presentStudents,
                        true
                    ) ?? [];
            }

            $isPresent = in_array(
                $student->uuid,
                $presentStudents
            );

            if ($isPresent) {
                $presentDays++;
            }

            $records[] = [
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

                'month' => $month,

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