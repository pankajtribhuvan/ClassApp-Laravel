<?php

namespace App\Http\Controllers\Api\TeacherApp;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Teacher Dashboard
     */
    public function index(Request $request)
    {
        $teacher = $request->user();

        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Get Teacher's Active Batches
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | batches.teacher_id stores the Teacher model's numeric id.
        |
        */
        $batches = Batch::where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->with([
                'students' => function ($query) {
                    $query->where('students.status', 'active');
                }
            ])
            ->orderBy('start_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Today's Day
        |--------------------------------------------------------------------------
        |
        | Admin stores days as:
        | Mon,Tue,Wed,Thu,Fri,Sat,Sun
        |
        */
        $todayDay = $today->format('D');

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalBatches = $batches->count();

        $totalStudents = $batches
            ->flatMap(function ($batch) {
                return $batch->students;
            })
            ->unique('uuid')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Today's Batches
        |--------------------------------------------------------------------------
        */

        $todayBatches = $batches->filter(function ($batch) use ($todayDay) {

            if (!$batch->days) {
                return false;
            }

            $days = array_map(
                'trim',
                explode(',', $batch->days)
            );

            return in_array($todayDay, $days);
        })->values();

        /*
        |--------------------------------------------------------------------------
        | Today's Batch Data
        |--------------------------------------------------------------------------
        */

        $todayBatchData = $todayBatches->map(function ($batch) use ($today) {

            $attendanceSubmitted = Attendance::where(
                'batch_uuid',
                $batch->uuid
            )
                ->whereDate(
                    'attendance_date',
                    $today
                )
                ->exists();

            return [
                'uuid' => $batch->uuid,

                'batch_name' => $batch->batch_name,

                'course_name' => $batch->course_name,

                'start_time' => $batch->start_time,

                'end_time' => $batch->end_time,

                'days' => $batch->days,

                'room_no' => $batch->room_no,

                'student_count' => $batch->students->count(),

                'attendance_status' => $attendanceSubmitted
                    ? 'completed'
                    : 'pending',
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'data' => [

                'teacher' => [
                    'uuid' => $teacher->uuid,
                    'full_name' => $teacher->full_name,
                    'photo' => $teacher->photo,
                ],

                'summary' => [
                    'total_batches' => $totalBatches,
                    'total_students' => $totalStudents,
                    'today_batches' => $todayBatchData->count(),
                ],

                'today' => [
                    'date' => $today->format('Y-m-d'),
                    'day' => $todayDay,
                ],

                'today_batches' => $todayBatchData,
            ],
        ]);
    }
}