<?php

namespace App\Http\Controllers\Api\StudentApp;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var Student $student */
        $student = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        $studentData = [
            'uuid' => $student->uuid,
            'full_name' => $student->full_name,
            'photo' => $student->student_photo,
            'admission_no' => $student->admission_no,
            'course_name' => $student->course_name,
        ];

        /*
        |--------------------------------------------------------------------------
        | Active Batch
        |--------------------------------------------------------------------------
        */

        $batch = $student->batches()
            ->where('batches.status', 'active')
            ->first();

        $batchData = null;
        $todayClass = [
            'is_class_today' => false,
            'day' => Carbon::now()->format('D'),
            'start_time' => null,
            'end_time' => null,
        ];

        /*
        |--------------------------------------------------------------------------
        | Attendance Defaults
        |--------------------------------------------------------------------------
        */

        $attendanceSummary = [
            'total_days' => 0,
            'present_days' => 0,
            'absent_days' => 0,
            'percentage' => 0,
        ];

        if ($batch) {

            /*
            |--------------------------------------------------------------------------
            | Batch
            |--------------------------------------------------------------------------
            */

            $days = array_values(
                array_filter(
                    array_map(
                        'trim',
                        explode(',', $batch->days ?? '')
                    )
                )
            );

            $batchData = [
                'uuid' => $batch->uuid,
                'batch_name' => $batch->batch_name,
                'course_name' => $batch->course_name,
                'start_time' => $batch->start_time,
                'end_time' => $batch->end_time,
                'days' => $days,
                'room_no' => $batch->room_no,
            ];

            /*
            |--------------------------------------------------------------------------
            | Today's Class
            |--------------------------------------------------------------------------
            */

            $today = Carbon::now()->format('D');

            $isClassToday = in_array(
                $today,
                $days
            );

            $todayClass = [
                'is_class_today' => $isClassToday,
                'day' => $today,
                'start_time' => $isClassToday
                    ? $batch->start_time
                    : null,
                'end_time' => $isClassToday
                    ? $batch->end_time
                    : null,
            ];

            /*
            |--------------------------------------------------------------------------
            | Attendance
            |--------------------------------------------------------------------------
            */

            $attendanceRecords = Attendance::where(
                'batch_uuid',
                $batch->uuid
            )
                ->orderBy('attendance_date')
                ->get();

            $totalDays = $attendanceRecords->count();

            $presentDays = 0;

            foreach ($attendanceRecords as $attendance) {

                $presentStudents =
                    $attendance->present_student_uuids ?? [];

                if (
                    in_array(
                        $student->uuid,
                        $presentStudents
                    )
                ) {
                    $presentDays++;
                }
            }

            $absentDays =
                $totalDays - $presentDays;

            $percentage = $totalDays > 0
                ? round(
                    ($presentDays / $totalDays) * 100,
                    2
                )
                : 0;

            $attendanceSummary = [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'percentage' => $percentage,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Summary
        |--------------------------------------------------------------------------
        */

        $paymentSummary = [
            'total_fees' => $student->total_fees,
            'paid_fees' => $student->paid_fees,
            'balance_fees' => $student->balance_fees,
            'next_due_date' => $student->next_due_date,
        ];

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'data' => [
                'student' => $studentData,

                'batch' => $batchData,

                'today_class' => $todayClass,

                'attendance' => $attendanceSummary,

                'payments' => $paymentSummary,
            ],
        ]);
    }
}